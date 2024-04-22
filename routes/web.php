<?php

use App\Enums\CreatedUsingAutoBilling;
use App\Enums\MessageType;
use App\Http\Controllers\AutoBillingReminderSubscriptionPlanController;
use App\Http\Controllers\AutoBillingScheduleController;
use App\Http\Controllers\AutoBillingSubscriptionPlanController;
use App\Http\Controllers\BillingReportController;
use App\Http\Controllers\BillingTransactionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SmsCampaignController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SmsCampaignScheduleController;
use App\Http\Controllers\SubscriberMessageController;
use App\Http\Controllers\TopicController;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\UserController;
use App\Http\Resources\SubscriptionResource;
use App\Jobs\BillingReport\StartCreatingBillingReports;
use App\Mail\MonthlyBillingReport;
use App\Models\BillingReport;
use App\Models\BillingTransaction;
use App\Models\Message;

use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Models\Pivots\SubscriberMessage;
use App\Models\Pivots\SubscriptionPlanAutoBillingReminder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\SmsCampaign;
use App\Models\Subscriber;
use App\Models\Project;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\BillingService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

            //  Subscribers
            Route::prefix('subscribers')->group(function () {
                Route::get('/', [SubscriberController::class, 'showSubscribers'])->middleware(['project.permission:View subscribers'])->name('show.subscribers');
                Route::post('/', [SubscriberController::class, 'createSubscriber'])->middleware(['project.permission:Manage subscribers'])->name('create.subscriber');

                Route::prefix('{subscriber}')->middleware(['project.permission:Manage subscribers'])->group(function () {
                    Route::put('/', [SubscriberController::class, 'updateSubscriber'])->name('update.subscriber');
                    Route::delete('/', [SubscriberController::class, 'deleteSubscriber'])->name('delete.subscriber');
                });
            });

            //  Subscriber Messages
            Route::prefix('subscriber-messages')
                ->middleware(['project.permission:View subscriber messages'])->group(function () {
                Route::get('/', [SubscriberMessageController::class, 'showSubscriberMessages'])->name('show.subscriber.messages');

                Route::prefix('{subscriber_message}')->group(function () {
                    Route::get('/', [SubscriberMessageController::class, 'showSubscriberMessage']);
                });
            });

            //  Auto Billing Schedules
            Route::prefix('auto-billing-schedules')
                ->middleware(['project.permission:View auto billing schedules'])->group(function () {
                Route::get('/', [AutoBillingScheduleController::class, 'showAutoBillingSchedules'])->name('show.auto.billing.schedules');

                Route::prefix('{auto_billing_schedule}')->group(function () {
                    Route::get('/', [AutoBillingScheduleController::class, 'showAutoBillingSchedule']);
                });
            });

            //  Billing Transactions
            Route::prefix('billing-transactions')
                ->middleware(['project.permission:View billing transactions'])->group(function () {
                Route::get('/', [BillingTransactionController::class, 'showBillingTransactions'])->name('show.billing.transactions');

                Route::prefix('{billing_transaction}')->group(function () {
                    Route::get('/', [BillingTransactionController::class, 'showBillingTransaction']);
                });
            });

            //  Billing Reports
            Route::prefix('billing-reports')
                ->middleware(['project.permission:View billing reports'])->group(function () {
                Route::get('/', [BillingReportController::class, 'showBillingReports'])->name('show.billing.reports');

                Route::prefix('{billing_report}')->group(function () {
                    Route::get('/', [BillingReportController::class, 'showBillingReport']);
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

            //  Sms Campaign Schedules
            Route::prefix('sms-campaign-schedules')
                ->middleware(['project.permission:View sms campaign schedules'])->group(function () {
                Route::get('/', [SmsCampaignScheduleController::class, 'showSmsCampaignSchedules'])->name('show.sms.campaign.schedules');

                Route::prefix('{sms_campaign_schedules}')->group(function () {
                    Route::get('/', [SmsCampaignScheduleController::class, 'showSmsCampaignSchedule']);
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

            //  Auto Billing Reminder Subscription Plans
            Route::prefix('auto-billing-reminder/subscription-plans')->middleware(['project.permission:View auto billing reminder subscription plans'])->group(function () {

                Route::get('/', [AutoBillingReminderSubscriptionPlanController::class, 'showAutoBillingReminderSubscriptionPlans'])->name('show.auto.billing.reminder.subscription.plans');

                Route::prefix('{subscription_plan}')->group(function () {
                    Route::get('/job-batches', [AutoBillingReminderSubscriptionPlanController::class, 'showAutoBillingReminderSubscriptionPlanJobBatches'])
                            ->name('show.auto.billing.subscription.plan.reminder.job.batches');
                });
            });

        });

    });

});

Route::get('/test-conversion', function() {

    return now()->addHours(72)->format('Y-m-d H:i:s');

    $subscriptionPlans = [
        [
            "id" => 22,
            "name" => "App Access - English Plans",
            "description" => null,
            "active" => 1,
            "is_folder" => 1,
            "frequency" => null,
            "duration" => null,
            "price" => null,
            "tags" => null,
            "can_auto_bill" => 0,
            "max_auto_billing_attempts" => 3,
            "insufficient_funds_message" => null,
            "successful_payment_sms_message" => null,
            "successful_auto_billing_payment_sms_message" => null,
            "next_auto_billing_reminder_sms_message" => null,
            "auto_billing_disabled_sms_message" => null,
            "project_id" => 1,
            "parent_id" => null,
            "created_at" => "2024-04-09 08:40:51",
            "updated_at" => "2024-04-09 08:40:51",
            "__children" => [
                [
                    "name" => "1 Day P3",
                    "description" => "Subscription for app access (1 day)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 1,
                    "price" => 3,
                    "tags" => "[\"english\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 1 day of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 1 day of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 1 day of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 1 day of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 06:00:00",
                    "updated_at" => "2024-03-05 06:00:00"
                ],
                [
                    "name" => "7 Days P15",
                    "description" => "Subscription for app access (7 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 7,
                    "price" => 15,
                    "tags" => "[\"english\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 7 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 7 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 7 days of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 7 days of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 05:00:00",
                    "updated_at" => "2024-03-05 05:00:00"
                ],
                [
                    "name" => "30 Days P50",
                    "description" => "Subscription for app access (30 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 30,
                    "price" => 50,
                    "tags" => "[\"english\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 30 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 30 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 30 days of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 30 days of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 04:00:00",
                    "updated_at" => "2024-03-05 04:00:00"
                ],
            ]
        ],
        [
            "id" => 23,
            "name" => "App Access - Setswana Plans",
            "description" => null,
            "active" => 1,
            "is_folder" => 1,
            "frequency" => null,
            "duration" => null,
            "price" => null,
            "tags" => null,
            "can_auto_bill" => 0,
            "max_auto_billing_attempts" => 3,
            "insufficient_funds_message" => null,
            "successful_payment_sms_message" => null,
            "successful_auto_billing_payment_sms_message" => null,
            "next_auto_billing_reminder_sms_message" => null,
            "auto_billing_disabled_sms_message" => null,
            "project_id" => 1,
            "parent_id" => null,
            "created_at" => "2024-04-09 08:41:04",
            "updated_at" => "2024-04-09 08:41:04",
            "__children" => [
                [
                    "name" => "1 Day P3",
                    "description" => "Subscription for app access (1 day)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 1,
                    "price" => 3,
                    "tags" => "[\"setswana\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction",
                    "successful_payment_sms_message" => "Your payment for 1 day of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 1 day of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 1 day of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 1 day of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 06:00:00",
                    "updated_at" => "2024-03-05 06:00:00"
                ],
                [
                    "name" => "7 Days P15",
                    "description" => "Subscription for app access (7 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 7,
                    "price" => 15,
                    "tags" => "[\"setswana\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 7 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 7 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 7 days of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 7 days of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 05:00:00",
                    "updated_at" => "2024-03-05 05:00:00"
                ],
                [
                    "name" => "30 Days P50",
                    "description" => "Subscription for app access (30 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 30,
                    "price" => 50,
                    "tags" => "[\"setswana\", \"app_access\"]",
                    "can_auto_bill" => 0,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 30 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 30 days of App Access priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 30 days of App Access priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 30 days of App Access priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 04:00:00",
                    "updated_at" => "2024-03-05 04:00:00"
                ],
            ]
        ],
        [
            "id" => 4,
            "name" => "Daily Quotes - English Plans",
            "description" => null,
            "active" => 1,
            "is_folder" => 1,
            "frequency" => null,
            "duration" => null,
            "price" => null,
            "tags" => null,
            "can_auto_bill" => 0,
            "max_auto_billing_attempts" => 3,
            "insufficient_funds_message" => null,
            "successful_payment_sms_message" => null,
            "successful_auto_billing_payment_sms_message" => null,
            "next_auto_billing_reminder_sms_message" => null,
            "auto_billing_disabled_sms_message" => null,
            "project_id" => 1,
            "parent_id" => null,
            "created_at" => "2024-03-05 07:40:27",
            "updated_at" => "2024-04-08 07:24:18",
            "__children" => [
                [
                    "name" => "Daily @ P1",
                    "description" => "Subscription for daily quotes (1 day)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 1,
                    "price" => 1,
                    "tags" => "[\"english\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 1 day of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 06:00:00",
                    "updated_at" => "2024-03-05 06:00:00"
                ],
                [
                    "name" => "Weekly @ P5",
                    "description" => "Subscription for daily quotes (7 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 7,
                    "price" => 5,
                    "tags" => "[\"english\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction",
                    "successful_payment_sms_message" => "Your payment for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 7 days of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 05:00:00",
                    "updated_at" => "2024-03-05 05:00:00"
                ],
                [
                    "name" => "Monthly @ P15",
                    "description" => "Subscription for daily quotes (30 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 30,
                    "price" => 15,
                    "tags" => "[\"english\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction",
                    "successful_payment_sms_message" => "Your payment for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 30 days of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 04:00:00",
                    "updated_at" => "2024-03-05 04:00:00"
                ],
            ]
        ],
        [
            "id" => 10,
            "name" => "Daily Quotes - Setswana Plans",
            "description" => null,
            "active" => 1,
            "is_folder" => 1,
            "frequency" => null,
            "duration" => null,
            "price" => null,
            "tags" => null,
            "can_auto_bill" => 0,
            "max_auto_billing_attempts" => 3,
            "insufficient_funds_message" => null,
            "successful_payment_sms_message" => null,
            "successful_auto_billing_payment_sms_message" => null,
            "next_auto_billing_reminder_sms_message" => null,
            "auto_billing_disabled_sms_message" => null,
            "project_id" => 1,
            "parent_id" => null,
            "created_at" => "2024-03-05 08:45:10",
            "updated_at" => "2024-03-05 08:45:10",
            "__children" => [
                [
                    "name" => "Daily @ P1",
                    "description" => "Subscription for daily quotes (1 day)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 1,
                    "price" => 1,
                    "tags" => "[\"setswana\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction.",
                    "successful_payment_sms_message" => "Your payment for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 1 day of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 1 day of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 06:00:00",
                    "updated_at" => "2024-03-05 06:00:00"
                ],
                [
                    "name" => "Weekly @ P5",
                    "description" => "Subscription for daily quotes (7 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 7,
                    "price" => 5,
                    "tags" => "[\"setswana\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction",
                    "successful_payment_sms_message" => "Your payment for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 7 days of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 7 days of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 05:00:00",
                    "updated_at" => "2024-03-05 05:00:00"
                ],
                [
                    "name" => "Monthly @ P15",
                    "description" => "Subscription for daily quotes (30 days)",
                    "active" => 1,
                    "is_folder" => 0,
                    "frequency" => "Days",
                    "duration" => 30,
                    "price" => 15,
                    "tags" => "[\"setswana\", \"daily_quotes\"]",
                    "can_auto_bill" => 1,
                    "max_auto_billing_attempts" => 2,
                    "insufficient_funds_message" => "You do not have enough funds to complete this transaction",
                    "successful_payment_sms_message" => "Your payment for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "successful_auto_billing_payment_sms_message" => "Your auto payment for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *217# to unsubscribe.",
                    "next_auto_billing_reminder_sms_message" => "You will be automatically billed for 30 days of Daily Quotes priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *217# to unsubscribe.",
                    "auto_billing_disabled_sms_message" => "You have been successfully unsubscribed from 30 days of Daily Quotes priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe.",
                    "project_id" => 1,
                    "created_at" => "2024-03-05 04:00:00",
                    "updated_at" => "2024-03-05 04:00:00"
                ]
            ]
        ],
    ];

    foreach($subscriptionPlans as $a => $subscriptionPlan) {

        $parentSubscriptionPlan = SubscriptionPlan::create($subscriptionPlan);

        foreach($subscriptionPlan['__children'] as $b => $childSubscriptionPlan) {

            $childSubscriptionPlan['created_at'] = now()->subHours($b);
            $childSubscriptionPlan['updated_at'] = now()->subHours($b);

            $childSubscriptionPlan = SubscriptionPlan::create($childSubscriptionPlan);

            $parentSubscriptionPlan->prependNode($childSubscriptionPlan);

        }

        $parentSubscriptionPlan->update([
            'created_at' => now()->subHours($a+$b),
            'updated_at' => now()->subHours($a+$b)
        ]);
    }

    return 'Done!';


    $response = Project::find(1)->subscriptionPlans()->whereAncestorOf(6)->pluck('id')->all();

    dd($response, count($response));
    return '';

    return \Carbon\Carbon::now()->addDay()->timestamp;

    $subscriber = Subscriber::find(1);
    $subscription = Subscription::find(1);
    $subscriptionPlan = SubscriptionPlan::find(9);

    /**
     *  @var Subscription $subscriptionWithFurthestEndAt
     */
    $subscriptionWithFurthestEndAt = $subscriber->subscriptionWithFurthestEndAt()
                                                ->where('subscription_plan_id', $subscriptionPlan->id)->first();

    return $subscriptionPlan->craftSuccessfulPaymentSmsMessage($subscriptionWithFurthestEndAt, $subscriptionWithFurthestEndAt);

});

Route::get('/save-pdf', function() {

    $project = Project::find(1);
    $billingReport = BillingReport::find(1);

    return Pdf::view('pdfs/monthly-billing-report', [
            'project' => $project,
            'billingReport' => $billingReport,
        ])->disk('public_uploads')
          ->save('pdf_files/monthly-billing-report.pdf')->pathinfo;

    return 'PDF DONE!';
});

Route::get('/StartCreatingBillingReports', function() {

    StartCreatingBillingReports::dispatch();
    return 'DONE';

});

Route::get('/MonthlyBillingReport', function() {

    $project = Project::find(1);
    $billingReport = BillingReport::find(1);

    foreach($project->billing_report_email_addresses as $emailAddress) {

        //  Send Monthly Billing Report Email
        Mail::to($emailAddress)->send(new MonthlyBillingReport($project, $billingReport));

    }

    return 'DONE';

});

Route::get('/test-sms', function() {

    $project = Project::find(1);
    $message = Message::find(3);
    $subscriber = Subscriber::find(1);
    return SmsService::sendSms($project, $subscriber, $message);

});

Route::get('/test-billing', function() {

    $project = Project::find(1);
    $subscriber = Subscriber::find(1);
    $subscriptionPlan = SubscriptionPlan::find(6);

    /**
     *  Bill the subscriber using artime.
     *
     *  @var BillingTransaction $billingTransaction
     */
    $billingTransaction = BillingTransaction::find(10);

    return $billingTransaction;

    //  Set the billing transaction status
    $isSuccessful = $billingTransaction->is_successful;

    //  If the subscriber was billed successfully
    if($isSuccessful) {

        //  Success message
        $message = 'Subscription created successfully';

        // Create a new subscription using the repository
        $subscription = $this->subscriptionRepository->createProjectSubscription($subscriber, $subscriptionPlan, CreatedUsingAutoBilling::NO, $billingTransaction);

    }else {

        //  Failure message
        $message = $billingTransaction->failure_reason;

    }

    // Return JSON response
    return response()->json([
        'message' => $message,
        'created' => $isSuccessful,
        'subscription' => $isSuccessful ? new SubscriptionResource($subscription) : null,
    ]);

});

Route::get('/testing2', function() {

    $project = Project::find(1);

    /**
     *  Query the subscribers that are ready to receive the next sms message
     */
    $subscribers = $project->subscribers()->whereDoesntHave('smsCampaigns', function (Builder $query) {

        $smsCampaign = Subscriber::find(1);

        $query->where('sms_campaigns.id', $smsCampaign->id)
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
         *          "message_id": 2,
         *          "subscriber_id": 1
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


