<?php

use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    $schedule->command('jira:sync-all')->hourly()->appendOutputTo(storage_path('logs/jira-sync.log'));
};
