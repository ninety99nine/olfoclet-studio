<?php

namespace App\Jobs\AutoBilling;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use App\Models\pricingPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\AutoBilling\AutoBillSubscriber;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutoBillingByPricingPlan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *  Project instance.
     *
     *  @var \App\Models\Project
     */
    protected $project;

    /**
     *  Pricing plan
     *
     *  @var \App\Models\PricingPlan
     */
    protected $pricingPlan;

    /**
     *  Auto billing job batches count
     *
     *  @var int
     */
    protected $autoBillingJobBatchesCount;

    /**
     *  The unique ID of the job.
     *
     *  Sometimes, you may want to ensure that only one instance of a specific job is on
     *  the queue at any point in time. You may do so by implementing the ShouldBeUnique
     *  interface on your job class. So the current job will not be dispatched if another
     *  instance of the job is already on the queue and has not finished processing.
     *
     *  Refer: https://laravel.com/docs/8.x/queues#unique-jobs
     *
     *  @return string
     */
    public function uniqueId()
    {
        return $this->pricingPlan->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param App\Models\PricingPlan $pricingPlan
     * @param int $autoBillingJobBatchesCount
     *
     * @return void
     */
    public function __construct(Project $project, PricingPlan $pricingPlan, int $autoBillingJobBatchesCount)
    {
        $this->pricingPlan = $pricingPlan;
        $this->project = $project->withoutRelations();

        /**
         *  It appears that the eager loaded withCount('autoBillingJobBatches')
         *  is not accessible using $pricingPlan->auto_billing_job_batches_count
         *  within the handle() method. Therefore we will set this as its own parameter.
         */
        $this->autoBillingJobBatchesCount = $autoBillingJobBatchesCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            //  If this project has the billing credentials then continue
            if($this->project->hasBillingCredentials()) {

                /**
                 *  Query the subscribers that are ready for billing.
                 *
                 *  Limit the loaded subscriber to the subscriber id and msisdn to consume less memory.
                 *
                 *  The final query output is as follows:
                 *
                 *  [
                 *      {
                 *          "id": 1,
                 *          "msisdn": "26772000001"
                 *      },
                 *      ...
                 *  ]
                 */
                $subscribers = $this->project->subscribers()->whereHas('autoBillingSchedules', function (Builder $query) {

                    /**
                     *  We need to limit the auto billing schedules based on the next_attempt_date.
                     *  The next_attempt_date helps us to determine the acceptable date and time
                     *  to qualify auto billing. We can autobill on the following conditions:
                     *
                     *  1) If the next_attempt_date has been reached, that is, auto bill on the same
                     *     date and time or sometime after.
                     *
                     *  2) If the next_attempt_date is not some time more than 48 hours from the
                     *     desired date and time.
                     *
                     *  Remember that the first billing attempt is expected to occur as soon as the
                     *  subscription ends provided that we do not have any downtime or delays while
                     *  processing other jobs. The second attempt will occur 1 hour later after
                     *  the first, and the third attempt will occur 1 hour later after the
                     *  second attempt.
                     *
                     *  Each pricing plan can have 1 or more "maximum auto billing attempts" e.g 365.
                     *  The following is a timeline of how the auto billing occurs based on the maximum
                     *  auto billing attempts set on the pricing plan (max_auto_billing_attempts).
                     *
                     *  ---------------------------------------------------------------------------------
                     *  Theoretical Timeline for (1) Attempt:
                     *
                     *  1) Subscription ends                    2024-01-01 08:00:00
                     *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                     *  ----------------------------------------------------------------------------------------
                     *  Theoretical Timeline for (2) Attempts:
                     *
                     *  1) Subscription ends                    2024-01-01 08:00:00
                     *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                     *  3) Attempt #2 (1 hour later)            2024-01-01 09:00:00 (Assuming attempt #1 failed)
                     *  ----------------------------------------------------------------------------------------
                     *  Theoretical Timeline for (3) Attempts:
                     *
                     *  1) Subscription ends                    2024-01-01 08:00:00
                     *  2) Attempt #1 (occurs immediately)      2024-01-01 08:00:00
                     *  3) Attempt #2 (1 hour later)            2024-01-01 09:00:00 (Assuming attempt #1 failed)
                     *  4) Attempt #3 (1 hour later)            2024-01-01 10:00:00 (Assuming attempt #2 failed)
                     *  ---------------------------------------------------------------------------------
                     *  This approach continues until the maximum auto billing attempts on the
                     *  pricing plan (max_auto_billing_attempts) are exhausted.
                     *  ---------------------------------------------------------------------------------
                     *
                     *  In theory, the first attempt date is the same as the subscription end date,
                     *  the second attempt date is then 1 hour after the first attempt date,
                     *  the third attempt date is then 1 hour after the second attempt date.
                     *  This is how the theoretical timeline might look like:
                     *
                     *  Subscription end|          |          |
                     *                  |          |          |
                     *                  |< 1 hour >|< 1 hour >|
                     *                  |          |          |
                     *         Attempt 1| Attempt 2| Attempt 3|
                     *
                     *  In practice, we might not always be able to run any of these attempts
                     *  exactly on their specified next_attempt_date due to various reasons e.g
                     *  system downtime, scheduled maintenance or delays due to other jobs
                     *  being processed etc. For simplicity, we will only take the first
                     *  attempt delays into consideration for demonstration. This is how
                     *  the practical timeline might look like:
                     *
                     *  Subscription end|                  |          |          |
                     *                  |    < x hours >   |          |          |
                     *                  | unexpected delays|< 1 hour >|< 1 hour >|
                     *                  |                  |          |          |
                     *                  |         Attempt 1| Attempt 2| Attempt 3|
                     */
                    $query->where('auto_billing_enabled', '1')
                        ->where('next_attempt_date', '<=', Carbon::now())
                        ->where('pricing_plan_id', $this->pricingPlan->id);

                })->select('subscribers.id', 'subscribers.msisdn');

                //  If we have subscribers to auto bill
                if( $subscribers->count() > 0 ) {

                    $jobs = [];

                    //  Only query 1000 subscribers at a time (This helps us save memory)
                    $subscribers->chunk(1000, function ($chunked_subscribers) use (&$jobs) {

                        //  Foreach subscriber we retrieved from the query
                        foreach ($chunked_subscribers as $subscriber) {

                            //  Create a job to bill the subscriber
                            $jobs[] = new AutoBillSubscriber($this->project, $subscriber, $this->pricingPlan);

                        }

                    });

                    //  If we have jobs to process
                    if( count($jobs) > 0 ) {

                        /**
                         *  We cannot reference "$this->pricingPlan" within the Bus::batch() closures.
                         *  Therefore we must create an pricingPlan variable that we can pass as a
                         *  parameter of the various closures.
                         */
                        $pricingPlan = $this->pricingPlan;

                        //  Set the sprint name
                        $sprintName = 'Sprint #' . ($this->autoBillingJobBatchesCount + 1);

                        //  Create the batch to send
                        $batch = Bus::batch($jobs
                            )->then(function (Batch $batch) use ($pricingPlan) {

                            })->catch(function (Batch $batch, Throwable $e) use ($pricingPlan) {

                            })->finally(function (Batch $batch) use ($pricingPlan) {

                            })->name($sprintName)->allowFailures()->dispatch();

                        //  Create a new auto billing job batch record
                        DB::table('auto_billing_job_batches')->insert([
                            'pricing_plan_id' => $pricingPlan->id,
                            'job_batch_id' => $batch->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                    }

                }

            }

        } catch (\Throwable $th) {

            Log::error('AutoBillingByPricingPlan Job Failed: '. $th->getMessage());

        }
    }

}
