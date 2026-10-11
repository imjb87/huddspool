<?php

use Illuminate\Foundation\Inspiring;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
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

Artisan::command('app:search:reindex {--flush}', function (): int {
    $models = [User::class, Team::class, Venue::class, Section::class];

    foreach ($models as $model) {
        if ($this->option('flush') && $this->call('scout:flush', ['model' => $model]) !== 0) {
            return 1;
        }

        if ($this->call('scout:import', ['model' => $model]) !== 0) {
            return self::FAILURE;
        }
    }

    $this->info('Scout search indexes have been refreshed.');

    return 0;
})->purpose('Refresh all Scout search indexes');

Schedule::command('sitemap:generate')->daily();
Schedule::command('app:purge-old-notifications')->dailyAt('01:00');
Schedule::command('app:send-league-night-tonight-notifications')->dailyAt('12:00');
Schedule::command('app:send-tuesday-result-catchup-notifications')->sundays()->at('12:00');
Schedule::command('app:send-knockout-match-reminder-notifications')->dailyAt('12:00');
Schedule::command('app:send-match-night-started-notifications')->dailyAt('20:00');
Schedule::command('app:send-outstanding-fixture-notifications')->dailyAt('12:00');
Schedule::command('app:send-outstanding-knockout-notifications')->dailyAt('12:00');
