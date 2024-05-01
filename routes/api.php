<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptionPlanController;

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

Route::/*middleware('verify.api.bearer.token')->*/prefix('/projects')->name('api.')->group(function () {

    Route::prefix('{project}')->group(function () {

        //  Topics
        Route::prefix('topics')->group(function () {
            Route::get('/', [TopicController::class, 'showTopipcs'])->name('show.topics');

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

        //  Subscription Plans
        Route::prefix('subscription-plans')->group(function () {
            Route::get('/', [SubscriptionPlanController::class, 'showSubscriptionPlans'])->name('show.subscription.plans');

            Route::prefix('{subscription_plan}')->group(function () {
                Route::get('/{type?}', [SubscriptionPlanController::class, 'showSubscriptionPlan'])->name('show.subscription.plan');
            });
        });

    });

});
