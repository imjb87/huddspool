<?php

use App\Http\Controllers\Api\Gpt\AdministrationCommandController;
use App\Http\Controllers\Api\Gpt\ConnectedAdminController;
use App\Http\Controllers\Api\Gpt\ContentAdministrationController;
use App\Http\Controllers\Api\Gpt\DashboardController;
use App\Http\Controllers\Api\Gpt\DeleteAdministrationRecordController;
use App\Http\Controllers\Api\Gpt\FixtureDateController;
use App\Http\Controllers\Api\Gpt\KnockoutAdministrationController;
use App\Http\Controllers\Api\Gpt\LeagueStructureController;
use App\Http\Controllers\Api\Gpt\OperationalAdministrationController;
use App\Http\Controllers\Api\Gpt\PlayerAccountController;
use App\Http\Controllers\Api\Gpt\PlayerController;
use App\Http\Controllers\Api\Gpt\PlayerPasswordResetController;
use App\Http\Controllers\Api\Gpt\PlayerTeamController;
use App\Http\Controllers\Api\Gpt\PublicInformationController;
use App\Http\Controllers\Api\Gpt\ResourceController;
use App\Http\Controllers\Api\Gpt\ResultCorrectionController;
use App\Http\Controllers\Api\Gpt\SupportTicketAdministrationController;
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
            Route::get('/players/{player}/profile', [PlayerController::class, 'show'])->name('api.gpt.players.show');
            Route::get('/teams', [TeamController::class, 'index'])->name('api.gpt.teams.index');
            Route::get('/teams/{team}/roster', [TeamController::class, 'roster'])->name('api.gpt.teams.roster');
            Route::get('/browse', PublicInformationController::class)->name('api.gpt.browse');
        });

        Route::middleware('scope:gpt:write')->group(function (): void {
            Route::post('/command', AdministrationCommandController::class)->name('api.gpt.command');
            Route::post('/players', [PlayerAccountController::class, 'store'])->name('api.gpt.players.store');
            Route::patch('/players/{player}', [PlayerAccountController::class, 'update'])->name('api.gpt.players.update');
            Route::post('/players/{player}/team', PlayerTeamController::class)->name('api.gpt.players.team.update');
            Route::post('/players/{player}/password-reset', PlayerPasswordResetController::class)->name('api.gpt.players.password-reset');
            Route::post('/fixtures/{fixture}/date', FixtureDateController::class)->name('api.gpt.fixtures.date.update');
            Route::post('/results/{result}/correction', ResultCorrectionController::class)->name('api.gpt.results.correction');
            Route::post('/teams', [LeagueStructureController::class, 'storeTeam'])->name('api.gpt.teams.store');
            Route::patch('/teams/{team}', [LeagueStructureController::class, 'updateTeam'])->name('api.gpt.teams.update');
            Route::post('/teams/{team}/fold', [LeagueStructureController::class, 'foldTeam'])->name('api.gpt.teams.fold');
            Route::post('/teams/{team}/captain', TeamCaptainController::class)->name('api.gpt.teams.captain.update');
            Route::post('/teams/{team}/venue', TeamVenueController::class)->name('api.gpt.teams.venue.update');
            Route::post('/venues', [LeagueStructureController::class, 'storeVenue'])->name('api.gpt.venues.store');
            Route::patch('/venues/{venue}', [LeagueStructureController::class, 'updateVenue'])->name('api.gpt.venues.update');
            Route::post('/sections/{section:id}/teams', [LeagueStructureController::class, 'addTeamToSection'])->name('api.gpt.sections.teams.store');
            Route::post('/seasons', [LeagueStructureController::class, 'storeSeason'])->name('api.gpt.seasons.store');
            Route::patch('/seasons/{season:id}', [LeagueStructureController::class, 'updateSeason'])->name('api.gpt.seasons.update');
            Route::post('/seasons/{season:id}/open', [LeagueStructureController::class, 'openSeason'])->name('api.gpt.seasons.open');
            Route::post('/sections', [LeagueStructureController::class, 'storeSection'])->name('api.gpt.sections.store');
            Route::patch('/sections/{section:id}', [LeagueStructureController::class, 'updateSection'])->name('api.gpt.sections.update');
            Route::patch('/section-teams/{sectionTeam}/deduction', [LeagueStructureController::class, 'updateDeduction'])->name('api.gpt.section-teams.deduction');
            Route::post('/section-teams/{sectionTeam}/withdraw', [LeagueStructureController::class, 'withdrawTeam'])->name('api.gpt.section-teams.withdraw');
            Route::post('/knockout-matches/{match}/result', [KnockoutAdministrationController::class, 'recordResult'])->name('api.gpt.knockout-matches.result');
            Route::post('/knockout-matches/{match}/forfeit', [KnockoutAdministrationController::class, 'recordForfeit'])->name('api.gpt.knockout-matches.forfeit');
            Route::post('/knockout-matches/{match}/clear-result', [KnockoutAdministrationController::class, 'clearResult'])->name('api.gpt.knockout-matches.clear-result');
            Route::post('/knockouts/{knockout:id}/generate-bracket', [KnockoutAdministrationController::class, 'generateBracket'])->name('api.gpt.knockouts.generate-bracket');
            Route::post('/knockouts/{knockout:id}/randomize-next-round', [KnockoutAdministrationController::class, 'randomizeNextRound'])->name('api.gpt.knockouts.randomize-next-round');
            Route::post('/knockouts', [KnockoutAdministrationController::class, 'storeKnockout'])->name('api.gpt.knockouts.store');
            Route::post('/knockouts/{knockout:id}/participants', [KnockoutAdministrationController::class, 'storeParticipant'])->name('api.gpt.knockouts.participants.store');
            Route::post('/knockouts/{knockout:id}/rounds', [KnockoutAdministrationController::class, 'storeRound'])->name('api.gpt.knockouts.rounds.store');
            Route::patch('/knockouts/{knockout:id}', [KnockoutAdministrationController::class, 'updateKnockout'])->name('api.gpt.knockouts.update');
            Route::patch('/knockout-participants/{participant}', [KnockoutAdministrationController::class, 'updateParticipant'])->name('api.gpt.knockout-participants.update');
            Route::patch('/knockout-rounds/{round}', [KnockoutAdministrationController::class, 'updateRound'])->name('api.gpt.knockout-rounds.update');
            Route::patch('/support-tickets/{ticket}', SupportTicketAdministrationController::class)->name('api.gpt.support-tickets.update');
            Route::post('/expulsions', [OperationalAdministrationController::class, 'storeExpulsion'])->name('api.gpt.expulsions.store');
            Route::patch('/notification-settings/{setting}', [OperationalAdministrationController::class, 'updateNotificationSetting'])->name('api.gpt.notification-settings.update');
            Route::post('/season-entries/{entry}/mark-paid', [OperationalAdministrationController::class, 'markEntryPaid'])->name('api.gpt.season-entries.mark-paid');
            Route::post('/content/{resource}', [ContentAdministrationController::class, 'store'])->name('api.gpt.content.store');
            Route::patch('/content/{resource}/{record}', [ContentAdministrationController::class, 'update'])->name('api.gpt.content.update');
            Route::delete('/resources/{resource}/{record}', DeleteAdministrationRecordController::class)->name('api.gpt.resources.destroy');
        });
    });
