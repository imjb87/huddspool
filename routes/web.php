<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnockoutController;
use App\Http\Controllers\KnockoutMatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RulesetController;
use App\Http\Controllers\SeasonEntryController;
use App\Http\Controllers\SupportTicketController;
use App\Models\Ruleset;
use App\Models\Season;
use App\Support\ResponseCacheTags;
use Illuminate\Support\Facades\Route;
use LaravelPWA\Http\Controllers\LaravelPWAController;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use STS\FilamentImpersonate\Facades\Impersonation;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::pattern('season', '[^/]*[0-9][^/]*');

Route::get('/', HomeController::class)
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::HOME]))
    ->name('home');
Route::middleware(CacheResponse::for(tags: [ResponseCacheTags::RULESETS]))->group(function () {
    Route::get('/rulesets', [RulesetController::class, 'index'])->name('ruleset.index');
    Route::get('/tables/{ruleset}', 'App\Http\Controllers\TableController@index')->name('table.index');
    Route::get('/fixtures-and-results/{ruleset}', 'App\Http\Controllers\FixtureController@index')->name('fixture.index');
    Route::get('/players/averages/{ruleset}', 'App\Http\Controllers\PlayerController@index')->name('player.index');
});
Route::middleware(CacheResponse::for(tags: [ResponseCacheTags::FIXTURES]))->group(function () {
    Route::get('/fixtures/{fixture}', 'App\Http\Controllers\FixtureController@show')->name('fixture.show');
    Route::get('/fixtures/download/{ruleset}/{section}', 'App\Http\Controllers\FixtureController@download')->name('fixture.download');
});
Route::get('/results/{result}', 'App\Http\Controllers\ResultController@show')
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::RESULTS]))
    ->name('result.show');
Route::get('/results/create/{fixture}', 'App\Http\Controllers\ResultController@create')->name('result.create');
Route::get('/players/{player}', 'App\Http\Controllers\PlayerController@show')
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::PLAYERS]))
    ->name('player.show');
Route::post('/players/{player}/avatar', [PlayerController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('player.avatar');
Route::get('/teams/{team}', 'App\Http\Controllers\TeamController@show')
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::TEAMS]))
    ->name('team.show');
Route::middleware(CacheResponse::for(tags: [ResponseCacheTags::KNOCKOUTS]))->group(function () {
    Route::get('/knockouts', [KnockoutController::class, 'index'])->name('knockout.index');
    Route::get('/knockouts/{knockout}', [KnockoutController::class, 'show'])->name('knockout.show');
});
Route::middleware(CacheResponse::for(tags: [ResponseCacheTags::HISTORY]))->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{season}', [HistoryController::class, 'season'])->name('history.season');
    Route::get('/history/{season}/knockouts/{knockout}', function (Season $season, string $knockout) {
        return redirect()->route('history.knockout.show', array_merge(request()->query(), [
            'season' => $season,
            'knockout' => $knockout,
        ]));
    });
    Route::get('/history/{season}/{ruleset}', function (Season $season, Ruleset $ruleset) {
        return redirect()->route('history.show', array_merge(request()->query(), [
            'season' => $season,
            'ruleset' => $ruleset,
        ]));
    });
    Route::get('/history/{season}/{ruleset}/{section}', function (Season $season, Ruleset $ruleset, string $section) {
        return redirect()->route('history.section.show', array_merge(request()->query(), [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
        ]));
    });
    Route::get('/{season}/knockouts/{knockout}', [KnockoutController::class, 'history'])->name('history.knockout.show');
    Route::get('/{season}/{ruleset}', [HistoryController::class, 'show'])->name('history.show');
    Route::get('/{season}/{ruleset}/{section}', [HistoryController::class, 'section'])->name('history.section.show');
});
Route::get('/venues/{venue}', 'App\Http\Controllers\VenueController@show')
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::VENUES]))
    ->name('venue.show');
Route::get('/seasons/{season}/sign-up', [SeasonEntryController::class, 'show'])->name('season.entry.show');
Route::get('/seasons/{season}/sign-up/orders/{entry:reference}', [SeasonEntryController::class, 'confirmation'])->name('season.entry.confirmation');
Route::get('/seasons/{season}/sign-up/orders/{entry:reference}/invoice', [SeasonEntryController::class, 'invoice'])->name('season.entry.invoice');
Route::get('/manifest.json', [LaravelPWAController::class, 'manifestJson'])->name('laravelpwa.manifest');
Route::get('/offline', [LaravelPWAController::class, 'offline'])->name('laravelpwa.offline');
Route::middleware('auth')->group(function () {
    Route::view('/account', 'account.show')->name('account.show');
    Route::view('/account/team', 'account.team')->name('account.team');
    Route::get('/stop-impersonating', function () {
        if (! Impersonation::isImpersonating()) {
            return redirect('/');
        }

        $redirectTo = session('impersonate.back_to') ?? '/';

        Impersonation::leave();

        return redirect($redirectTo);
    })->name('impersonation.leave');
    Route::get('/knockout-matches/{match}/submit', [KnockoutMatchController::class, 'submit'])->name('knockout.matches.submit');
    Route::middleware('throttle:support-tickets')->group(function () {
        Route::get('/support/tickets', [SupportTicketController::class, 'create'])->name('support.tickets');
        Route::post('/support/tickets', [SupportTicketController::class, 'store'])->name('support.tickets.store');
    });
});
Route::middleware(CacheResponse::for(tags: [ResponseCacheTags::RULESETS]))->group(function () {
    Route::get('/rulesets/{ruleset}', function (Ruleset $ruleset) {
        return redirect()->route('ruleset.show', array_merge(request()->query(), [
            'ruleset' => $ruleset,
        ]));
    });
    Route::get('/rulesets/{ruleset}/{section}', function (Ruleset $ruleset, string $section) {
        $resolvedSection = $ruleset->openSections()
            ->where('slug', $section)
            ->firstOrFail();

        return redirect()->route('ruleset.section.show', array_merge(request()->query(), [
            'ruleset' => $ruleset,
            'section' => $resolvedSection,
        ]));
    })->scopeBindings();
    Route::get('/{ruleset}', [RulesetController::class, 'show'])->name('ruleset.show');
    Route::get('/{ruleset}/{section}', [RulesetController::class, 'section'])->name('ruleset.section.show');
});
Route::get('/{page}', 'App\Http\Controllers\PageController@show')
    ->middleware(CacheResponse::for(tags: [ResponseCacheTags::PAGES]))
    ->name('page.show');
