<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sitemap:generate')->daily();
Schedule::command('app:purge-old-notifications')->dailyAt('01:00');
Schedule::command('app:send-league-night-tonight-notifications')->dailyAt('12:00');
Schedule::command('app:send-tuesday-result-catchup-notifications')->sundays()->at('12:00');
Schedule::command('app:send-knockout-match-reminder-notifications')->dailyAt('12:00');
Schedule::command('app:send-match-night-started-notifications')->dailyAt('20:00');
Schedule::command('app:send-outstanding-fixture-notifications')->dailyAt('12:00');
Schedule::command('app:send-outstanding-knockout-notifications')->dailyAt('12:00');
