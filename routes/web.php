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

Route::get('/', \App\Http\Controllers\HomeController::class);

Route::get('/tables/{ruleset}', \App\Http\Livewire\Table\Index::class)->name('table.index');
Route::get('/teams/{team}', \App\Http\Livewire\Team\Show::class)->name('team.show');
Route::get('/players/averages/{ruleset}', \App\Http\Livewire\Player\Index::class)->name('player.index');
Route::get('/players/{player}', \App\Http\Livewire\Player\Show::class)->name('player.show');
Route::get('/venues', \App\Http\Livewire\Venue\Index::class)->name('venue.index');
Route::get('/venues/{venue}', \App\Http\Livewire\Venue\Show::class)->name('venue.show');
Route::get('/fixtures-and-results/{ruleset}', \App\Http\Livewire\Fixture\Index::class)->name('fixture.index');
Route::get('/fixtures/{fixture}', \App\Http\Livewire\Fixture\Show::class)->name('fixture.show');
Route::get('/results/create/{fixture}', \App\Http\Livewire\Result\Create::class)->name('result.create');
