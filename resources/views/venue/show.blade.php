@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px]">
        <div class="pb-10 lg:pb-14" data-venue-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900">{{ $venue->name }}</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    <section class="py-1" data-venue-info-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">Venue information</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Contact details and location information for this venue.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <p class="text-sm font-medium text-gray-500">Address</p>
                                        <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $venue->address }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Telephone</p>
                                        @if ($venue->telephone)
                                            <a href="tel:{{ $venue->telephone }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                                {{ $venue->telephone }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900">Not listed</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6" data-venue-teams-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">Teams</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Active teams currently playing out of this venue in the open season.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200">
                                    @forelse ($teams as $team)
                                        @php
                                            $openSection = $team->openSection();
                                        @endphp
                                        <a href="{{ route('team.show', $team) }}"
                                            class="block transition hover:bg-gray-50"
                                            wire:key="venue-team-{{ $team->id }}">
                                            <div class="flex items-start justify-between gap-4 py-4">
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $team->name }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ $openSection?->name ?? 'Section TBC' }}
                                                    </p>
                                                </div>

                                                @if ($team->captain)
                                                    <div class="shrink-0 text-right">
                                                        <p class="text-xs font-medium text-gray-500">Captain</p>
                                                        <p class="mt-1 text-sm font-semibold text-gray-700">{{ $team->captain->name }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    @empty
                                        <div class="py-6">
                                            <p class="text-sm text-gray-500">No active teams for the current season.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6" data-venue-map-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">Map</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Find the venue using the embedded map.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                                    <iframe class="h-[360px] w-full"
                                        src="https://www.google.com/maps/embed/v1/place?q={{ urlencode($venue->address) }}&key={{ config('services.google_maps.embed_key') }}"
                                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
