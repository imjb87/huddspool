<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KnockoutController;
use App\Http\Controllers\KnockoutMatchController;


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

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');
Route::get('/tables/{ruleset}', 'App\Http\Controllers\TableController@index')->name('table.index');
Route::get('/fixtures-and-results/{ruleset}', 'App\Http\Controllers\FixtureController@index')->name('fixture.index');
Route::get('/fixtures/{fixture}', 'App\Http\Controllers\FixtureController@show')->name('fixture.show');
Route::get('/fixtures/download/{section}', 'App\Http\Controllers\FixtureController@download')->name('fixture.download');

Route::get('/results/{result}', 'App\Http\Controllers\ResultController@show')->name('result.show');
Route::get('/results/create/{fixture}', 'App\Http\Controllers\ResultController@create')->name('result.create');
Route::get('/players/averages/{ruleset}', 'App\Http\Controllers\PlayerController@index')->name('player.index');
Route::get('/players/{player}', 'App\Http\Controllers\PlayerController@show')->name('player.show');
Route::post('/players/{player}/avatar', [\App\Http\Controllers\PlayerController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('player.avatar');
Route::get('/teams/{team}', 'App\Http\Controllers\TeamController@show')->name('team.show');
Route::get('/rulesets/{ruleset}', 'App\Http\Controllers\RulesetController@show')->name('ruleset.show');
Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
Route::get('/history/{season}', [\App\Http\Controllers\HistoryController::class, 'season'])->name('history.season');
Route::get('/history/{season}/{ruleset}', [\App\Http\Controllers\HistoryController::class, 'show'])->name('history.show');
Route::get('/venues/{venue}', 'App\Http\Controllers\VenueController@show')->name('venue.show');
Route::get('/knockouts', [KnockoutController::class, 'index'])->name('knockout.index');
Route::get('/knockouts/{knockout:slug}', [KnockoutController::class, 'show'])->name('knockout.show');
Route::middleware('auth')->group(function () {
    Route::get('/knockout-matches/{match}/submit', [KnockoutMatchController::class, 'submit'])->name('knockout.matches.submit');
});
Route::get('/{page}', 'App\Http\Controllers\PageController@show')->name('page.show');
