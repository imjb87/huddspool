@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-team-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Teams'],
                    ['label' => $team->name, 'current' => true],
                ]" />
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $team->name }}</h1>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                @include('team.partials.info-section')
                <livewire:team.players-section :team="$team" :section="$section" />
                <livewire:team.fixtures-section :team="$team" :section="$section" :show-submission-actions="true" />
                @include('team.partials.knockout-section')
                <livewire:team.history-section :team="$team" :current-section="$section" />
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
