@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-player-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Players'],
                    ['label' => $player->name, 'current' => true],
                ]" />
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $player->name }}</h1>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            @if (session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="space-y-6">
                @include('player.partials.info-section')
                @include('player.partials.knockout-section')
                <livewire:player.frames-section :player="$player" :section="$player->team?->openSection()" />
                <livewire:player.history-section :player="$player" />
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
