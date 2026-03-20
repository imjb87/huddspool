@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-result-create-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Submit a result</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $fixture->section->name }}</p>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    <section class="py-1" data-result-create-info-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixture details</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Enter the match card below. Frames save automatically while you work.
                                </p>
                            </div>

                            <div class="space-y-5 lg:col-span-2">
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3.5 dark:border-amber-900/60 dark:bg-amber-950/30">
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 pt-0.5">
                                            <svg class="h-5 w-5 text-amber-500 dark:text-amber-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <h2 class="text-sm font-semibold text-amber-900 dark:text-amber-100">Automatic saving in progress</h2>
                                            <div class="mt-1.5 space-y-1.5 text-sm leading-6 text-amber-800 dark:text-amber-200">
                                                <p>Frames are saved automatically as you enter them. When the card is signed off, hit <strong>Submit result</strong> to lock everything in.</p>
                                                @if ($fixture->result && ! $fixture->result->is_confirmed)
                                                    <p>This fixture already has a draft result that was last updated on {{ $fixture->result->updated_at->format('l jS F Y \\a\\t H:i') }}.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fixture->homeTeam->name }} <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                            {{ $fixture->awayTeam->name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $fixture->fixture_date->format('l jS F Y') }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                                        @if ($fixture->venue)
                                            <a class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400"
                                                href="{{ route('venue.show', $fixture->venue->id) }}">
                                                {{ $fixture->venue->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-result-create-form-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Complete each frame exactly as it appears on the card, then submit once everything is signed off.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <livewire:result-form :fixture="$fixture" />
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
