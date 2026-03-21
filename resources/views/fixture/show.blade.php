@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-fixture-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fixture</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $fixture->section->name }}</p>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    @include('fixture.partials.info-section')
                    @include('fixture.partials.head-to-head-section')
                    @include('fixture.partials.team-section', [
                        'title' => $fixture->homeTeam->name,
                        'players' => $home_team_players,
                        'sectionKey' => 'fixture-home-team-section',
                    ])
                    @include('fixture.partials.team-section', [
                        'title' => $fixture->awayTeam->name,
                        'players' => $away_team_players,
                        'sectionKey' => 'fixture-away-team-section',
                    ])
                </div>
            </div>
        </div>

        <x-logo-clouds  />
    </div>
@endsection
