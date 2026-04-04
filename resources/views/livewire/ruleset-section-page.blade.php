<div class="ui-page-shell {{ $contentPadding }}">
    <div class="ui-section" data-section-shared-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <x-ui-breadcrumb class="mb-3" :items="[
                ['label' => 'Rulesets'],
                ['label' => $ruleset->name, 'url' => route('ruleset.show', $ruleset)],
                ['label' => $section->name, 'current' => true],
            ]" />
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-end lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <div class="min-w-0">
                        <div class="ui-page-title-with-icon">
                            <div class="ui-page-title-icon" data-section-header-icon>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</h1>
                            </div>
                        </div>
                    </div>
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

    <section class="bg-white ring-1 ring-neutral-950/5 dark:bg-neutral-900 dark:ring-neutral-100/10"
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
                        @if ($activeTab === $tabKey) aria-current="page" @endif
                        class="{{ $activeTab === $tabKey ? 'ui-button-primary' : 'ui-button-secondary' }} shrink-0 snap-start data-loading:opacity-60">
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
                    <div class="ui-section-intro">
                        <div class="ui-section-intro-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12m-12 6.003H20.24m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 1 1 1.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 0 1 0 2.25H3.74m0-.002h.375a1.125 1.125 0 0 1 0 2.25H2.99" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Other sections in {{ $ruleset->name }}</h2>
                            <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Browse the other sections in this ruleset.
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
