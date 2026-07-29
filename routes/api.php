<?php

use App\Http\Controllers\Api\Gpt\ConnectedAdminController;
use App\Http\Controllers\Api\Gpt\DashboardController;
use App\Http\Controllers\Api\Gpt\FixtureDateController;
use App\Http\Controllers\Api\Gpt\PlayerAccountController;
use App\Http\Controllers\Api\Gpt\PlayerController;
use App\Http\Controllers\Api\Gpt\PlayerPasswordResetController;
use App\Http\Controllers\Api\Gpt\PlayerTeamController;
use App\Http\Controllers\Api\Gpt\ResourceController;
use App\Http\Controllers\Api\Gpt\ResultCorrectionController;
use App\Http\Controllers\Api\Gpt\TeamCaptainController;
use App\Http\Controllers\Api\Gpt\TeamController;
use App\Http\Controllers\Api\Gpt\TeamVenueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('gpt')
    ->middleware(['auth:api', 'gpt.admin'])
    ->group(function (): void {
        Route::middleware('scope:gpt:read')->group(function (): void {
            Route::get('/me', ConnectedAdminController::class)->name('api.gpt.me');
            Route::get('/dashboard', DashboardController::class)->name('api.gpt.dashboard');
            Route::get('/capabilities', [ResourceController::class, 'capabilities'])->name('api.gpt.capabilities');
            Route::get('/resources/{resource}', [ResourceController::class, 'index'])->name('api.gpt.resources.index');
            Route::get('/resources/{resource}/{record}', [ResourceController::class, 'show'])->name('api.gpt.resources.show');
            Route::get('/players', [PlayerController::class, 'index'])->name('api.gpt.players.index');
            Route::get('/teams', [TeamController::class, 'index'])->name('api.gpt.teams.index');
            Route::get('/teams/{team}/roster', [TeamController::class, 'roster'])->name('api.gpt.teams.roster');
        });

        Route::middleware('scope:gpt:write')->group(function (): void {
            Route::post('/players', [PlayerAccountController::class, 'store'])->name('api.gpt.players.store');
            Route::patch('/players/{player}', [PlayerAccountController::class, 'update'])->name('api.gpt.players.update');
            Route::post('/players/{player}/team', PlayerTeamController::class)->name('api.gpt.players.team.update');
            Route::post('/players/{player}/password-reset', PlayerPasswordResetController::class)->name('api.gpt.players.password-reset');
            Route::post('/fixtures/{fixture}/date', FixtureDateController::class)->name('api.gpt.fixtures.date.update');
            Route::post('/results/{result}/correction', ResultCorrectionController::class)->name('api.gpt.results.correction');
            Route::post('/teams/{team}/captain', TeamCaptainController::class)->name('api.gpt.teams.captain.update');
            Route::post('/teams/{team}/venue', TeamVenueController::class)->name('api.gpt.teams.venue.update');
        });
    });
