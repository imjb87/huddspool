<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::group(['prefix' => 'admin', 'middleware' => ['is_admin', 'admin']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', \App\Livewire\Admin\User\Index::class)->name('admin.users.index');
    Route::get('/users/create', \App\Livewire\Admin\User\Create::class)->name('admin.users.create');
    Route::get('/users/create/{team_id}', \App\Livewire\Admin\User\Create::class)->name('admin.users.team.create');
    Route::get('/users/edit/{user}', \App\Livewire\Admin\User\Edit::class)->name('admin.users.edit');
    Route::get('/users/{user}', \App\Livewire\Admin\User\Show::class)->name('admin.users.show');

    Route::get('/venues', \App\Livewire\Admin\Venue\Index::class)->name('admin.venues.index');
    Route::get('/venues/create', \App\Livewire\Admin\Venue\Create::class)->name('admin.venues.create');
    Route::get('/venues/edit/{venue}', \App\Livewire\Admin\Venue\Edit::class)->name('admin.venues.edit');
    Route::get('/venues/{venue}', \App\Livewire\Admin\Venue\Show::class)->name('admin.venues.show');

    Route::get('/teams', \App\Livewire\Admin\Team\Index::class)->name('admin.teams.index');
    Route::get('/teams/create', \App\Livewire\Admin\Team\Create::class)->name('admin.teams.create');
    Route::get('/teams/edit/{team}', \App\Livewire\Admin\Team\Edit::class)->name('admin.teams.edit');
    Route::get('/teams/{team}', \App\Livewire\Admin\Team\Show::class)->name('admin.teams.show');

    Route::get('/rulesets', \App\Livewire\Admin\Ruleset\Index::class)->name('admin.rulesets.index');
    Route::get('/rulesets/create', \App\Livewire\Admin\Ruleset\Create::class)->name('admin.rulesets.create');
    Route::get('/rulesets/edit/{ruleset}', \App\Livewire\Admin\Ruleset\Edit::class)->name('admin.rulesets.edit');
    Route::get('/rulesets/{ruleset}', \App\Livewire\Admin\Ruleset\Show::class)->name('admin.rulesets.show');

    Route::get('/seasons', \App\Livewire\Admin\Season\Index::class)->name('admin.seasons.index');
    Route::get('/seasons/create', \App\Livewire\Admin\Season\Create::class)->name('admin.seasons.create');
    Route::get('/seasons/edit/{season}', \App\Livewire\Admin\Season\Edit::class)->name('admin.seasons.edit');
    Route::get('/seasons/{season}', \App\Livewire\Admin\Season\Show::class)->name('admin.seasons.show');

    Route::get('/sections/create/{season}', \App\Livewire\Admin\Section\Create::class)->name('admin.sections.create');
    Route::get('/sections/edit/{section}', \App\Livewire\Admin\Section\Edit::class)->name('admin.sections.edit');
    Route::get('/sections/{section}', \App\Livewire\Admin\Section\Show::class)->name('admin.sections.show');

    Route::get('/fixtures/create/{section}', \App\Livewire\Admin\Fixture\Create::class)->name('admin.fixtures.create');
    Route::get('/fixtures/show/{fixture}', \App\Livewire\Admin\Fixture\Show::class)->name('admin.fixtures.show');
    Route::get('/fixtures/edit/{fixture}', \App\Livewire\Admin\Fixture\Edit::class)->name('admin.fixtures.edit');

    Route::get('/results/create/{fixture}', \App\Livewire\Admin\Result\Create::class)->name('admin.results.create');
    Route::get('/results/edit/{result}', \App\Livewire\Admin\Result\Edit::class)->name('admin.results.edit');

    Route::get('/news', \App\Livewire\Admin\News\Index::class)->name('admin.news.index');
    Route::get('/news/create', \App\Livewire\Admin\News\Create::class)->name('admin.news.create');
    Route::get('/news/edit/{news}', \App\Livewire\Admin\News\Edit::class)->name('admin.news.edit');
    Route::get('/news/{news}', \App\Livewire\Admin\News\Show::class)->name('admin.news.show');

    Route::get('/pages', \App\Livewire\Admin\Page\Index::class)->name('admin.pages.index');
    Route::get('/pages/create', \App\Livewire\Admin\Page\Create::class)->name('admin.pages.create');
    Route::get('/pages/edit/{page}', \App\Livewire\Admin\Page\Edit::class)->name('admin.pages.edit');
    Route::get('/pages/{page}', \App\Livewire\Admin\Page\Show::class)->name('admin.pages.show');

    Route::get('/switch-user/{user}', [\App\Http\Controllers\AdminController::class, 'switchUser'])->name('admin.switch-user');
    Route::get('/switch-back', [\App\Http\Controllers\AdminController::class, 'switchBack'])->name('admin.switch-back');

    Route::get('/knockouts/create/{season}', \App\Livewire\Admin\Knockout\Create::class)->name('admin.knockouts.create');
    Route::get('/knockouts/edit/{knockout}', \App\Livewire\Admin\Knockout\Edit::class)->name('admin.knockouts.edit');
    Route::get('/knockouts/{knockout}', \App\Livewire\Admin\Knockout\Show::class)->name('admin.knockouts.show');

    Route::get('/rounds/create/{knockout}', \App\Livewire\Admin\Round\Create::class)->name('admin.rounds.create');
    Route::get('/rounds/edit/{round}', \App\Livewire\Admin\Round\Edit::class)->name('admin.rounds.edit');
    Route::get('/rounds/{round}', \App\Livewire\Admin\Round\Show::class)->name('admin.rounds.show');

    Route::get('/matches/create/{round}', \App\Livewire\Admin\KnockoutMatch\Create::class)->name('admin.matches.create');
    Route::get('/matches/edit/{match}', \App\Livewire\Admin\KnockoutMatch\Edit::class)->name('admin.matches.edit');
    Route::get('/matches/{match}', \App\Livewire\Admin\KnockoutMatch\Show::class)->name('admin.matches.show');
});