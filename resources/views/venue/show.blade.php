@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-venue-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Venues'],
                    ['label' => $venue->name, 'current' => true],
                ]" />
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $venue->name }}</h1>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                @include('venue.partials.info-section')
                @include('venue.partials.teams-section')
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
