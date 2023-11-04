<?php

namespace App\Providers;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Twilio\Rest\Client as Twilio;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(200);

        $this->app->scoped('twilio', function($app) {
            return new Twilio(env('TWILIO_ACCOUNT_SID'), env('TWILIO_SECRET_TOKEN'));
        });

        Date::serializeUsing(function ($date) {
            return $date->toDateTimeLocalString();
        });
    }
}
