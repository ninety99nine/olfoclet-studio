<?php

use App\Http\Controllers\AutoBillingReminderSubscriptionPlanController;
use App\Http\Controllers\AutoBillingSubscriptionPlanController;
use App\Http\Controllers\AutoBillingScheduleController;
use App\Http\Controllers\SmsCampaignScheduleController;
use App\Http\Controllers\BillingTransactionController;
use App\Http\Controllers\SubscriberMessageController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\BillingReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SmsCampaignController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    //  Server Commands
    Route::prefix('/server')->group(function () {

        Route::get('/', [ServerController::class, 'showServer'])->name('show.server');

        Route::post('/view-clear', [ServerController::class, 'viewClear'])->name('view.clear');
        Route::post('/route-clear', [ServerController::class, 'routeClear'])->name('route.clear');
        Route::post('/config-clear', [ServerController::class, 'configClear'])->name('config.clear');
        Route::post('/cache-clear', [ServerController::class, 'cacheClear'])->name('cache.clear');

        Route::post('/view-cache', [ServerController::class, 'viewCache'])->name('view.cache');
        Route::post('/event-cache', [ServerController::class, 'eventCache'])->name('event.cache');
        Route::post('/route-cache', [ServerController::class, 'routeCache'])->name('route.cache');
        Route::post('/config-cache', [ServerController::class, 'configCache'])->name('config.cache');
        Route::post('/optimize', [ServerController::class, 'optimize'])->name('optimize');
        Route::post('/handle-server-errors', [ServerController::class, 'handleServerErrors'])->name('handle.server.errors');

    });

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
