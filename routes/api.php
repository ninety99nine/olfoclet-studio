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

Route::prefix('/projects')->name('api.')->group(function () {

    Route::prefix('{project}')->group(function () {

        Route::get('/topics', [TopicController::class, 'get'])->name('topics');
        Route::get('/topics/{topic}/{type?}', [TopicController::class, 'show'])->name('topic');

        Route::get('/messages', [MessageController::class, 'get'])->name('messages');
        Route::get('/messages/{message}/{type?}', [MessageController::class, 'show'])->name('message');

        //  Subscribers
        Route::prefix('subscribers')->group(function () {
            Route::post('/', [SubscriberApiController::class, 'createSubscriber'])->name('create.subscriber');
        });

        //  Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionApiController::class, 'showSubscriptions'])->name('show.subscriptions');
            Route::post('/', [SubscriptionApiController::class, 'createSubscription'])->name('create.subscription');
        });

        //  Subscription Plans
        Route::prefix('subscription-plans')->group(function () {
            Route::get('/', [SubscriptionPlanApiController::class, 'showSubscriptionPlans'])->name('show.subscription.plans');
        });

    });

});
