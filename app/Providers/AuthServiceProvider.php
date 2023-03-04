<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Carbon\Carbon;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register plicies & define the passport API tokens.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::tokensCan([
            'do_anything' => 'the application license owner',
            'manage_everything' => 'can do everything except giving/removing admin permissions ( admin role )',
            'manage_own_content' => 'has the freedom the upload content into the app ( artist role )',
            'user_scope' => 'can upload Songs only and need an admin to approve his content'
        ]);
        Passport::routes();

        Passport::tokensExpireIn(Carbon::now()->addDays(1));
    }
}
