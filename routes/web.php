<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


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
require __DIR__.'/admin.php';

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Route::get('/standings/{ruleset}', \App\Livewire\Standings\Index::class)->name('standings.index');
Route::get('/teams/{team}', \App\Livewire\Team\Show::class)->name('team.show');
Route::get('/players/averages/{ruleset}', \App\Livewire\Player\Index::class)->name('player.index');
Route::get('/players/{player}', \App\Livewire\Player\Show::class)->name('player.show');
Route::get('/venues', \App\Livewire\Venue\Index::class)->name('venue.index');
Route::get('/venues/{venue}', \App\Livewire\Venue\Show::class)->name('venue.show');
Route::get('/fixtures-and-results/{ruleset}', \App\Livewire\Fixture\Index::class)->name('fixture.index');
Route::get('/fixtures/{fixture}', \App\Livewire\Fixture\Show::class)->name('fixture.show');
Route::get('/results/create/{fixture}', \App\Livewire\Result\Create::class)->name('result.create');
Route::get('/results/{result}', \App\Livewire\Result\Show::class)->name('result.show');
Route::get('/rulesets/{ruleset}', \App\Livewire\Ruleset\Show::class)->name('ruleset.show');
Route::get('/history/{season}/{ruleset}', \App\Livewire\History::class)->name('history.show');
Route::get('/{slug}', \App\Livewire\Page\Show::class)->name('page.show');
