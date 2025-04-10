<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\JiraSyncCommand;
use Illuminate\Console\Scheduling\Schedule;

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
        //
        $this->app->booted(function () {
            $schedule = app(Schedule::class);

            // Manually call the command's schedule method
            (new JiraSyncCommand(app(\App\Services\JiraService::class)))->schedule($schedule);
        });
    }
}
