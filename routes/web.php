<?php

use App\Http\Controllers\AutoBillingSubscriptionPlanController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SmsCampaignController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Jobs\SmsCampaign\StartSmsCampaign;
use App\Http\Controllers\TopicController;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\UserController;
use App\Models\Message;
use App\Models\Pivots\SubscriberMessage;
use App\Models\Pivots\SubscriptionPlanAutoBillingReminder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\SmsCampaign;
use App\Models\Subscriber;
use App\Models\Project;
use App\Services\SmsService;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login', 301);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::prefix('/projects')->group(function () {

        //  Projects
        Route::get('/', [ProjectController::class, 'showProjects'])->name('show.projects');
        Route::post('/', [ProjectController::class, 'createProject'])->name('create.project');

        Route::prefix('{project}')->group(function () {

            //  Project
            Route::middleware(['project.permission:Manage project settings'])->group(function () {
                Route::get('/', [ProjectController::class, 'showProject'])
                        ->withoutMiddleware(['project.permission:Manage project settings'])
                        ->middleware(['project.permission:View project settings'])
                        ->name('show.project');
                Route::put('/', [ProjectController::class, 'updateProject'])->name('update.project');
                Route::delete('/', [ProjectController::class, 'deleteProject'])->name('delete.project');
            });

            //  Project About
            Route::prefix('about')->group(function () {
                Route::get('/', [ProjectController::class, 'showProjectAbout'])->name('show.project.about');
            });

            //  Users
            Route::prefix('show-users')->group(function () {
                Route::get('/', [UserController::class, 'showUsers'])->middleware(['project.permission:View users'])->name('show.users');
                Route::post('/', [UserController::class, 'createUser'])->middleware(['project.permission:Manage users'])->name('create.user');

                Route::prefix('{user}')->middleware(['project.permission:Manage users'])->group(function () {
                    //  Route::get('/', [UserController::class, 'showUser'])->name('show.user');
                    Route::put('/', [UserController::class, 'updateUser'])->name('update.user');
                    Route::delete('/', [UserController::class, 'deleteUser'])->name('delete.user');
                });
            });

            //  Messages
            Route::prefix('messages')->group(function () {
                Route::get('/', [MessageController::class, 'showMessages'])->middleware(['project.permission:View messages'])->name('show.messages');
                Route::post('/', [MessageController::class, 'createMessage'])->middleware(['project.permission:Manage messages'])->name('create.message');

                Route::prefix('{message}')->middleware(['project.permission:Manage messages'])->group(function () {
                    Route::get('/', [MessageController::class, 'showMessage'])
                        ->withoutMiddleware(['project.permission:Manage messages'])
                        ->middleware(['project.permission:View messages'])
                        ->name('show.message');
                    Route::put('/', [MessageController::class, 'updateMessage'])->name('update.message');
                    Route::delete('/', [MessageController::class, 'deleteMessage'])->name('delete.message');
                });
            });

            //  Sms Campaigns
            Route::prefix('sms-campaigns')->group(function () {
                Route::get('/', [SmsCampaignController::class, 'showSmsCampaigns'])->middleware(['project.permission:View sms campaigns'])->name('show.sms.campaigns');
                Route::post('/', [SmsCampaignController::class, 'createSmsCampaign'])->middleware(['project.permission:Manage sms campaigns'])->name('create.sms.campaign');

                Route::prefix('{sms_campaign}')->middleware(['project.permission:Manage sms campaigns'])->group(function () {
                    Route::get('/', [SmsCampaignController::class, 'showSmsCampaign'])->name('show.sms.campaign');
                    Route::put('/', [SmsCampaignController::class, 'updateSmsCampaign'])->name('update.sms.campaign');
                    Route::delete('/', [SmsCampaignController::class, 'deleteSmsCampaign'])->name('delete.sms.campaign');
                    Route::get('/job-batches', [SmsCampaignController::class, 'showSmsCampaignJobBatches'])
                            ->withoutMiddleware(['project.permission:Manage sms campaigns'])
                            ->middleware(['project.permission:View sms campaigns'])
                            ->name('show.sms.campaign.job.batches');
                });
            });

            //  Subscribers
            Route::prefix('subscribers')->group(function () {
                Route::get('/', [SubscriberController::class, 'showSubscribers'])->middleware(['project.permission:View subscribers'])->name('show.subscribers');
                Route::post('/', [SubscriberController::class, 'createSubscriber'])->middleware(['project.permission:Manage subscribers'])->name('create.subscriber');

                Route::prefix('{subscriber}')->middleware(['project.permission:Manage subscribers'])->group(function () {
                    Route::put('/', [SubscriberController::class, 'updateSubscriber'])->name('update.subscriber');
                    Route::delete('/', [SubscriberController::class, 'deleteSubscriber'])->name('delete.subscriber');
                });
            });

            //  Subscriptions
            Route::prefix('subscriptions')->group(function () {
                Route::get('/', [SubscriptionController::class, 'showSubscriptions'])->middleware(['project.permission:View subscriptions'])->name('show.subscriptions');
                Route::post('/', [SubscriptionController::class, 'createSubscription'])->middleware(['project.permission:Manage subscriptions'])->name('create.subscription');

                Route::prefix('{subscription}')->middleware(['project.permission:Manage subscriptions'])->group(function () {
                    Route::put('/', [SubscriptionController::class, 'updateSubscription'])->name('update.subscription');
                    Route::delete('/', [SubscriptionController::class, 'deleteSubscription'])->name('delete.subscription');
                    Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('cancel.subscription');
                    Route::post('/uncancel', [SubscriptionController::class, 'uncancelSubscription'])->name('uncancel.subscription');
                });
            });

            //  Subscription Plans
            Route::prefix('subscription-plans')->group(function () {
                Route::get('/', [SubscriptionPlanController::class, 'showSubscriptionPlans'])->middleware(['project.permission:View subscription plans'])->name('show.subscription.plans');
                Route::post('/', [SubscriptionPlanController::class, 'createSubscriptionPlan'])->middleware(['project.permission:Manage subscription plans'])->name('create.subscription.plan');

                Route::prefix('{subscription_plan}')->middleware(['project.permission:Manage subscription plans'])->group(function () {
                    Route::get('/', [SubscriptionPlanController::class, 'showSubscriptionPlan'])
                        ->withoutMiddleware(['project.permission:Manage subscription plans'])
                        ->middleware(['project.permission:View subscription plans'])
                        ->name('show.subscription.plan');
                    Route::put('/', [SubscriptionPlanController::class, 'updateSubscriptionPlan'])->name('update.subscription.plan');
                    Route::delete('/', [SubscriptionPlanController::class, 'deleteSubscriptionPlan'])->name('delete.subscription.plan');
                });
            });

            //  Auto Billing Subscription Plans
            Route::prefix('auto-billing/subscription-plans')->group(function () {
                Route::get('/', [AutoBillingSubscriptionPlanController::class, 'showAutoBillingSubscriptionPlans'])->middleware(['project.permission:View auto billing subscription plans'])->name('show.auto.billing.subscription.plans');

                Route::prefix('{subscription_plan}')->middleware(['project.permission:Manage auto billing subscription plans'])->group(function () {
                    Route::get('/job-batches', [AutoBillingSubscriptionPlanController::class, 'showAutoBillingSubscriptionPlanJobBatches'])
                            ->withoutMiddleware(['project.permission:Manage auto billing subscription plans'])
                            ->middleware(['project.permission:View auto billing subscription plans'])
                            ->name('show.auto.billing.subscription.plan.job.batches');
                });
            });

            //  Topics
            Route::prefix('topics')->group(function () {
                Route::get('/', [TopicController::class, 'showTopics'])->middleware(['project.permission:View topics'])->name('show.topics');
                Route::post('/', [TopicController::class, 'createTopic'])->middleware(['project.permission:Manage topics'])->name('create.topic');

                Route::prefix('{topic}')->middleware(['project.permission:Manage topics'])->group(function () {
                    Route::get('/', [TopicController::class, 'showTopic'])
                        ->withoutMiddleware(['project.permission:Manage topics'])
                        ->middleware(['project.permission:View topics'])
                        ->name('show.topic');
                    Route::put('/', [TopicController::class, 'updateTopic'])->name('update.topic');
                    Route::delete('/', [TopicController::class, 'deleteTopic'])->name('delete.topic');
                });
            });

        });

    });

});





