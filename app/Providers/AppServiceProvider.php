<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\JiraSyncCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

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

        View::composer('*', function ($view) {
            $view->with('appVersion', 'Scout v1.2.1');

            $lastSynced = Cache::get('jira_last_sync');
            $view->with('jiraLastSynced', $lastSynced ? $lastSynced->diffForHumans() : '--');
        });
    }
}
