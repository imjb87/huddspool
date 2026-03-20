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
                                @php
                                    $seasonKnockouts = collect($group['knockouts'] ?? []);
                                @endphp
                                <section class="border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80" data-history-season-shell>
                                    <button type="button"
                                        class="flex w-full items-center justify-between gap-4 py-4 text-left transition hover:text-gray-700 dark:hover:text-gray-200"
                                        @click="openSeason = openSeason === 'season-{{ $group['season']->id }}' ? null : 'season-{{ $group['season']->id }}'; openRuleset = null"
                                        :aria-expanded="openSeason === 'season-{{ $group['season']->id }}'"
                                        data-history-season-trigger>
                                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $group['season']->name }}</h2>
                                        <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
                                            :class="{ 'rotate-90': openSeason === 'season-{{ $group['season']->id }}' }"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="openSeason === 'season-{{ $group['season']->id }}'" x-cloak class="pb-4"
                                        data-history-season-panel>
                                        @foreach ($group['rulesets'] as $rulesetGroup)
                                            <div class="ml-4 border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80 sm:ml-6"
                                                data-history-ruleset-shell>
                                                <button type="button"
                                                    class="flex w-full items-center justify-between gap-4 py-3 text-left transition hover:text-gray-700 dark:hover:text-gray-200"
                                                    @click="openRuleset = openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' ? null : 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                                    :aria-expanded="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                                    data-history-ruleset-trigger>
                                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $rulesetGroup['ruleset']->name }}</h3>
                                                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
                                                        :class="{ 'rotate-90': openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' }"
                                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div
                                                    x-show="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                                    x-cloak
                                                    class="pb-1"
                                                    data-history-ruleset-panel>
                                                    <div class="ml-4 divide-y divide-gray-200 dark:divide-zinc-800/80 sm:ml-6">
                                                        @foreach ($rulesetGroup['sections'] as $section)
                                                            <a href="{{ route('history.section.show', ['season' => $group['season'], 'ruleset' => $rulesetGroup['ruleset'], 'section' => $section]) }}"
                                                                class="flex items-center justify-between gap-3 py-3 text-sm font-medium text-gray-700 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100"
                                                                data-history-section-link>
                                                                <span>{{ $section->name }}</span>
                                                                <svg class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                    <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                                </svg>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        @if ($seasonKnockouts->isNotEmpty())
                                            <div class="ml-4 border-t border-gray-200 dark:border-zinc-800/80 sm:ml-6"
                                                data-history-knockouts-shell>
                                                <p class="py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    Knockouts
                                                </p>

                                                <div class="ml-4 divide-y divide-gray-200 dark:divide-zinc-800/80 sm:ml-6">
                                                    @foreach ($seasonKnockouts as $knockout)
                                                        <a href="{{ route('knockout.show', $knockout) }}"
                                                            class="flex items-center justify-between gap-3 py-3 text-sm font-medium text-gray-700 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100"
                                                            data-history-knockout-link>
                                                            <span>{{ $knockout->name }}</span>
                                                            <svg class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                            </svg>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </section>
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
