<?php

namespace App\Jobs\SmsCampaign;

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

            Log::info('StartSmsCampaign');
            Log::info('canStartSmsCampaign: '.$this->smsCampaign->canStartSmsCampaign());

            //  If this sms campaign can be started
            if( $this->smsCampaign->canStartSmsCampaign() ) {

                /*******************************
                 *  GET THE SENDABLE MESSAGES  *
                 ******************************/

                $messages = [];

                //  If we have the message ids
                if( is_array($this->smsCampaign->message_ids) ) {

                    /*****************************
                     *  GET SENDABLE MESSAGES    *
                     ****************************/

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

                Log::info('$messages->count(): '.$messages->count());

                //  If this sms campaign has messages to send
                if( $messages->count() > 0 ) {

                    Log::info('stage 1');

                    /**
                     *  Query the subscribers that are ready to receive the next sms message
                     */
                    $subscribers = $this->project->subscribers()->whereDoesntHave('smsCampaigns', function (Builder $query) {

                        $query->where('sms_campaigns.id', $this->smsCampaign->id)
                              ->where('next_message_date', '>', \Carbon\Carbon::now());

                    })->with(['messages' => function($query) {

                        /**
                         *  1) Limit the loaded message to the message id and sent sms count to consume less memory.
                         *  2) Order by the sent sms count "ASC" so that the messages that have the lowest sent sms
                         *     count appear at the top of this relationsip stack. If the
                         *  3) For messages that have the same sent sms count, we can then order by the messages
                         *     created_at "ASC" so that the messages that were created earlier appear first
                         *     before messages that were created later after them.
                         *
                         *  The eager loaded "messages" will look something like this for every subscriber:
                         *
                         *  "messages": [
                         *      {
                         *          "id": 3,
                         *          "pivot": {
                         *              "message_id": 2,
                         *              "subscriber_id": 1,
                         *              "sent_sms_count": 199
                         *          }
                         *      },
                         *      ...
                         *  ]
                         *
                         *  Notice that the "pivot" will always include the "message_id" and "subscriber_id"
                         *  by default even if the withPivot() only specifies the "sent_sms_count".
                         */
                        return $query->select('messages.id')->withPivot('sent_sms_count')->orderBy('sent_sms_count')->orderBy('messages.created_at');

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
                     *                  "id": 3,
                     *                  "pivot": {
                     *                      "message_id": 2,
                     *                      "subscriber_id": 1,
                     *                      "sent_sms_count": 199
                     *                  }
                     *              },
                     *              {
                     *                  "id": 3,
                     *                  "pivot": {
                     *                      "message_id": 1,
                     *                      "subscriber_id": 1,
                     *                      "sent_sms_count": 200
                     *                  }
                     *              },
                     *              ...
                     *          ]
                     *      },
                     *      ...
                     *  ]
                     */
                    }])->select('subscribers.id', 'subscribers.msisdn');

                    Log::info('stage 2');
                    //  If this sms campaign requires the subscribers to have an active subscription
                    if( count($subscriptionPlanIds = $this->smsCampaign->subscriptionPlans()->pluck('subscription_plan_id')) ) {

                        /**
                         *  Limit the subscribers based on the active non cancelled subscriptions
                         *  matching the specified subscription plans.
                         */
                        $subscribers = $subscribers->hasActiveNonCancelledSubscription($subscriptionPlanIds);

                    }
                    Log::info('stage 3');
                    Log::info('$subscribers->count(): '.$subscribers->count());

                    //  If this sms campaign has subscribers to send messages
                    if( $subscribers->count() > 0 ) {

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
                                 *  subscriber with the message "id" and "sent_sms_count".
                                 */
                                $sentMessageIds = collect($subscriber->messages)->pluck('id');

                                /**
                                 *  Check if the subscriber has seen every message.
                                 *
                                 *  It's possible for a subscriber to have more messages that are sent than messages
                                 *  that have been created since this subscriber could be receiving repeating
                                 *  messages. This happens if the subscriber has seen every message before
                                 *  and we allow re-sending of the same message. Taking this into account,
                                 *  let's count the messages while making sure to avoid counting
                                 *  duplicates. We do this by using the unique() method which
                                 *  clears out any duplicate entry
                                 */
                                $sentMessageIds = $sentMessageIds->unique();

                                $hasSeenEveryMessage = $sentMessageIds->count() == $messages->count();

                                //  If we have not seen every message
                                if( $hasSeenEveryMessage == false ) {

                                    /**
                                     *  Get the first message that the subscriber has not received
                                     */
                                    $message = $messages->whereNotIn('id', $sentMessageIds->all())->first();

                                //  If we have seen every message and we can repeat
                                }elseif($hasSeenEveryMessage == true && $canRepeatMessage == true) {

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
                                }elseif($hasSeenEveryMessage == true && $canRepeatMessage == false) {

                                    //  Continue to the next iteration
                                    continue;

                                }

                                //  If we have a message to send
                                if( $message ) {

                                    //  If this project has the sms credentials then continue
                                    if( $this->project->hasSmsCredentials() ) {

                                        //  Create a job to send this message
                                        $jobs[] = new SendSmsCampaignMessage($this->project, $subscriber, $message, $this->smsCampaign);

                                    }

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

            Log::info('Error: '. $th->getMessage());

            // Send error report here
            //  Log::channel('slack')->error('StartSmsCampaign Job Failed: '. $th->getMessage());

        }
    }

}
