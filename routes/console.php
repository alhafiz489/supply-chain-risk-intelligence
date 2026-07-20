<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('supplyguard:sync-countries')
    ->dailyAt('01:00')
    ->withoutOverlapping(30)
    ->appendOutputTo(
        storage_path('logs/country-sync.log')
    );

Schedule::command('supplyguard:sync-economy')
    ->dailyAt('02:00')
    ->withoutOverlapping(60)
    ->appendOutputTo(
        storage_path('logs/economy-sync.log')
    );

Schedule::command('supplyguard:sync-currency')
    ->cron('0 */6 * * *')
    ->withoutOverlapping(60)
    ->appendOutputTo(
        storage_path('logs/currency-sync.log')
    );

Schedule::command('supplyguard:sync-weather')
    ->hourlyAt(10)
    ->withoutOverlapping(50)
    ->appendOutputTo(
        storage_path('logs/weather-sync.log')
    );

Schedule::command('supplyguard:sync-global-news')
    ->hourlyAt(20)
    ->withoutOverlapping(55)
    ->appendOutputTo(
        storage_path('logs/news-sync.log')
    );

Schedule::command('supplyguard:recalculate-risks')
    ->dailyAt('04:30')
    ->withoutOverlapping(180);

Schedule::command('supplyguard:sync-global-ports')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping(360);
