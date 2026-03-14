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
    public $token; // The unique lock token
    public $billingAttemptAt;

    public $tries = 3;
    public $retryAfter = 3600;

    public function __construct(Project $project, Subscriber $subscriber, PricingPlan $pricingPlan, string $token)
    {
        $this->onQueue('billing');
        $this->project = $project->withoutRelations();
        $this->subscriber = $subscriber->withoutRelations();
        $this->pricingPlan = $pricingPlan->withoutRelations();
        $this->token = $token;
    }

    public function uniqueId()
    {
        return $this->pricingPlan->id . '-' . $this->subscriber->id;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle()
    {
        // 1. TOKEN VALIDATION (Anti-Zombie Check)
        // If the token in DB doesn't match ours, this job is stale.
        $currentLock = DB::table('auto_billing_schedules')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'pricing_plan_id' => $this->pricingPlan->id
            ])->select('processing_token', 'is_processing')->first();

        if (!$currentLock || $currentLock->processing_token !== $this->token) {
            Log::warning("Zombie Job Terminated: Subscriber {$this->subscriber->id} was reclaimed by a newer process or Janitor.");
            return;
        }

        try {
            Log::info("AutoBillSubscriber Started: Subscriber {$this->subscriber->id}");

            // 2. DYNAMIC IDEMPOTENCY CHECK
            $lookBackMinutes = match ($this->pricingPlan->frequency) {
                'Minutes' => (int) $this->pricingPlan->duration,
                'Hours'   => (int) $this->pricingPlan->duration * 60,
                default   => 1440,
            };

            $checkTime = now()->subMinutes(max(5, $lookBackMinutes - 2));

            $existingSuccessfulTransaction = BillingTransaction::where('subscriber_id', $this->subscriber->id)
                ->where('pricing_plan_id', $this->pricingPlan->id)
                ->where('is_successful', true)
                ->where('created_at', '>', $checkTime)
                ->latest()
                ->first();

            if ($existingSuccessfulTransaction) {
                if ($existingSuccessfulTransaction->subscription_id !== null) {
                    Log::info("Idempotency: Subscriber {$this->subscriber->id} already fulfilled. Skipping.");
                    return;
                }
                Log::warning("Idempotency: Fulfilling partial success for Subscriber {$this->subscriber->id}.");
                $billingTransaction = $existingSuccessfulTransaction;
            } else {
                $billingTransaction = BillingService::billUsingAirtime(
                    $this->project,
                    $this->pricingPlan,
                    $this->subscriber,
                    CreatedUsingAutoBilling::YES
                );
            }

            $this->billingAttemptAt = $billingTransaction->created_at;

            // 3. FULFILLMENT
            if ($billingTransaction->is_successful) {
                (new SubscriptionRepository($this->project))->createProjectSubscription(
                    $this->subscriber,
                    $this->pricingPlan,
                    CreatedUsingAutoBilling::YES,
                    $billingTransaction
                );
            } else {
                $this->updateBillingScheduleOnUnsuccessfulAttempt();
            }

        } catch (\Throwable $th) {
            // BillingService never throws; API/gateway failures return is_successful=false and are handled above.
            // Only code/DB failures (e.g. SubscriptionRepository, DB) reach here; rethrow so they are reported.
            throw $th;
        } finally {
            // 4. ATOMIC RELEASE
            // Only unlock if our token is still the one in the seat.
            DB::table('auto_billing_schedules')
                ->where([
                    'subscriber_id' => $this->subscriber->id,
                    'pricing_plan_id' => $this->pricingPlan->id,
                    'processing_token' => $this->token
                ])->update([
                    'is_processing' => false,
                    'processing_token' => null,
                    'updated_at' => now()
                ]);
        }
    }

    private function updateBillingScheduleOnUnsuccessfulAttempt()
    {
        $existingSchedule = DB::table('auto_billing_schedules')
            ->select('attempt', 'overall_attempts', 'overall_failed_attempts')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'pricing_plan_id' => $this->pricingPlan->id
            ])->first();

        if (!$existingSchedule) return;

        $attempt = $existingSchedule->attempt + 1;
        $maxAttempts = $this->pricingPlan->max_auto_billing_attempts;
        $autoBillingEnabled = ($maxAttempts == 0 || $attempt < $maxAttempts);

        $nextAttemptDate = $autoBillingEnabled ? now()->addDay() : null;
        if (!$autoBillingEnabled) $attempt = 0;

        DB::table('auto_billing_schedules')
            ->where([
                'subscriber_id' => $this->subscriber->id,
                'pricing_plan_id' => $this->pricingPlan->id
            ])->update([
                'attempt'                 => $attempt,
                'overall_attempts'        => $existingSchedule->overall_attempts + 1,
                'overall_failed_attempts' => $existingSchedule->overall_failed_attempts + 1,
                'updated_at'              => $this->billingAttemptAt ?? now(),
                'next_attempt_date'       => $nextAttemptDate,
                'auto_billing_enabled'    => $autoBillingEnabled,
            ]);

        if (!$autoBillingEnabled && !empty($this->pricingPlan->auto_billing_disabled_sms_message)) {
            SendAutoBillingDisabledSms::dispatch($this->project, $this->subscriber, $this->pricingPlan);
        }
    }
}
