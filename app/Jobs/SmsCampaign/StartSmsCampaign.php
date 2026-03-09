<?php

namespace App\Jobs\SmsCampaign;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Bus\Batch;
use App\Enums\MessageType;
use App\Models\SmsCampaign;
use Illuminate\Bus\Queueable;
use App\Helpers\PhpCodeExecuter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use App\Traits\Models\SmsCampaignTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartSmsCampaign implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SmsCampaignTrait;

    protected $project;
    protected $smsCampaign;
    protected $smsCampaignBatchJobsCount;

    public function uniqueId()
    {
        return $this->smsCampaign->id;
    }

    public function __construct(Project $project, SmsCampaign $smsCampaign, int $smsCampaignBatchJobsCount)
    {
        $this->onQueue('high');

        // Strip relationships to prevent heavy Redis/DB queue payloads
        $this->project = $project->withoutRelations();
        $this->smsCampaign = $smsCampaign->withoutRelations();

        $this->smsCampaignBatchJobsCount = $smsCampaignBatchJobsCount;
    }

    public function handle()
    {
        try {
            // Exit early if credentials are missing or campaign cannot start
            if (!$this->project->hasSmsCredentials() || !$this->smsCampaign->canStartSmsCampaign()) {
                return;
            }

            /*******************************
             * GET THE SENDABLE MESSAGES  *
             ******************************/
            $messages = [];

            if (is_array($this->smsCampaign->message_ids) && !empty($this->smsCampaign->message_ids)) {

                if ($this->smsCampaign->message_to_send == 'Specific Message') {
                    $messages = $this->getMessageDescendantOrSelf($this->project, $this->smsCampaign->message_ids);
                } elseif ($this->smsCampaign->message_to_send == 'Any Message') {
                    foreach ($this->smsCampaign->message_ids as $message_ids) {
                        $messages = array_merge(
                            $messages,
                            $this->getMessageDescendantOrSelf($this->project, $message_ids, $messages)
                        );
                    }
                }
            }

            $messages = collect($messages);

            // Exit early if there are no messages to send
            if ($messages->isEmpty()) {
                return;
            }

            /**********************************
             * BUILD THE SUBSCRIBER QUERY     *
             **********************************/
            $subscribersQuery = $this->project->subscribers()
                ->select('subscribers.id', 'subscribers.msisdn', 'subscribers.metadata')
                ->whereDoesntHave('smsCampaigns', function (Builder $query) {
                    $query->where('sms_campaigns.id', $this->smsCampaign->id)
                          ->where('next_message_date', '>', Carbon::now());
                })
                ->with(['latestSubscription', 'messages' => function ($query) {
                    return $query->where('is_successful', '1')
                        ->where('type', MessageType::Content->value)
                        ->select(
                            'subscriber_messages.message_id',
                            'subscriber_messages.subscriber_id',
                            DB::raw('COUNT(*) as sent_sms_count'),
                            DB::raw('MAX(created_at) as last_sent_at')
                        )
                        ->groupBy('subscriber_messages.message_id', 'subscriber_messages.subscriber_id')
                        ->orderBy('sent_sms_count', 'ASC')
                        ->orderBy('last_sent_at', 'ASC');
                }])
                ->orderBy('subscribers.id');

            /**********************************
             * APPLY PRICING PLAN FILTERS     *
             **********************************/
            $pricingPlans = [];
            $hasListedPricingPlans = !empty($this->smsCampaign->pricing_plan_ids);
            $pricingPlanIds = [];

            if ($hasListedPricingPlans) {
                foreach ($this->smsCampaign->pricing_plan_ids as $pricing_plan_ids) {
                    $pricingPlans = array_merge(
                        $pricingPlans,
                        $this->getPricingPlanDescendantOrSelf($this->project, $pricing_plan_ids, $pricingPlans)
                    );
                }

                $pricingPlanIds = collect($pricingPlans)->pluck('id')->toArray();

                // Safety Check: If plans were required but none were found in the DB, abort to prevent sending to EVERYONE.
                if (empty($pricingPlanIds)) {
                    return;
                }

                $subscribersQuery->hasActiveNonCancelledSubscription($pricingPlanIds);
            }

            /**********************************
             * DISPATCH BATCHES EFFICIENTLY   *
             **********************************/

            // Extract variables for the closure to prevent binding the whole $this instance
            $project = $this->project;
            $smsCampaign = $this->smsCampaign;
            $batchCount = $this->smsCampaignBatchJobsCount;
            $batchIndex = 0;

            /**
             * Use chunk() (not chunkById) because chunkById can abort when the query
             * uses whereDoesntHave or scope-based subqueries (e.g. hasActiveNonCancelledSubscription).
             */
            $subscribersQuery->chunk(1000, function ($chunked_subscribers) use ($project, $smsCampaign, $messages, &$batchIndex, $batchCount) {

                $chunkJobs = [];

                foreach ($chunked_subscribers as $subscriber) {

                    // CRITICAL BUG FIX: Reset $message to null on every iteration!
                    $messageToSend = null;

                    // Check validation code
                    $code = $smsCampaign->validation_code;
                    if (!empty($code)) {
                        $canSendMessage = PhpCodeExecuter::runCode($code, [
                            'subscriber'         => $subscriber,
                            'latestSubscription' => $subscriber->latestSubscription
                        ]);

                        if (!$canSendMessage) {
                            continue;
                        }
                    }

                    $sentMessageIds = collect($subscriber->messages)->pluck('message_id');
                    $hasReceivedEveryMessage = $sentMessageIds->count() >= $messages->count();

                    if ($hasReceivedEveryMessage == false) {

                        $messageToSend = $messages->whereNotIn('id', $sentMessageIds->all())->first();

                    } elseif ($hasReceivedEveryMessage == true && $smsCampaign->can_repeat_messages == true) {

                        foreach ($sentMessageIds as $sentMessageId) {
                            foreach ($messages as $currMessage) {
                                if ($sentMessageId == $currMessage->id) {
                                    $messageToSend = $currMessage;
                                    break 2;
                                }
                            }
                        }

                    }

                    if ($messageToSend) {
                        $chunkJobs[] = new SendSmsCampaignMessage($project, $subscriber, $messageToSend, $smsCampaign);
                    }
                }

                // If this chunk yielded jobs, dispatch them as a batch
                if (!empty($chunkJobs)) {

                    $sprintName = 'Sprint #' . ($batchCount + $batchIndex + 1);

                    $batch = Bus::batch($chunkJobs)
                        ->name($sprintName)
                        ->allowFailures()
                        ->dispatch();

                    DB::table('sms_campaign_job_batches')->insert([
                        'sms_campaign_id' => $smsCampaign->id,
                        'job_batch_id'    => $batch->id,
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now()
                    ]);

                    $batchIndex++;
                }

                // Explicitly free memory for the next chunk
                unset($chunkJobs);

            });

        } catch (Throwable $th) {

            Log::error('StartSmsCampaign Job Failed: ' . $th->getMessage());

            // Re-throw the exception so the queue worker registers the failure
            throw $th;
        }
    }
}