Route::get('/testing2', function() {

    $project = Project::find(1);
    $message = Message::find(3);
    $subscriber = Subscriber::find(1);
    $subscriberMessage = SubscriberMessage::find(2);

    //  Get projects that can send messages
    $subscriberMessages = SubscriberMessage::messageWaiting()->with(['project' => function($query) {

        //  Get sms campaigns that can send messages with their total batch jobs
        return $query->select('id', 'settings');

    }])->select('id', 'project_id')->oldest();

    //  Only query 1000 subscriber messages at a time (This helps us save memory)
    return $subscriberMessages->chunk(1000, function ($chunkedSubscriberMessages) {

        //  Foreach chunked subscriber messages
        foreach($chunkedSubscriberMessages as $subscriberMessage) {

            //  Create a job to update the sms delivery status
            return dd($subscriberMessage->toArray());

        }

    });

    return 'done';

    //
    //return SmsService::sendSms($project, $subscriber, $message);
    return SmsService::updateSmsDeliveryStatus($project, $subscriberMessage);


    return Project::find(1)->subscribers()
        ->hasActiveNonCancelledSubscription(9, 1)
        ->select('subscribers.id', 'subscribers.msisdn')->get();

    /***************************************************
     *  GET THE SUBSCRIBERS READY FOR THE NEXT MESSAGE *
     **************************************************/

    $project = Project::find(1);
    $smsCampaign = SmsCampaign::find(3);
    $subscriber = Subscriber::find(1);
    $subscriptionPlanIds = [1, 2, 3];
    $subscribers = $project->subscribers()->whereDoesntHave('smsCampaigns', function (Builder $query) use ($smsCampaign) {

        $query->where('sms_campaigns.id', $smsCampaign->id)
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
         *  {
         *      ...
         *      "messages":[
         *          {
         *              "id":1,"pivot":{
         *                  "message_id": 2,
         *                  "subscriber_id": 1,
         *                  "sent_sms_count": 199
         *              }
         *          }
         *          {
         *              "id":2,"pivot":{
         *                  "message_id": 1,
         *                  "subscriber_id": 1,
         *                  "sent_sms_count": 200
         *              }
         *          }
         *          ...
         *      ]
         *  }
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
     *          "messages":[
     *              {
     *                  "id":3,
     *                  "pivot":{
     *                      "message_id": 2,
     *                      "subscriber_id": 1,
     *                      "sent_sms_count": 199
     *                  }
     *              },
     *              {
     *                  "id":3,
     *                  "pivot":{
     *                  "message_id": 1,
     *                  "subscriber_id": 1,
     *                  "sent_sms_count": 200
     *              }
     *          }
     *          ...
     *      ]
     *
     *
     */
    }])->select('subscribers.id', 'subscribers.msisdn');

    return $subscriber->subscriptions()->active()->get();

    //  If this sms campaign requires the subscribers to have an active subscription
    if( count($subscriptionPlanIds = $smsCampaign->subscriptionPlans()->pluck('subscription_plan_id')) ) {
        /**
         *  Limit the subscribers based on the active subscriptions
         *  matching the specified subscription plans.
         */
        $subscribers = $subscribers->hasActiveNonCancelledSubscription($subscriptionPlanIds);

    }

    return $subscribers->get();

});

