<?php

namespace App\Jobs\AutoBilling;

use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use App\Models\PricingPlan;
use App\Services\BillingService;
use App\Models\BillingTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\CreatedUsingAutoBilling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\SubscriptionRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class AutoBillSubscriber implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    public $subscriber;
    public $pricingPlan;
    public $billingAttemptAt;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 3600;  // 3600 seconds = 1 hour

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project, Subscriber $subscriber, PricingPlan $pricingPlan)
    {
        $this->onQueue('billing');

        // Ensure we don't serialize any eager-loaded relationships into the queue payload
        $this->project = $project->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
        $this->pricingPlan = $pricingPlan->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->pricingPlan->id . '-' . $this->subscriber->id;
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            /**
             * Bill the subscriber using airtime.
             *
             * @var BillingTransaction $billingTransaction
             */
            $billingTransaction = BillingService::billUsingAirtime(
                $this->project,
                $this->pricingPlan,
                $this->subscriber,
                CreatedUsingAutoBilling::YES
            );

            // Set the billing attempt datetime
            $this->billingAttemptAt = $billingTransaction->created_at;

            // If the subscriber was billed successfully
            if ($billingTransaction->is_successful) {

                // Create a new subscription
                (new SubscriptionRepository($this->project))->createProjectSubscription(
                    $this->subscriber,
                    $this->pricingPlan,
                    CreatedUsingAutoBilling::YES,
                    $billingTransaction
                );

            } else {

                // Update the billing schedule for business failures (e.g., insufficient funds)
                $this->updateBillingScheduleOnUnsuccessfulAttempt();

            }

            // Only unset local var; do NOT unset $this->project, $this->subscriber, $this->pricingPlan
            // because Laravel calls uniqueId() again after handle() returns to release the unique lock.
            unset($billingTransaction);

        } catch (\Throwable $th) {

            Log::error('AutoBillSubscriber Job Failed: ' . $th->getMessage());

            /**
             * CRITICAL FIX:
             * Returning 'false' does NOT trigger a queue retry in Laravel. It marks the job as
             * successful and discards it. To actually trigger your 3 tries / 1-hour delay
             * (for network/API failures), you MUST throw the exception so the worker knows it crashed.
             */
            throw $th;
        }
    }

    /**
     * Update the billing schedule on an unsuccessful attempt
     *
     * @return void
     */
    private function updateBillingScheduleOnUnsuccessfulAttempt()
    {
        // Query ONLY the columns we need to save memory
        $existingSchedule = DB::table('auto_billing_schedules')
            ->select('attempt', 'overall_attempts', 'overall_failed_attempts')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'pricing_plan_id' => $this->pricingPlan->id
            ])->first();

        // If the schedule is missing for some reason, abort safely
        if (!$existingSchedule) {
            return;
        }

        $attempt = $existingSchedule->attempt + 1;

        // Determine if auto-billing is still enabled based on max attempts
        $maxAttempts = $this->pricingPlan->max_auto_billing_attempts;
        $autoBillingEnabled = ($maxAttempts == 0 || $attempt < $maxAttempts);

        if ($autoBillingEnabled) {
            $nextAttemptDate = now()->addDay();
        } else {
            $attempt = 0;
            $nextAttemptDate = null;
        }

        // Update the existing auto billing schedule
        DB::table('auto_billing_schedules')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'pricing_plan_id' => $this->pricingPlan->id
            ])->update([
                'attempt'                 => $attempt,
                'overall_attempts'        => $existingSchedule->overall_attempts + 1,
                'overall_failed_attempts' => $existingSchedule->overall_failed_attempts + 1,
                'updated_at'              => $this->billingAttemptAt,
                'next_attempt_date'       => $nextAttemptDate,
                'auto_billing_enabled'    => $autoBillingEnabled,
            ]);

        // If auto billing has been disabled, notify the user
        if (!$autoBillingEnabled && !empty($this->pricingPlan->auto_billing_disabled_sms_message)) {
            SendAutoBillingDisabledSms::dispatch($this->project, $this->subscriber, $this->pricingPlan);
        }
    }
}
