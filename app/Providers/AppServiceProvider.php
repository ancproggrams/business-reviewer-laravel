<?php

namespace App\Providers;

use App\Review;
use App\ReviewReaction;
use App\Observers\ReviewObserver;
use App\Observers\ReactionObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Review::observe(ReviewObserver::class);
        ReviewReaction::observe(ReactionObserver::class);
    }
}
