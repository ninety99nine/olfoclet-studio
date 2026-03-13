<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Pivots\SubscriberMessage;
use App\Services\SmsService;
use Illuminate\Console\Command;

class TestSmsDeliveryStatus extends Command
{
    /**
     * Manually test the Orange SMS delivery status endpoint using the
     * same FAC gateway and client credentials as the application, without
     * modifying any database records.
     *
     * Example:
     *
     * php artisan sms:test-delivery-status \
     *   --project=1 \
     *   --endpoint="/smsmessaging/v1/outbound/tel:+26777479083/requests/req69b40c911b43237f893ae943/deliveryInfos"
     *
     * or with the full URL copied from the dashboard:
     *
     * php artisan sms:test-delivery-status \
     *   --project=1 \
     *   --endpoint="https://api.orange.com/smsmessaging/v1/outbound/tel:+26777479083/requests/req69b40c911b43237f893ae943/deliveryInfos"
     *
     * @var string
     */
    protected $signature = 'sms:test-delivery-status
                            {project : The project ID to use for SMS client credentials}
                            {--endpoint= : (Optional) Override delivery status endpoint (relative path or full URL)}';

    /**
     * @var string
     */
    protected $description = 'Test Orange SMS delivery-status lookup via FAC gateway using project SMS credentials';

    public function handle(): int
    {
        $projectId = (int) $this->argument('project');
        $endpointOverride = (string) $this->option('endpoint');

        $project = Project::find($projectId);
        if (! $project) {
            $this->error("Project with ID {$projectId} not found.");
            return self::FAILURE;
        }

        $clientCredentials = $project->settings['sms_client_credentials'] ?? null;
        if (! $clientCredentials) {
            $this->error("Project \"{$project->name}\" (ID={$projectId}) has no sms_client_credentials in settings.");
            return self::FAILURE;
        }

        $baseEndpoint = rtrim((string) config('app.ORANGE_SMS_ENDPOINT'), '/');

        $this->info("Project: {$project->name} (ID={$projectId})");
        $this->info('Using ORANGE_SMS_ENDPOINT: ' . $baseEndpoint);
        $this->newLine();

        // Resolve which delivery_status_endpoint we are going to test.
        if ($endpointOverride !== '') {
            $this->info('Using overridden endpoint from --endpoint option:');
            $endpoint = $endpointOverride;
            $subscriberMessage = new SubscriberMessage();
            $subscriberMessage->delivery_status_endpoint = $endpoint;
        } else {
            $this->info('No --endpoint provided. Selecting the newest sent subscriber message for this project with a delivery_status_endpoint...');

            $existing = SubscriberMessage::where('project_id', $projectId)
                ->whereNotNull('delivery_status_endpoint')
                ->where('is_successful', true)
                ->orderByDesc('sent_at')
                ->orderByDesc('id')
                ->first();

            if (! $existing) {
                $this->error('No subscriber_messages found for this project with a delivery_status_endpoint.');
                return self::FAILURE;
            }

            $endpoint = (string) $existing->delivery_status_endpoint;
            $this->info('Using SubscriberMessage ID: ' . $existing->id);
            $this->info('Stored delivery_status_endpoint: ' . $endpoint);
            $this->newLine();

            // Build an in-memory clone so we never modify the original DB record.
            $subscriberMessage = $existing->replicate();
            $subscriberMessage->delivery_status_endpoint = $endpoint;
        }

        $this->info('Requesting SMS access token...');
        $tokenResult = SmsService::requestSmsAccessToken($clientCredentials);

        if (! $tokenResult['status']) {
            $this->error('Token request failed.');
            $this->line(json_encode($tokenResult['body'] ?? $tokenResult, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        $accessToken = $tokenResult['body']['access_token'] ?? null;
        if (! $accessToken) {
            $this->error('Token response missing access_token.');
            $this->line(json_encode($tokenResult['body'] ?? $tokenResult, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        $this->info('Token obtained successfully. Calling delivery status endpoint via FAC gateway...');
        $deliveryResult = SmsService::requestSmsDeliveryStatus($subscriberMessage, $accessToken);

        $this->newLine();
        $this->info('Raw response:');
        $this->line(json_encode($deliveryResult, JSON_PRETTY_PRINT));

        if (! $deliveryResult['status']) {
            $this->newLine();
            $this->error('Delivery status request FAILED (this is what the jobs would record as "Delivery Status Request Failed").');
            return self::FAILURE;
        }

        $status = $deliveryResult['body']['deliveryInfo'][0]['deliveryStatus'] ?? 'n/a';

        $this->newLine();
        $this->info('Delivery status request SUCCEEDED.');
        $this->line('  deliveryStatus: ' . $status);

        return self::SUCCESS;
    }
}

