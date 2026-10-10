<div class="ui-page-shell {{ $contentPadding }}">
    <div class="ui-section" data-section-shared-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <x-ui-breadcrumb class="mb-3" :items="[
                ['label' => 'Rulesets'],
                ['label' => $ruleset->name, 'url' => route('ruleset.show', $ruleset)],
                ['label' => $section->name, 'current' => true],
            ]" />
            <div class="ui-shell-grid">
                <div class="min-w-0 lg:col-span-2">
                    <div class="min-w-0">
                        <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $section->name }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-muted dark:bg-card text-card-foreground ring-1 ring-foreground/10"
        data-section-tabs
        data-active-section-tab="{{ $activeTab }}">
        <div class="ui-tab-strip-shell"
            data-section-tabs-scroll
            data-section-tabs-track
            tabindex="0"
            aria-label="Section tabs">
            <nav class="ui-tab-strip" aria-label="Section tabs">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a href="{{ $this->tabUrl($tabKey) }}"
                        wire:click.prevent="setActiveTab('{{ $tabKey }}')"
                        wire:key="section-tab-{{ $tabKey }}"
                        data-section-tab="{{ $tabKey }}"
                        data-state="{{ $activeTab === $tabKey ? 'active' : 'inactive' }}"
                        @if ($activeTab === $tabKey) aria-current="page" @endif
                        class="ui-tab-trigger shrink-0 snap-start">
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    <div wire:loading.grid
        wire:target="setActiveTab('tables')"
        class="gap-0"
        data-section-tab-skeleton>
        @include('ruleset.partials.tab-skeleton-tables')
    </div>

    <div wire:loading.grid
        wire:target="setActiveTab('fixtures-results')"
        class="gap-0"
        data-section-tab-skeleton>
        @include('ruleset.partials.tab-skeleton-fixtures-results')
    </div>

    <div wire:loading.grid
        wire:target="setActiveTab('averages')"
        class="gap-0"
        data-section-tab-skeleton>
        @include('ruleset.partials.tab-skeleton-averages')
    </div>

    <div wire:loading.remove
        wire:target="setActiveTab"
        data-ruleset-active-panel="{{ $activeTab }}">
        <div wire:key="section-active-panel-{{ $activeTab }}">
            @if ($activeTab === 'tables')
                @include('livewire.standings.show', [
                    'section' => $section,
                    'standings' => $this->standings,
                    'standingRows' => $standingRows,
                    'summaryCopy' => $standingsSummaryCopy,
                ])
            @elseif ($activeTab === 'fixtures-results')
                @include('livewire.section-fixtures', [
                    'section' => $section,
                    'fixtures' => $this->fixtures,
                    'fixtureRows' => $fixtureRows,
                    'week' => $week,
                    'showPrint' => true,
                ])
            @else
                @include('livewire.section-averages', [
                    'section' => $section,
                    'players' => $this->players,
                    'page' => $page,
                    'perPage' => $perPage,
                    'totalPlayers' => $this->totalPlayers,
                    'averageRows' => $averageRows,
                    'averageSummaryCopy' => $averageSummaryCopy,
                    'lastPage' => $lastPage,
                ])
            @endif
        </div>
    </div>

    @if ($this->relatedSections->isNotEmpty())
        <section class="ui-section" data-section-see-also>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid">
                    <div class="ui-section-intro gap-2">
                        <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-search size-5 text-neutral-700 dark:text-neutral-200">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11 15a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M18.5 18.5l2.5 2.5" />
                                <path d="M4 6h16" />
                                <path d="M4 12h4" />
                                <path d="M4 18h4" />
                            </svg>
                        </span>
                        <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                            <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Other sections in {{ $ruleset->name }}</h2>
                            <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                Switch sections to compare standings, fixtures, results, and averages.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card ui-section-see-also-card" data-section-see-also-links>
                            <div class="ui-section-see-also-list" data-slot="item-group">
                                @foreach ($this->relatedSections as $relatedSection)
                                    <a href="{{ $this->sectionUrl($relatedSection) }}" class="ui-section-see-also-item" data-slot="item" data-variant="muted" data-size="sm">
                                        <div class="ui-section-see-also-item-content" data-slot="item-content">
                                            <p class="ui-section-see-also-item-title" data-slot="item-title">
                                                {{ $relatedSection->name }}
                                            </p>
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
                    </div>
                </div>
            </div>
        </section>
    @endif

    <x-logo-clouds />
</div>
