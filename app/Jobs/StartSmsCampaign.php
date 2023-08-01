<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StartSmsCampaign implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * The campaign instance.
     *
     * @var \App\Models\Campaign
     */
    protected $campaign;

    /**
     * The total batch jobs for the specified campaign instance.
     *
     * @var int
     */
    protected $campaignBatchJobsCount;

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
        return $this->campaign->id;
    }

    /**
     * Create a new job instance.
     *
     * @param App\Models\Project $project
     * @param App\Models\Campaign $campaign
     * @param int $campaignBatchJobsCount
     * @return void
     */
    public function __construct(Project $project, Campaign $campaign, int $campaignBatchJobsCount)
    {
        $this->project = $project;
        $this->campaign = $campaign;

        /**
         *  It appears that the eager loaded withCount('campaignBatchJobs')
         *  is not accessible using $campaign->campaign_batch_jobs_count
         *  within the handle() method. Therefore we will set this as
         *  its own parameter.
         */
        $this->campaignBatchJobsCount = $campaignBatchJobsCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            Log::channel('slack')->info('Event Handle Campaign # '.$this->campaign->id);
            Log::channel('slack')->info('canStartSmsCampaign() # '.$this->campaign->id.': '.$this->campaign->canStartSmsCampaign());

            //  If this campaign can be started
            if( $this->campaign->canStartSmsCampaign() ) {

                /*******************************
                 *  GET THE SENDABLE MESSAGES  *
                 ******************************/

                $messages = [];

                //  If we have the message ids
                if( is_array($this->campaign->message_ids) ) {

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

                    if( $this->campaign->message_to_send == 'Specific Message' ) {

                        //  Get the message ids, this is a single array of ids
                        $message_ids = $this->campaign->message_ids;

                        //  Capture the qualified messages
                        $messages = $this->getQualifiedSendablesFromIdArray($message_ids);

                    //  Get the message ids, this is a multiple array of ids
                    }elseif( $this->campaign->message_to_send == 'Any Message' ) {

                        //  Extract the sendable messages
                        foreach($this->campaign->message_ids as $message_ids) {

                            //  Capture the qualified messages
                            $messages = array_merge(
                                $messages,
                                $this->getQualifiedSendablesFromIdArray($message_ids, $messages)
                            );

                        }
                    }

                }

                //  Convert to collection
                $messages = collect($messages);

                //  If this campaign has messages to send
                if( $messages->count() > 0 ) {

                    /***************************************************
                     *  GET THE SUBSCRIBERS READY FOR THE NEXT MESSAGE *
                     **************************************************/

                    // Get the ids of subscribers to this campaign (Those who have received content before but are not ready for the next round of content)
                    $subscriberIdsNotReadyForNextSms = $this->campaign->subscribersNotReadyForNextSms()->pluck('subscribers.id');

                    //  Get the project subscribers (Those who have received or have not received content before)
                    $subscribers = $this->project->subscribers()->with(['messages' => function($query) {

                        //  Limit the loaded message to the message id and sent sms count to consume less memory
                        return $query->select('messages.id', 'sent_sms_count')->orderBy('sent_sms_count');

                    }])->select('subscribers.id', 'subscribers.msisdn');

                    //  If this campaign has subscribers not ready for the next sms message
                    if( count($subscriberIdsNotReadyForNextSms) ) {

                        //  Exclude the subscribers that are not ready to receive the next sms message
                        $subscribers = $subscribers->excludeIds($subscriberIdsNotReadyForNextSms);

                    }

                    //  If this campaign requires the subscribers to have an active subscription
                    if( count($this->campaign->subcription_plan_ids ?? []) ) {

                        //  Limit to subscribers with the given subscription plans
                        $subscribers = $subscribers->hasActiveSubscription($this->campaign->subcription_plan_ids);

                    }

                    Log::channel('slack')->info('Found '.$subscribers->count().($subscribers->count() == 1 ? ' subscriber' : ' subscribers'));

                    //  If this campaign has subscribers to send messages
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
                                     *  Get the first message.
                                     *
                                     *  This message must be a message that the subscriber has not received
                                     */
                                    $message = $messages->whereNotIn('id', $sentMessageIds->all())->first();

                                //  If we have seen every message
                                }elseif($hasSeenEveryMessage == true && $canRepeatMessage == true) {

                                    /**
                                     *  Get the first message with the lowest views
                                     *
                                     *  This message has the least views since we eager loaded the subscriber
                                     *  messages relationship with the additional orderBy('sent_sms_count')
                                     *  method.
                                     */
                                    $message = $messages->where('id', $sentMessageIds->first())->first();

                                //  If we don't have a new message to send
                                }else{

                                    //  Stop execution of futher code
                                    return;

                                }

                                Log::channel('slack')->info('send Subscriber # '.$subscriber->id.' a message');

                                //  If we have a message to send
                                if( $message ) {

                                    $senderName = $this->project->settings['sms_sender_name'];
                                    $senderNumber = $this->project->settings['sms_sender_number'];
                                    $clientCredentials = $this->project->settings['sms_client_credentials'];

                                    //  If this project has the sms credentials then continue
                                    if( $this->project->hasSmsCredentials() ) {

                                        Log::channel('slack')->info('Dispatch SendCampaignSms() on Campaign # '.$subscriber->id);

                                        //  Create a job to send this message
                                        $jobs[] = new SendCampaignSms($subscriber, $message, $this->campaign, $senderName, $senderNumber, $clientCredentials);

                                    }

                                }

                            }

                        });

                        //  If this campaign has jobs to process
                        if( count($jobs) > 0 ) {

                            /**
                             *  We cannot reference "$this->campaign" within the Bus::batch() closures.
                             *  Therefore we must create a campaign variable that we can pass as a
                             *  parameter of the various closures.
                             */
                            $campaign = $this->campaign;

                            //  Set the campaign name
                            $campaignName = 'Campaign #' . ($this->campaignBatchJobsCount + 1);

                            //  Create the batch to send
                            $batch = Bus::batch($jobs
                                )->then(function (Batch $batch) use ($campaign) {

                                })->catch(function (Batch $batch, Throwable $e) use ($campaign) {

                                })->finally(function (Batch $batch) use ($campaign) {

                                })->name($campaignName)->allowFailures()->dispatch();

                            //  Create a new campaign job batch record
                            DB::table('campaign_job_batches')->insert([
                                'campaign_id' => $campaign->id,
                                'job_batch_id' => $batch->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                        }

                    }
                }else{

                    Log::channel('slack')->info('No messages for campaign # '.$this->campaign->id);

                }
            }

        } catch (\Throwable $th) {

            // Send error report here
            Log::channel('slack')->error('StartSmsCampaign Job Failed: '. $th->getMessage());

            // The job failed
            return false;

        }
    }

    /**
     *  This function helps us to capture the qualified messages.
     *  These are the messages that we want to send to the
     *  subscribers
     *
     *  @param array<int> $ids
     *  @param array<Message> $selectedMessages
     *  @return array<Message>
     */
    public function getQualifiedSendablesFromIdArray($ids, $selectedMessages = []) {

        //  Get the last id in the cascade of message ids
        $id = collect($ids)->last();

        /**
         *  Check if the item already exists in the list
         *  of previously qualified sendable messages.
         *
         *  On the Frontend, we know that its possible to select a topic that has
         *  descendant topics. After selecting this topic we could also select one
         *  of the descendant topics. This would mean we could query a topic that
         *  has already been captured as a descendant of another topic. We need
         *  to make sure this does not happen otherwise we are performing
         *  unnecessary database queries.
         */
        $exists = collect($selectedMessages)->contains(function($selectedMessage) use ($id) {
            return $selectedMessage->id == $id;
        });

        //  If the message does not yet exist
        if( $exists == false ) {

            //  Get the project message descendants that do not have nested children (Must be leaf messages i.e at the tips of the tree)
            $result = $this->project->messages()->whereDescendantOrSelf($id)->doesntHave('children')->get();

            /**
             *  Laravel all() vs toArray()
             *
             *  Using all() - It will return an array of Eloquent models without converting them to arrays.
             *  Using toArray() - If it's a collection of Eloquent models, the models will also be converted to arrays with toArray()
             *
             *  Reference: https://stackoverflow.com/questions/43287573/difference-between-all-and-toarray-in-laravel-5
             */
            return $result->all();

        }

        //  Return nothing
        return [];

    }

}
