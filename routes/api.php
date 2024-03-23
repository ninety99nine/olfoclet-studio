<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SubscriberApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\SubscriptionPlanApiController;

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
            Route::get('/', [SubscriberApiController::class, 'showSubscribers'])->name('show.subscriber');
            Route::post('/', [SubscriberApiController::class, 'createSubscriber'])->name('create.subscriber');
        });

        //  Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionApiController::class, 'showSubscriptions'])->name('show.subscriptions');
            Route::post('/', [SubscriptionApiController::class, 'createSubscription'])->name('create.subscription');
            Route::post('/cancel', [SubscriptionApiController::class, 'cancelSubscriptions'])->name('cancel.subscriptions');

            Route::prefix('{subscription}')->group(function () {
                Route::get('/{type?}', [SubscriptionApiController::class, 'showSubscription'])->name('show.subscription');
            });
        });

        //  Subscription Plans
        Route::prefix('subscription-plans')->group(function () {
            Route::get('/', [SubscriptionPlanApiController::class, 'showSubscriptionPlans'])->name('show.subscription.plans');

            Route::prefix('{subscription_plan}')->group(function () {
                Route::get('/{type?}', [SubscriptionPlanApiController::class, 'showSubscriptionPlan'])->name('show.subscription.plan');
            });
        });

    });

});
