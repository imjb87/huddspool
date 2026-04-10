@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-team-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Teams'],
                    ['label' => $team->name, 'current' => true],
                ]" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <div class="ui-page-title-with-icon">
                                <div class="ui-page-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.742-.478 3 3 0 0 0-4.682-2.72m.94 3.198v-.75A2.25 2.25 0 0 0 15.75 15.72h-7.5A2.25 2.25 0 0 0 6 17.97v.75m12 0a9.094 9.094 0 0 1-12 0m12 0a9.094 9.094 0 0 0-12 0m8.25-10.47a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
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
