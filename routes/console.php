<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('autofill:cleanup')->daily();

// Queue worker for shared hosting (no Supervisor available).
// Runs every minute, processes jobs for up to 55 seconds, then exits
// before the next invocation. withoutOverlapping() prevents duplicate workers.
Schedule::command('queue:work --queue=autofill --stop-when-empty --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();

// Auto-recover workflows stuck for more than 30 minutes (queue worker crash, deploy, etc.)
Schedule::command('autofill:recover --minutes=30')->everyFifteenMinutes();
