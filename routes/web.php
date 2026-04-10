<?php

use App\Http\Controllers\AccountNotificationController;
use App\Http\Controllers\DesignSystemController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnockoutController;
use App\Http\Controllers\KnockoutMatchController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RulesetController;
use App\Http\Controllers\SeasonEntryController;
use App\Http\Controllers\SiteSearchController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;
use LaravelPWA\Http\Controllers\LaravelPWAController;
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

Route::get('/', HomeController::class)->name('home');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/search', SiteSearchController::class)->name('search.index');
Route::prefix('rulesets')->group(function () {
    Route::get('/{ruleset}', [RulesetController::class, 'show'])->name('ruleset.show');
    Route::get('/{ruleset}/rules', [RulesetController::class, 'rules'])->name('ruleset.rules');
    Route::get('/{ruleset}/{section}', [RulesetController::class, 'section'])->name('ruleset.section.show');
});
Route::prefix('fixtures')->group(function () {
    Route::get('/{fixture}', [FixtureController::class, 'show'])->name('fixture.show');
    Route::get('/download/{ruleset}/{section}', [FixtureController::class, 'download'])->name('fixture.download');
});
Route::prefix('results')->group(function () {
    Route::get('/{result}', [ResultController::class, 'show'])->name('result.show');
    Route::get('/{result}/og-image', [ResultController::class, 'ogImage'])->name('result.og-image');
    Route::get('/create/{fixture}', [ResultController::class, 'create'])->name('result.create');
});
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('player.show');
Route::post('/players/{player}/avatar', [PlayerController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('player.avatar');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('team.show');
Route::prefix('knockouts')->group(function () {
    Route::get('/', [KnockoutController::class, 'index'])->name('knockout.index');
    Route::get('/{knockout}', [KnockoutController::class, 'show'])->name('knockout.show');
});
Route::prefix('history')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/{season}/knockouts/{knockout}', [KnockoutController::class, 'history'])->name('history.knockout.show');
    Route::get('/{season}/{ruleset}/{section}', [HistoryController::class, 'section'])->name('history.section.show');
});
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venue.show');
Route::get('/seasons/{season}/sign-up', [SeasonEntryController::class, 'show'])->name('season.entry.show');
Route::get('/seasons/{season}/sign-up/orders/{entry:reference}', [SeasonEntryController::class, 'confirmation'])->name('season.entry.confirmation');
Route::get('/seasons/{season}/sign-up/orders/{entry:reference}/invoice', [SeasonEntryController::class, 'invoice'])->name('season.entry.invoice');
Route::get('/manifest.json', [LaravelPWAController::class, 'manifestJson'])->name('laravelpwa.manifest');
Route::get('/offline', [LaravelPWAController::class, 'offline'])->name('laravelpwa.offline');
Route::middleware('auth')->group(function () {
    Route::view('/account', 'account.show')->name('account.show');
    Route::get('/account/notifications/summary', [AccountNotificationController::class, 'summary'])->name('account.notifications.summary');
    Route::post('/account/notifications/read-all', [AccountNotificationController::class, 'markAllAsRead'])->name('account.notifications.read-all');
    Route::post('/account/notifications/{notification}/read', [AccountNotificationController::class, 'markAsRead'])->name('account.notifications.read');
    Route::get('/account/notifications/{notification}/open', [AccountNotificationController::class, 'open'])->name('account.notifications.open');
    Route::post('/account/push-permission/acknowledge', [PushSubscriptionController::class, 'acknowledge'])->name('account.push-permission.acknowledge');
    Route::post('/account/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('account.push-subscriptions.store');
    Route::delete('/account/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('account.push-subscriptions.destroy');
    Route::view('/account/team', 'account.team')->name('account.team');
    Route::redirect('/support/tickets', '/account/support');
    Route::get('/design-system', DesignSystemController::class)->name('design-system.index');
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
        Route::get('/account/support', [SupportTicketController::class, 'create'])->name('support.tickets');
        Route::post('/account/support', [SupportTicketController::class, 'store'])->name('support.tickets.store');
    });
});
Route::get('/pages/{page}', [PageController::class, 'show'])->name('page.show');
