@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-player-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                @if (session('status'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-6">
                    @include('player.partials.info-section')
                    @include('player.partials.knockout-section')
                    @include('player.partials.frames-section')
                    @include('player.partials.history-section')
                </div>
            </div>
        </div>

        <x-logo-clouds  />
    </div>
@endsection
