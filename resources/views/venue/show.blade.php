@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-venue-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Venue</p>
                        <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $venue->name }}</h1>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                @include('venue.partials.info-section')
                @include('venue.partials.teams-section')
                @include('venue.partials.map-section')
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
