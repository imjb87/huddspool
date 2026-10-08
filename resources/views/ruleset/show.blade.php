@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-ruleset-hub>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Rulesets'],
                    ['label' => $ruleset->name, 'current' => true],
                ]" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $ruleset->name }}</h1>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section" data-ruleset-sections>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro gap-2">
                        <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list size-5 text-neutral-700 dark:text-neutral-200">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l11 0" />
                                <path d="M9 12l11 0" />
                                <path d="M9 18l11 0" />
                                <path d="M5 6l0 .01" />
                                <path d="M5 12l0 .01" />
                                <path d="M5 18l0 .01" />
                            </svg>
                        </span>
                        <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                            <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Current sections</h2>
                            <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                Choose a section to view current standings, fixtures, results, and averages for {{ $ruleset->name }}.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        @if ($sections->isEmpty())
                            <div class="ui-card" data-ruleset-sections-empty>
                                <x-ui-empty-state
                                    title="No open sections are available for this ruleset yet."
                                />
                            </div>
                        @else
                            <div class="ui-card ui-section-see-also-card" data-ruleset-sections-list>
                                <div class="ui-section-see-also-list" data-slot="item-group">
                                    @foreach ($sections as $section)
                                        <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                            class="ui-section-see-also-item"
                                            data-slot="item"
                                            data-variant="muted">
                                            <div class="ui-section-see-also-item-content" data-slot="item-content">
                                                <div class="min-w-0">
                                                    <p class="ui-section-see-also-item-title">{{ $section->name }}</p>
                                                    <p class="mt-1 text-xs text-muted-foreground">{{ $section->season->name }}</p>
                                                </div>
                                            </div>
                                            <span class="ui-section-see-also-item-action" data-slot="item-actions" aria-hidden="true">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m9 18 6-6-6-6" />
                                                </svg>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
