<div class="ui-page-shell {{ $contentPadding }}" data-history-section-page>
    <div class="ui-section" data-section-shared-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <div class="ui-page-title-with-icon">
                        <div class="ui-page-title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25m6-2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $season->name }}</p>
                            <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</h1>
                        </div>
                    </div>
                </div>

                <div aria-hidden="true"></div>
            </div>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white dark:border-neutral-800/80 dark:bg-neutral-900/75"
        data-section-tabs
        data-active-section-tab="{{ $activeTab }}">
        <div class="ui-tab-strip-shell"
            data-section-tabs-scroll
            data-section-tabs-track>
            <nav class="ui-tab-strip">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a href="{{ $this->tabUrl($tabKey) }}"
                        wire:click.prevent="setActiveTab('{{ $tabKey }}')"
                        wire:key="history-section-tab-{{ $tabKey }}"
                        data-section-tab="{{ $tabKey }}"
                        @if ($activeTab === $tabKey) aria-current="page" @endif
                        class="{{ $activeTab === $tabKey ? 'ui-button-primary' : 'ui-button-secondary' }} shrink-0 data-loading:opacity-60">
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    <div wire:loading.grid
        wire:target="setActiveTab('tables')"
        class="gap-0"
        data-section-tab-skeleton="tables">
        @include('ruleset.partials.tab-skeleton-tables')
    </div>

    <div wire:loading.grid
        wire:target="setActiveTab('fixtures-results')"
        class="gap-0"
        data-section-tab-skeleton="fixtures-results">
        @include('ruleset.partials.tab-skeleton-fixtures-results')
    </div>

    <div wire:loading.grid
        wire:target="setActiveTab('averages')"
        class="gap-0"
        data-section-tab-skeleton="averages">
        @include('ruleset.partials.tab-skeleton-averages')
    </div>

    <div wire:loading.remove
        wire:target="setActiveTab"
        data-ruleset-active-panel="{{ $activeTab }}">
        <div wire:key="history-section-active-panel-{{ $activeTab }}">
            @if ($activeTab === 'tables')
                @include('livewire.standings.show', [
                    'section' => $section,
                    'standings' => $this->standings,
                    'standingRows' => $standingRows,
                    'summaryCopy' => $standingsSummaryCopy,
                    'history' => true,
                ])
            @elseif ($activeTab === 'fixtures-results')
                @include('livewire.section-fixtures', [
                    'section' => $section,
                    'fixtures' => $this->fixtures,
                    'fixtureRows' => $fixtureRows,
                    'week' => $week,
                    'history' => true,
                ])
            @else
                @include('livewire.section-averages', [
                    'section' => $section,
                    'players' => $this->players,
                    'page' => $page,
                    'perPage' => $perPage,
                    'totalPlayers' => $this->totalPlayers,
                    'history' => true,
                    'averageRows' => $averageRows,
                    'averageSummaryCopy' => $averageSummaryCopy,
                    'lastPage' => $lastPage,
                ])
            @endif
        </div>
    </div>

    @if ($this->relatedSections->isNotEmpty())
        <section class="ui-section mt-10 border-t border-gray-200 pt-6 dark:border-neutral-800/80 sm:pt-8" data-section-see-also>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid">
                    <div class="ui-section-intro">
                        <div class="ui-section-intro-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12m-12 6.003H20.24m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 1 1 1.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 0 1 0 2.25H3.74m0-.002h.375a1.125 1.125 0 0 1 0 2.25H2.99" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Other sections in {{ $ruleset->name }}</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Browse the other sections in this archived ruleset.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card" data-section-see-also-links>
                            <div class="ui-card-rows">
                                @foreach ($this->relatedSections as $relatedSection)
                                    <a href="{{ $this->sectionUrl($relatedSection) }}" class="ui-card-row-link">
                                        <div class="ui-card-row">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $relatedSection->name }}
                                            </p>
                                            <svg class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06L8.28 14.53a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
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
