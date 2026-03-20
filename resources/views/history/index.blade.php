@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14">
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">History</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <section class="py-1">
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Season archive</h2>
                            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Browse past seasons, then drill into each ruleset and section for archived standings, fixtures, results, and averages.
                            </p>
                        </div>

                        <div class="lg:col-span-2" data-history-index-accordion
                            x-data="{ openSeason: null, openRuleset: null }">
                            @forelse ($seasonGroups as $group)
                                @include('history.partials.season-group')
                            @empty
                                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-800/75 dark:text-gray-400">
                                    No historical seasons are available yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <x-logo-clouds  />
    </div>
@endsection
