<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::group(['prefix' => 'admin', 'middleware' => ['is_admin','auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', \App\Http\Livewire\Admin\User\Index::class)->name('admin.users.index');
    Route::get('/users/create', \App\Http\Livewire\Admin\User\Create::class)->name('admin.users.create');
    Route::get('/users/edit/{user}', \App\Http\Livewire\Admin\User\Edit::class)->name('admin.users.edit');
    Route::get('/users/{user}', \App\Http\Livewire\Admin\User\Show::class)->name('admin.users.show');

    Route::get('/venues', \App\Http\Livewire\Admin\Venue\Index::class)->name('admin.venues.index');
    Route::get('/venues/create', \App\Http\Livewire\Admin\Venue\Create::class)->name('admin.venues.create');
    Route::get('/venues/edit/{venue}', \App\Http\Livewire\Admin\Venue\Edit::class)->name('admin.venues.edit');
    Route::get('/venues/{venue}', \App\Http\Livewire\Admin\Venue\Show::class)->name('admin.venues.show');

    Route::get('/teams', \App\Http\Livewire\Admin\Team\Index::class)->name('admin.teams.index');
    Route::get('/teams/create', \App\Http\Livewire\Admin\Team\Create::class)->name('admin.teams.create');
    Route::get('/teams/edit/{team}', \App\Http\Livewire\Admin\Team\Edit::class)->name('admin.teams.edit');
    Route::get('/teams/{team}', \App\Http\Livewire\Admin\Team\Show::class)->name('admin.teams.show');

    Route::get('/rulesets', \App\Http\Livewire\Admin\Ruleset\Index::class)->name('admin.rulesets.index');
    Route::get('/rulesets/create', \App\Http\Livewire\Admin\Ruleset\Create::class)->name('admin.rulesets.create');
    Route::get('/rulesets/edit/{ruleset}', \App\Http\Livewire\Admin\Ruleset\Edit::class)->name('admin.rulesets.edit');
    Route::get('/rulesets/{ruleset}', \App\Http\Livewire\Admin\Ruleset\Show::class)->name('admin.rulesets.show');

    Route::get('/seasons', \App\Http\Livewire\Admin\Season\Index::class)->name('admin.seasons.index');
    Route::get('/seasons/create', \App\Http\Livewire\Admin\Season\Create::class)->name('admin.seasons.create');
    Route::get('/seasons/edit/{season}', \App\Http\Livewire\Admin\Season\Edit::class)->name('admin.seasons.edit');
    Route::get('/seasons/{season}', \App\Http\Livewire\Admin\Season\Show::class)->name('admin.seasons.show');

    Route::get('/sections/create/{season}', \App\Http\Livewire\Admin\Section\Create::class)->name('admin.sections.create');
    Route::get('/sections/edit/{section}', \App\Http\Livewire\Admin\Section\Edit::class)->name('admin.sections.edit');
    Route::get('/sections/{section}', \App\Http\Livewire\Admin\Section\Show::class)->name('admin.sections.show');

    Route::get('/fixtures/create/{section}', \App\Http\Livewire\Admin\Fixture\Create::class)->name('admin.fixtures.create');
    Route::get('/fixtures/show/{fixture}', \App\Http\Livewire\Admin\Fixture\Show::class)->name('admin.fixtures.show');

    Route::get('/results/create/{fixture}', \App\Http\Livewire\Admin\Result\Create::class)->name('admin.results.create');

    Route::get('/news', \App\Http\Livewire\Admin\News\Index::class)->name('admin.news.index');
    Route::get('/news/create', \App\Http\Livewire\Admin\News\Create::class)->name('admin.news.create');
    Route::get('/news/edit/{news}', \App\Http\Livewire\Admin\News\Edit::class)->name('admin.news.edit');
    Route::get('/news/{news}', \App\Http\Livewire\Admin\News\Show::class)->name('admin.news.show');

});