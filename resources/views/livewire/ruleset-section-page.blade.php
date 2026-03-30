<div class="ui-page-shell {{ $contentPadding }}">
    <div class="ui-section" data-section-shared-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-end lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $section->season->name }}</p>
                    <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</h1>
                </div>

                @if ($activeTab === 'fixtures-results')
                    <div class="flex justify-end">
                        <a href="{{ route('fixture.download', ['ruleset' => $ruleset, 'section' => $section]) }}"
                            target="_blank"
                            class="ui-button-secondary min-w-24 gap-2"
                            aria-label="Print fixtures">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M5 4.25A2.25 2.25 0 017.25 2h5.5A2.25 2.25 0 0115 4.25V6h.75A2.25 2.25 0 0118 8.25v4.5A2.25 2.25 0 0115.75 15H15v.75A2.25 2.25 0 0112.75 18h-5.5A2.25 2.25 0 014 15.75V15h-.75A2.25 2.25 0 011 12.75v-4.5A2.25 2.25 0 013.25 6H4V4.25zM13.5 6V4.25a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75V6h7zM5.5 14.5v1.25c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75V14.5h-7zm9.5-7H3.25a.75.75 0 00-.75.75v4.5c0 .414.336.75.75.75H4V12a1 1 0 011-1h9a1 1 0 011 1v1.5h.75a.75.75 0 00.75-.75v-4.5a.75.75 0 00-.75-.75z" />
                            </svg>
                            <span>Print</span>
                        </a>
                    </div>
                @else
                    <div aria-hidden="true"></div>
                @endif
            </div>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75"
        data-section-tabs
        data-active-section-tab="{{ $activeTab }}">
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6"
            data-section-tabs-scroll
            data-section-tabs-track>
            <nav class="flex gap-2">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a href="{{ $this->tabUrl($tabKey) }}"
                        wire:click.prevent="setActiveTab('{{ $tabKey }}')"
                        wire:key="section-tab-{{ $tabKey }}"
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
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Other sections in {{ $ruleset->name }}</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Browse the other sections in this ruleset.
                        </p>
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
