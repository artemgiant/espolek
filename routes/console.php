<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Синхронізація транзакцій кожні 6 годин
Schedule::command('transactions:sync')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground();

// Очистка старих jobs щодня о 3:00
Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::command('queue:prune-failed --hours=72')->daily();
