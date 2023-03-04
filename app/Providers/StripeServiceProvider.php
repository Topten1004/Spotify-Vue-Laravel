<?php

namespace App\Providers;

use App\Helpers\Billing\StripeAPI;
use App\Setting;
use Illuminate\Support\ServiceProvider;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if( config('services.stripe.key') && Setting::get('enableBilling')) {
            $this->app->singleton(StripeAPI::class, function () {
                $client = new StripeAPI();
                return $client;
            });
        }
    }
}
