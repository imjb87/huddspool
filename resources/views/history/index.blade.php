@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
            <div class="mx-auto max-w-4xl pb-6 sm:pb-8" data-section-shared-header>
                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">History</h1>
            </div>

            <div class="mx-auto max-w-4xl" data-history-index-accordion
                x-data="{ openSeason: null, openRuleset: null }">
                @forelse ($seasonGroups as $group)
                    <section data-history-season-shell>
                        <button type="button"
                            class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-md px-2 py-2 text-left transition hover:bg-gray-100 dark:hover:bg-zinc-800/70"
                            @click="openSeason = openSeason === 'season-{{ $group['season']->id }}' ? null : 'season-{{ $group['season']->id }}'; openRuleset = null"
                            :aria-expanded="openSeason === 'season-{{ $group['season']->id }}'"
                            data-history-season-trigger>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $group['season']->name }}</h2>
                            <svg class="h-5 w-5 text-gray-400 transition-transform dark:text-gray-500"
                                :class="{ 'rotate-90': openSeason === 'season-{{ $group['season']->id }}' }"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="openSeason === 'season-{{ $group['season']->id }}'" x-cloak class="pl-4 pb-3 sm:pl-6 sm:pb-4" data-history-season-panel>
                            <div>
                                @foreach ($group['rulesets'] as $rulesetGroup)
                                    <div data-history-ruleset-shell>
                                        <button type="button"
                                            class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-md px-2 py-2 text-left transition hover:bg-gray-100 dark:hover:bg-zinc-800/70"
                                            @click="openRuleset = openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' ? null : 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                            :aria-expanded="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                            data-history-ruleset-trigger>
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $rulesetGroup['ruleset']->name }}</h3>
                                            <svg class="h-5 w-5 text-gray-400 transition-transform dark:text-gray-500"
                                                :class="{ 'rotate-90': openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' }"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div
                                            x-show="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
                                            x-cloak
                                            class="pb-2 sm:pb-3"
                                            data-history-ruleset-panel>
                                            @foreach ($rulesetGroup['sections'] as $section)
                                                <a href="{{ route('history.section.show', ['season' => $group['season'], 'ruleset' => $rulesetGroup['ruleset'], 'section' => $section]) }}"
                                                    class="flex items-center justify-between gap-3 rounded-md px-2 py-2 pl-4 text-base font-medium text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100"
                                                    data-history-section-link>
                                                    <span>{{ $section->name }}</span>
                                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900/75 dark:text-gray-400">
                        No historical seasons are available yet.
                    </div>
                @endforelse
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
