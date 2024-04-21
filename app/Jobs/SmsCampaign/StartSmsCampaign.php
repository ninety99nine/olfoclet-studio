<?php

namespace App\Jobs\SmsCampaign;

use App\Enums\MessageType;
use Throwable;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Bus\Batch;
use App\Models\SmsCampaign;
use Illuminate\Bus\Queueable;
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

    /**
     * The project instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * The sms campaign instance.
     *
     * @var \App\Models\SmsCampaign
     */
    protected $smsCampaign;

    /**
     * The total batch jobs for the specified sms campaign instance.
     *
     * @var int
     */
    protected $smsCampaignBatchJobsCount;

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
        return $this->smsCampaign->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param App\Models\SmsCampaign $smsCampaign
     * @param int $smsCampaignBatchJobsCount
     * @return void
     */
    public function __construct(Project $project, SmsCampaign $smsCampaign, int $smsCampaignBatchJobsCount)
    {
        $this->smsCampaign = $smsCampaign;
        $this->project = $project->withoutRelations();

        /**
         *  It appears that the eager loaded withCount('smsCampaignBatchJobs')
         *  is not accessible using $smsCampaign->sms_campaign_batch_jobs_count
         *  within the handle() method. Therefore we will set this as
         *  its own parameter.
         */
        $this->smsCampaignBatchJobsCount = $smsCampaignBatchJobsCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            //  If this project has the sms credentials and this sms campaign can be started
            if( $this->project->hasSmsCredentials() && $this->smsCampaign->canStartSmsCampaign() ) {

                /*******************************
                 *  GET THE SENDABLE MESSAGES  *
                 ******************************/

                $messages = [];

                //  If we have the message ids
                if( is_array($this->smsCampaign->message_ids) && !empty($this->smsCampaign->message_ids) ) {

                    /**
                     *  (1) Specific Message
                     *  ---------------------
                     *
                     *  If the message_to_send is "Specific Message" then the
                     *  message_ids will contain one array with a list of ids
                     *  from the parent to the child message we want to send
                     *  e.g
                     *
                     *  [ 1, 10, 20, 30 ]
                     *
                     *  In the case above we want the message with id of 30
                     *  which is a descendant of message 1, 10, and 20
                     *
                     *  (1) Any Message
                     *  ---------------
                     *
                     *  If the message_to_send is "Any Message" then the
                     *  message_ids will contain one array with a list
                     *  of arrays of ids from the parent to the child
                     *  message we want to send e.g
                     *
                     *  [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
                     *
                     *  In the case above we want the message with id of 30
                     *  and message with id 35 which are both descendants
                     *  of message 1, 10, and 20
                     */
                    if( $this->smsCampaign->message_to_send == 'Specific Message' ) {

                        //  Get the message ids, this is a single array of ids
                        $message_ids = $this->smsCampaign->message_ids;

                        //  Capture the message descendant or self messages
                        $messages = $this->getMessageDescendantOrSelf($this->project, $message_ids);

                    //  Get the message ids, this is a multiple array of ids
                    }elseif( $this->smsCampaign->message_to_send == 'Any Message' ) {

                        //  Extract the sendable messages
                        foreach($this->smsCampaign->message_ids as $message_ids) {

                            //  Capture the message descendant or self instances
                            $messages = array_merge(
                                $messages,
                                $this->getMessageDescendantOrSelf($this->project, $message_ids, $messages)
                            );

                        }
                    }

                }

                //  Convert to collection
                $messages = collect($messages);

                //  If this sms campaign has messages to send
                if( $messages->count() > 0 ) {

                    /**
                     *  Query the subscribers that are ready to receive the next sms message
                     */
                    $subscribers = $this->project->subscribers()->whereDoesntHave('smsCampaigns', function (Builder $query) {

                        $query->where('sms_campaigns.id', $this->smsCampaign->id)
                                ->where('next_message_date', '>', \Carbon\Carbon::now());

                    })->with(['messages' => function($query) {

                        /**
                         *  1) Limit the loaded message to the message id, subscriber id, sent sms count and the
                         *     last sent at datetime to consume less memory.
                         *  2) Order by the sent sms count "ASC" so that the messages that have the lowest sent sms
                         *     count appear at the top of this relationsip stack.
                         *  3) For messages that have the same sent sms count, we can then order by the messages
                         *     last sent at "ASC" so that the messages that were sent a longer time ago appear
                         *     first before messages that were sent must more recently.
                         *
                         *  The eager loaded "messages" will look something like this for every subscriber:
                         *
                         *  "messages": [
                         *      {
                         *          "message_id": 3,
                         *          "subscriber_id": 1,
                         *          "sent_sms_count": 1,
                         *          "last_sent_at": "2024-01-01 08:00:00"
                         *      },
                         *      {
                         *          "message_id": 2,
                         *          "subscriber_id": 1,
                         *          "sent_sms_count": 2,
                         *          "last_sent_at": "2024-01-01 08:00:05"
                         *      },
                         *      ...
                         *  ]
                         */
                        return $query->where('is_successful', '1')
                                    ->where('type', MessageType::Content->value)
                                    ->select(
                                        'subscriber_messages.message_id',
                                        'subscriber_messages.subscriber_id',
                                        DB::raw('COUNT(*) as sent_sms_count'),
                                        DB::raw('MAX(created_at) as last_sent_at')
                                    )->groupBy('subscriber_messages.message_id')
                                    ->orderBy('sent_sms_count', 'ASC')
                                    ->orderBy('last_sent_at', 'ASC');

                    /**
                     *  1) Limit the loaded subscriber to the subscriber id and msisdn to consume less memory.
                     *
                     *  The final query output is as follows:
                     *
                     *  [
                     *      {
                     *          "id": 1,
                     *          "msisdn": "26772000001",
                     *          "messages": [
                     *              {
                     *                  "message_id": 3,
                     *                  "subscriber_id": 1,
                     *                  "sent_sms_count": 1,
                     *                  "last_sent_at": "2024-01-01 08:00:00"
                     *              },
                     *              {
                     *                  "message_id": 2,
                     *                  "subscriber_id": 1,
                     *                  "sent_sms_count": 2,
                     *                  "last_sent_at": "2024-01-01 08:00:05"
                     *              },
                     *              ...
                     *          ]
                     *      },
                     *      ...
                     *  ]
                     */
                    }])->select('subscribers.id', 'subscribers.msisdn');

                    /***************************************
                     *  GET QUALIFYING SUBSCRIPTION PLANS  *
                     **************************************/

                    $subscriptionPlans = [];
                    $hasListedSubscriptionPlans = !empty($this->smsCampaign->subscription_plan_ids);

                    //  If we have the subscription plan ids
                    if($hasListedSubscriptionPlans) {

                        /**
                         *  The subscription_plan_ids will contain one array with a list
                         *  of arrays of ids from the parent to the child
                         *  subscription plan that is qualified e.g
                         *
                         *  [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
                         *
                         *  In the case above we want the subscription plan with id of 30
                         *  and subscription plan with id 35 which are both descendants
                         *  of subscription plan 1, 10, and 20
                         */

                        //  Extract the subscription plans
                        foreach($this->smsCampaign->subscription_plan_ids as $subscription_plan_ids) {

                            //  Capture the subscription plan descendant or self instances
                            $subscriptionPlans = array_merge(
                                $subscriptionPlans,
                                $this->getSubscriptionPlanDescendantOrSelf($this->project, $subscription_plan_ids, $subscriptionPlans)
                            );

                        }

                    }

                    //  If this sms campaign requires the subscribers to have an active subscription
                    if( count($subscriptionPlanIds = collect($subscriptionPlans)->pluck('id')->toArray()) ) {

                        /**
                         *  Limit the subscribers based on the active non cancelled subscriptions
                         *  matching the specified subscription plans.
                         */
                        $subscribers = $subscribers->hasActiveNonCancelledSubscription($subscriptionPlanIds);

                    }

                    /**
                     *  If this sms campaign has subscribers to send messages.
                     *
                     *  Send the subscriber a message if:
                     *
                     *  1) We have subscribers to send and we don't have any subscription plans listed on this sms campaign
                     *  2) We have subscribers to send and we do have subscription plans listed on this sms campaign
                     *     and those listed subscription plans have been found and actually used to qualify these
                     *     subscribers. We want to avoid a situation where a listed subscription plan has been
                     *     has been since deleted from the project. The "$subscribers->count() > 0" may run
                     *     while the subscribers have not been qualified using the listed subscription plan,
                     *     thereby qualifying everyone.
                     */
                    if( ($subscribers->count() > 0) && (!$hasListedSubscriptionPlans || ($hasListedSubscriptionPlans && count($subscriptionPlanIds) > 0))) {

                        $jobs = [];

                        //  Only query 1000 subscribers at a time (This helps us save memory)
                        $subscribers->chunk(1000, function ($chunked_subscribers) use ($messages, &$jobs) {

                            //  Foreach subscriber we retrieved from the query
                            foreach ($chunked_subscribers as $subscriber) {

                                //  Determine if we can repeat messages
                                $canRepeatMessage = true;

                                /**
                                 *  Get the ids of the messages that have already been sent.
                                 *  Remember that these messages where eager loaded on each
                                 *  subscriber.
                                 */
                                $sentMessageIds = collect($subscriber->messages)->pluck('message_id');

                                /**
                                 *  Check if the subscriber has received every message.
                                 *
                                 *  Just incase the subscriber has received every message, and some messages have
                                 *  been deleted such that $sentMessageIds->count() != $messages->count(),
                                 *  then we can use ">=" instead of just "==" to qualify odd situations
                                 *  where the subscriber received messages are more than the project
                                 *  messages created.
                                 */
                                $hasReceivedEveryMessage = $sentMessageIds->count() >= $messages->count();

                                //  If we have not seen every message
                                if( $hasReceivedEveryMessage == false ) {

                                    /**
                                     *  Get the first message that the subscriber has not received
                                     */
                                    $message = $messages->whereNotIn('id', $sentMessageIds->all())->first();

                                //  If we have seen every message and we can repeat
                                }elseif($hasReceivedEveryMessage == true && $this->smsCampaign->can_repeat_messages == true) {

                                    /**
                                     *  Get the first message with the lowest "sent_sms_count"
                                     *
                                     *  This message has the least views since we eager loaded the subscriber
                                     *  messages relationship with the additional orderBy('sent_sms_count')
                                     *  method.
                                     */
                                    foreach($sentMessageIds as $sentMessageId) {

                                        foreach($messages as $currMessage) {

                                            if($sentMessageId == $currMessage->id) {

                                                //  Set the first message with the lowest "sent_sms_count"
                                                $message = $currMessage;

                                                //  Stop these two foreach iterations since we found a message
                                                break(2);

                                            }

                                        }

                                    }

                                //  If we have seen every message and but we cannot repeat
                                }elseif($hasReceivedEveryMessage == true && $this->smsCampaign->can_repeat_messages == false) {

                                    //  Continue to the next iteration
                                    continue;

                                }

                                //  If we have a message to send
                                if( $message ) {

                                    //  Create a job to send this message
                                    $jobs[] = new SendSmsCampaignMessage($this->project, $subscriber, $message, $this->smsCampaign);

                                }else{

                                    //  Continue to the next iteration
                                    continue;

                                }

                            }

                        });

                        //  If this sms campaign has jobs to process
                        if( count($jobs) > 0 ) {

                            /**
                             *  We cannot reference "$this->smsCampaign" within the Bus::batch() closures.
                             *  Therefore we must create an smsCampaign variable that we can pass as a
                             *  parameter of the various closures.
                             */
                            $smsCampaign = $this->smsCampaign;

                            //  Set the sprint name
                            $sprintName = 'Sprint #' . ($this->smsCampaignBatchJobsCount + 1);

                            //  Create the batch to send
                            $batch = Bus::batch($jobs
                                )->then(function (Batch $batch) use ($smsCampaign) {

                                })->catch(function (Batch $batch, Throwable $e) use ($smsCampaign) {

                                })->finally(function (Batch $batch) use ($smsCampaign) {

                                })->name($sprintName)->allowFailures()->dispatch();

                            //  Create a new campaign job batch record
                            DB::table('sms_campaign_job_batches')->insert([
                                'sms_campaign_id' => $smsCampaign->id,
                                'job_batch_id' => $batch->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                        }

                    }

                }

            }

        } catch (\Throwable $th) {

            Log::error('StartSmsCampaign Job Failed: '. $th->getMessage());
            Log::error($th->getTraceAsString());

        }
    }

}
