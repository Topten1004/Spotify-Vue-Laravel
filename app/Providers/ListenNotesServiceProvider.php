<?php

namespace App\Providers;

use App\Helpers\Content\ListenNotes\ListenNotesAPI;
use App\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ListenNotesServiceProvider extends ServiceProvider
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
        if( file_exists(base_path('.env'))  &&  Schema::hasTable('settings') && Setting::get('provider_listenNotes')) {
            $this->app->singleton('ListenNotes', function () {
                $client = new \ListenNotes\PodcastApi\Client(config('listennotes.client_id'));
                return new ListenNotesAPI($client);
            });
        }
      
    }
}