Route::get('testing', function() {

    $subscriptionPlanAutoBillingReminders = SubscriptionPlanAutoBillingReminder::whereHas('project', function($query) {

        /**
         *  Must have a project that can auto bill.
         */
        return $query->canAutoBill();

    })->whereHas('subscriptionPlan', function($query) {

        /**
         *  Must have an active, non-folder subscription plan that can also auto bill.
         */
        return $query->active()->nonFolder()->canAutoBill();

    })->with(['project', 'autoBillingReminder', 'subscriptionPlan' => function($query) {

        $query->withCount('autoBillingReminderJobBatches');

    }])->get();

    /**
     *  Order the subscriptionPlanAutoBillingReminders by the autoBillingReminder hours
     *  so that those with more hours appear at the top of the stack while those with
     *  fewer hours appear at the bottom of the stack.
     */
    $subscriptionPlanAutoBillingReminders = $subscriptionPlanAutoBillingReminders->sortByDesc(function ($subscriptionPlanAutoBillingReminder) {
        return $subscriptionPlanAutoBillingReminder->autoBillingReminder->hours;
    })->all();

    // Foreach project
    foreach ($subscriptionPlanAutoBillingReminders as $subscriptionPlanAutoBillingReminder) {

        $project = $subscriptionPlanAutoBillingReminder->project;
        $subscriptionPlan = $subscriptionPlanAutoBillingReminder->subscriptionPlan;
        $autoBillingReminder = $subscriptionPlanAutoBillingReminder->autoBillingReminder;
        $autoBillingReminderJobBatchesCount = $subscriptionPlan->auto_billing_reminder_job_batches_count;

        dd($project, $subscriptionPlan, $autoBillingReminder, $autoBillingReminderJobBatchesCount);

    }

});

