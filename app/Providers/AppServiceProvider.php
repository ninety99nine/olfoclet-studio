<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         *  Disable Wrapping API Resources
         *
         *  If you would like to disable the wrapping of the outer-most resource, you may use the
         *  "withoutWrapping" method on the base resource class. Typically, you should call this
         *  method from your AppServiceProvider or another service provider that is loaded on
         *  every request to your application:
         *
         *  Reference: https://laravel.com/docs/10.x/eloquent-resources
         */
        JsonResource::withoutWrapping();
    }
}
