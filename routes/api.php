<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\PricingPlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('verify.api.bearer.token')->prefix('/projects')->name('api.')->group(function () {

    Route::prefix('{project}')->group(function () {

        //  Topics
        Route::prefix('topics')->group(function () {
            Route::get('/', [TopicController::class, 'showTopics'])->name('show.topics');

            Route::prefix('{topic}')->group(function () {
                Route::get('/{type?}', [TopicController::class, 'showTopic'])->name('show.topic');
            });
        });

        //  Messages
        Route::prefix('messages')->group(function () {
            Route::get('/', [MessageController::class, 'showMessages'])->name('show.messages');

            Route::prefix('{message}')->group(function () {
                Route::get('/{type?}', [MessageController::class, 'showMessage'])->name('show.message');
            });
        });

        //  Subscribers
        Route::prefix('subscribers')->group(function () {
            Route::post('/', [SubscriberController::class, 'createSubscriber'])->name('create.subscriber');

            //  To avoid conflict between the route "msisdn" and the request input "msisdn" we will
            //  name the route "msisdn" to "subscriber_msisdn"
            Route::prefix('{subscriber_msisdn}')->group(function () {
                Route::get('/', [SubscriberController::class, 'showSubscriber'])->name('show.subscriber');
                Route::put('/', [SubscriberController::class, 'updateSubscriber'])->name('update.subscriber');
                Route::delete('/', [SubscriberController::class, 'deleteSubscriber'])->name('delete.subscriber');
            });
        });

        //  Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'showSubscriptions'])->name('show.subscriptions');
            Route::post('/', [SubscriptionController::class, 'createSubscription'])->name('create.subscription');
            Route::post('/cancel', [SubscriptionController::class, 'cancelSubscriptions'])->name('cancel.subscriptions');

            Route::prefix('{subscription}')->group(function () {
                Route::get('/', [SubscriptionController::class, 'showSubscription'])->name('show.subscription');
                Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('cancel.subscription');
                Route::post('/uncancel', [SubscriptionController::class, 'uncancelSubscription'])->name('uncancel.subscription');
            });
        });

        //  Pricing Plans
        Route::prefix('pricing-plans')->group(function () {
            Route::get('/', [PricingPlanController::class, 'showPricingPlans'])->name('show.pricing.plans');

            Route::prefix('{pricing_plan}')->group(function () {
                Route::get('/{type?}', [PricingPlanController::class, 'showPricingPlan'])->name('show.pricing.plan');
            });
        });

        //  Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/overview', [AnalyticsController::class, 'overview'])->name('overview');
            Route::get('/subscribers-over-time', [AnalyticsController::class, 'subscribersOverTime'])->name('subscribers-over-time');
            Route::get('/transactions-over-time', [AnalyticsController::class, 'transactionsOverTime'])->name('transactions-over-time');
            Route::get('/messages-over-time', [AnalyticsController::class, 'messagesOverTime'])->name('messages-over-time');
            Route::get('/subscription-mix', [AnalyticsController::class, 'subscriptionMix'])->name('subscription-mix');
            Route::get('/subscriptions-by-plan', [AnalyticsController::class, 'subscriptionsByPlan'])->name('subscriptions-by-plan');
            Route::get('/revenue-over-time', [AnalyticsController::class, 'revenueOverTime'])->name('revenue-over-time');
            Route::get('/subscriptions-over-time', [AnalyticsController::class, 'subscriptionsOverTime'])->name('subscriptions-over-time');
        });

    });

});