Route::get('/upload', function() {

    $projectId = 1;

    //  English Parent topic
    $englishLanguage = \App\Models\Topic::create([
        'title' => 'English',
        'project_id' => $projectId
    ]);

    //  Setswana Parent topic
    $setswanaLanguage = \App\Models\Topic::create([
        'title' => 'Setswana',
        'project_id' => $projectId
    ]);

    /************************
     *  SELF HELP TOPICS    *
     ***********************/


    $template = [];

    //  English Parent topic
    $englishRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $englishLanguage->prependNode($englishRootSelfHelpTopic);

    //  Setswana Parent topic
    $setswanaRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $setswanaLanguage->prependNode($setswanaRootSelfHelpTopic);




    //  Get file
    $selfHelpTopicsFile = file(storage_path('app').'/selfHelpTopics.csv');
    $selfHelpTopics = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $selfHelpTopicsFile), 1);

    //  Clean fields
    foreach($selfHelpTopics as $key => $selfHelpTopic){
        $selfHelpTopics[$key] = json_decode(str_replace('\u202f', '', $selfHelpTopic));

        foreach($selfHelpTopics[$key] as $y => $selfHelpTopicString){
            $selfHelpTopics[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $selfHelpTopicString));
        }
    }

    //  Create topics
    foreach($selfHelpTopics as $key => $selfHelpTopic) {

        $template['title'] = trim($selfHelpTopic[0]);
        $template['content'] = trim($selfHelpTopic[1]);
        $template['project_id'] = $projectId;

        $selfHelpTopicModel = \App\Models\Topic::create($template);

        //  Set the English parent
        if( trim($selfHelpTopic[2]) == 'English' ){

            $englishRootSelfHelpTopic->prependNode($selfHelpTopicModel);

        //  Set the Setswana parent
        }else{

            $setswanaRootSelfHelpTopic->prependNode($selfHelpTopicModel);

        }
    }

    /***************************
     *  GET EDUCATED TOPICS    *
     **************************/

    $template = [];

    //  English Parent topic
    $englishRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $englishLanguage->prependNode($englishRootGetEducatedTopic);

    //  Setswana Parent topic
    $setswanaRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $setswanaLanguage->prependNode($setswanaRootGetEducatedTopic);







    $getEducatedTopicsFile = file(storage_path('app').'/getEducatedTopics.csv');
    $getEducatedTopics = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $getEducatedTopicsFile), 1);

    foreach($getEducatedTopics as $key => $getEducatedTopic){
        $getEducatedTopics[$key] = json_decode(str_replace('\u202f', '', $getEducatedTopic));

        foreach($getEducatedTopics[$key] as $y => $getEducatedTopicString){
            $getEducatedTopics[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $getEducatedTopicString));
        }
    }

    $parentTopicTitles = [];
    $getEducatedParentTopicModels = [];

    foreach($getEducatedTopics as $key => $getEducatedTopic) {

        $parentTopicTitle = trim($getEducatedTopic[2]);

        $template['title'] = $parentTopicTitle;
        $template['project_id'] = $projectId;

        if(!in_array($parentTopicTitle, $parentTopicTitles)) {

            array_push($parentTopicTitles, $parentTopicTitle);

            $getEducatedParentTopicModel = \App\Models\Topic::create($template);
            $getEducatedParentTopicModels[$key] = $getEducatedParentTopicModel;

            //  Set the English parent
            if( trim($getEducatedTopic[3]) == 'English' ) {

                $englishRootGetEducatedTopic->prependNode($getEducatedParentTopicModel);

            //  Set the Setswana parent
            }else{

                $setswanaRootGetEducatedTopic->prependNode($getEducatedParentTopicModel);

            }

        }
    }

    /*******************************
     *  GET EDUCATED SUB TOPICS    *
     ******************************/

    $template = [];

    foreach($getEducatedTopics as $key => $getEducatedTopic){

        $subTopicTitle = trim($getEducatedTopic[0]);
        $subTopicContent = trim($getEducatedTopic[1]);
        $parentTopicTitle = trim($getEducatedTopic[2]);

        $template['title'] = $subTopicTitle;
        $template['content'] = $subTopicContent;
        $template['project_id'] = $projectId;

        /**
         *  Convert the occurance of "?." into "#?" e.g
         *
         *  Take the following:
         *
         *  $template['content'] = "1. Weakened immune system. 2. High blood pressure. 3. Depression"
         *
         *  And convert into:
         *
         *  $template['content'] = "(1) Weakened immune system. (2) High blood pressure. (3) Depression"
         */
        $template['content'] = preg_replace('/(\d+)\.\s/', '#$1 ', $template['content']);

        $getEducatedSubTopicModel = \App\Models\Topic::create($template);

        //  Set the parent Topic
        $getEducatedParentTopicModel = collect($getEducatedParentTopicModels)->filter(function($getEducatedParentTopicModel) use ($parentTopicTitle) {
            return strtolower(trim($getEducatedParentTopicModel->title)) == strtolower($parentTopicTitle);
        })->first();

        if( $getEducatedParentTopicModel ) {

            $getEducatedParentTopicModel->prependNode($getEducatedSubTopicModel);

        }
    }

    /***************************
     *  DAILY QUOTES           *
     **************************/

    //  English Parent topic
    $englishLanguage = \App\Models\Message::create([
        'content' => 'English',
        'project_id' => $projectId
    ]);

    //  Setswana Parent topic
    $setswanaLanguage = \App\Models\Message::create([
        'content' => 'Setswana',
        'project_id' => $projectId
    ]);


    //  Get file
    $dailyQuotesFile = file(storage_path('app').'/dailyQuotes.csv');
    $dailyQuotes = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $dailyQuotesFile), 1);

    //  Clean fields
    foreach($dailyQuotes as $key => $dailyQuote){
        $dailyQuotes[$key] = json_decode(str_replace('\u202f', '', $dailyQuote));

        foreach($dailyQuotes[$key] as $y => $dailyQuoteString){
            $dailyQuotes[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $dailyQuoteString));
        }
    }

    //  Create Messages
    foreach($dailyQuotes as $key => $dailyQuote){

        $template['content'] = trim($dailyQuote[1]);
        $template['project_id'] = $projectId;

        $dailyQuoteModel = \App\Models\Message::create($template);
        $dailyQuotes[$key] = $dailyQuoteModel;

        //  Set the English parent
        if( trim($dailyQuote[5]) == '1' ) {

            $englishLanguage->prependNode($dailyQuoteModel);

        //  Set the Setswana parent
        }else {

            $setswanaLanguage->prependNode($dailyQuoteModel);

        }
    }

    //  Set the created_at and updated_at so that each topic is 1 second apart
    foreach(\App\Models\Topic::all() as $key => $topic){
        DB::table('topics')->where('id', $topic->id)->update([
            'created_at' => now()->addSeconds($key)->format('Y-m-d H:i:s'),
            'updated_at' => now()->addSeconds($key)->format('Y-m-d H:i:s'),
        ]);
    }

    //  Set the created_at and updated_at so that each message is 1 second apart
    foreach(\App\Models\Message::all() as $key => $message){
        DB::table('messages')->where('id', $message->id)->update([
            'created_at' => now()->addSeconds($key)->format('Y-m-d H:i:s'),
            'updated_at' => now()->addSeconds($key)->format('Y-m-d H:i:s'),
        ]);
    }

    return 'UPLOADED';

});


