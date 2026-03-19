@php
    $contentPadding = in_array($activeTab, ['tables', 'averages'], true) ? 'pb-8 lg:pb-8' : 'pb-10 lg:pb-14';
@endphp

<div class="pt-[72px] {{ $contentPadding }}" data-history-section-page>
    <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
        data-section-shared-header>
        <div class="min-w-0">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $season->name }}</p>
            <h1 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</h1>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75"
        data-section-tabs
        data-active-section-tab="{{ $activeTab }}">
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6"
            data-section-tabs-scroll
            data-section-tabs-track>
            @php($tabs = $this->tabs())
            <nav class="-ml-3 flex gap-2">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <a href="{{ $this->tabUrl($tabKey) }}"
                        wire:click.prevent="setActiveTab('{{ $tabKey }}')"
                        wire:key="history-section-tab-{{ $tabKey }}"
                        data-section-tab="{{ $tabKey }}"
                        @if ($activeTab === $tabKey) aria-current="page" @endif
                        class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold transition data-loading:opacity-60 {{ $activeTab === $tabKey ? 'bg-gray-100 text-gray-700 dark:bg-zinc-700 dark:text-gray-300' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100' }}">
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
                    'history' => true,
                ])
            @elseif ($activeTab === 'fixtures-results')
                @include('livewire.section-fixtures', [
                    'section' => $section,
                    'fixtures' => $this->fixtures,
                    'week' => $week,
                    'history' => true,
                ])
            @else
                @include('livewire.section-averages', [
                    'section' => $section,
                    'players' => $this->players,
                    'page' => $page,
                    'perPage' => $perPage,
                    'history' => true,
                ])
            @endif
        </div>
    </div>

    @if ($this->relatedSections->isNotEmpty())
        <section class="mx-auto mt-10 w-full max-w-4xl border-t border-gray-200 px-4 pt-6 dark:border-zinc-800/80 sm:px-6 sm:pt-8 lg:px-6" data-section-see-also>
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                <div class="space-y-2">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Other sections in {{ $ruleset->name }}</h2>
                    <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Browse the other sections in this archived ruleset.
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <ul class="text-base leading-6 text-gray-700 dark:text-gray-300 [overflow-wrap:normal] [word-break:normal]" data-section-see-also-links>
                        @foreach ($this->relatedSections as $relatedSection)
                            <li class="inline">
                                <a href="{{ $this->sectionUrl($relatedSection) }}"
                                    class="font-semibold underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                    {{ $relatedSection->name }}
                                </a>
                                @unless ($loop->last)
                                    <span class="mx-2 text-gray-300 dark:text-zinc-600" aria-hidden="true">/</span>
                                @endunless
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif
</div>
