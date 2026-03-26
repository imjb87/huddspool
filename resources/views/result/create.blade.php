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
                                    Review the fixture details before entering the result.
                                </p>
                            </div>

                            <div class="space-y-5 lg:col-span-2">
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
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Enter result</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Complete each frame accurately, then submit the result when you're ready.
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
