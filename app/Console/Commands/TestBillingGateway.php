<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\BillingService;
use Illuminate\Console\Command;

class TestBillingGateway extends Command
{
    /**
     * Test the Orange AAS billing gateway using ORANGE_BILLING_ENDPOINT from .env
     * and a project's auto_billing_client_id / auto_billing_client_secret.
     *
     * @var string
     */
    protected $signature = 'billing:test-gateway
                            {--project=1 : The project ID to use for credentials}';

    /**
     * @var string
     */
    protected $description = 'Test the billing API gateway using .env endpoint and project auto-billing credentials';

    /**
     * @return int
     */
    public function handle()
    {
        $projectId = (int) $this->option('project');

        $project = Project::find($projectId);
        if (!$project) {
            $this->error("Project with ID {$projectId} not found.");
            return self::FAILURE;
        }

        $clientId = $project->settings['auto_billing_client_id'] ?? null;
        $clientSecret = $project->settings['auto_billing_client_secret'] ?? null;

        if (empty($clientId) || empty($clientSecret)) {
            $this->error("Project \"{$project->name}\" (ID={$projectId}) has no auto-billing credentials in settings.");
            return self::FAILURE;
        }

        $endpoint = config('app.ORANGE_BILLING_ENDPOINT');
        $this->info("Endpoint (from .env): {$endpoint}");
        $this->info("Project: {$project->name} (ID={$projectId})");
        $this->info("Client ID: " . substr($clientId, 0, 8) . '...');
        $this->newLine();

        // 1. Request token
        $this->info('Requesting access token...');
        $tokenResult = BillingService::requestNewAirtimeBillingAccessToken($clientId, $clientSecret);

        if (!$tokenResult['status']) {
            $this->error('Token request failed.');
            $this->line(json_encode($tokenResult['body'] ?? $tokenResult, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        $this->info('Token obtained successfully.');
        $accessToken = $tokenResult['body']['access_token'];

        // 2. Test product inventory (Orange test number)
        $testMsisdn = '26775263791';
        $this->newLine();
        $this->info("Calling product inventory for test number {$testMsisdn}...");
        $inventoryResult = BillingService::requestAirtimeBillingProductInventory($testMsisdn, $accessToken);

        if (!$inventoryResult['status']) {
            $this->error('Product inventory request failed.');
            $this->line(json_encode($inventoryResult['body'] ?? $inventoryResult, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        $this->info('Product inventory OK.');
        $body = $inventoryResult['body'] ?? [];
        if (is_array($body) && isset($body[0])) {
            $first = $body[0];
            $this->line('  Status: ' . ($first['status'] ?? 'n/a'));
            $this->line('  Rating type: ' . ($first['ratingType'] ?? 'n/a'));
        }

        $this->newLine();
        $this->info('Billing gateway test passed.');

        return self::SUCCESS;
    }
}
