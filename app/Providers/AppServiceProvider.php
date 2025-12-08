<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
        if (app()->environment('testing')) {
            Passport::loadKeysFrom(__DIR__.'/../../storage/testing_oauth');
        }else {
            Passport::loadKeysFrom(__DIR__.'/../../storage/oauth');
        }
        Passport::enablePasswordGrant();

        RateLimiter::for('redeem', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('invitation_lookup', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
