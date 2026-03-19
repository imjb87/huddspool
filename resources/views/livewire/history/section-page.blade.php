@php
    $contentPadding = in_array($activeTab, ['tables', 'averages'], true) ? 'pb-8 lg:pb-8' : 'pb-10 lg:pb-14';
@endphp

<div class="pt-[72px] {{ $contentPadding }}" data-history-section-page>
    <section class="sticky top-[72px] z-30 bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl"
        data-section-tabs
        data-active-section-tab="{{ $activeTab }}"
        x-data="{
            activeTab: @js($activeTab),
            indicatorStyle: '',
            indicatorVisible: false,
            syncIndicator(tabKey = null) {
                if (tabKey) {
                    this.activeTab = tabKey;
                }

                this.$nextTick(() => {
                    const activeItem = this.$refs.track?.querySelector(`[data-section-tab-item='${this.activeTab}']`);

                    if (! activeItem || ! this.$refs.track) {
                        return;
                    }

                    this.indicatorStyle = `width: ${activeItem.offsetWidth}px; transform: translateX(${activeItem.offsetLeft}px);`;
                    this.indicatorVisible = true;
                });
            },
        }"
        x-init="syncIndicator()"
        @resize.window="syncIndicator()">
        <div class="mx-auto max-w-7xl px-2.5 py-3 lg:px-8">
            @php($tabs = $this->tabs())
            <div class="mx-auto w-full max-w-2xl rounded-full bg-black/15 p-1.5 shadow-[inset_0_1px_0_rgba(255,255,255,0.12),inset_0_-1px_0_rgba(5,46,22,0.35)]"
                data-section-tabs-scroll
                data-section-tabs-track>
                <div class="relative grid w-full grid-cols-3 gap-2" x-ref="track">
                    <div class="absolute inset-y-0 left-0 rounded-full bg-linear-to-b from-white/90 via-white/70 to-white/50 backdrop-blur-xl shadow-[inset_0_1px_0_rgba(255,255,255,0.32),inset_0_-1px_0_rgba(255,255,255,0.2),0_12px_28px_rgba(5,46,22,0.22)] transition-transform duration-300 ease-out"
                        data-section-tab-indicator
                        x-cloak
                        x-show="indicatorVisible"
                        :style="indicatorStyle"></div>
                    @foreach ($tabs as $tabKey => $tabLabel)
                        <div class="relative z-10 min-w-0" data-section-tab-item="{{ $tabKey }}">
                            <a href="{{ $this->tabUrl($tabKey) }}"
                                wire:click.prevent="setActiveTab('{{ $tabKey }}')"
                                @click="syncIndicator('{{ $tabKey }}')"
                                wire:key="history-section-tab-{{ $tabKey }}"
                                data-section-tab="{{ $tabKey }}"
                                @if ($activeTab === $tabKey) aria-current="page" @endif
                                class="inline-flex min-w-0 w-full items-center justify-center rounded-full px-3 py-2 text-center text-[13px] font-semibold whitespace-nowrap transition sm:px-4 sm:text-sm data-loading:opacity-60 {{ $activeTab === $tabKey ? 'text-shadow-xs/20 text-shadow-green-950/30' : 'text-gray-300 hover:text-gray-100' }}">
                                <span class="leading-tight {{ $activeTab === $tabKey ? 'text-shadow-xs/20 text-shadow-green-950/30' : '' }}">{{ $tabLabel }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
        data-section-shared-header>
        <div class="min-w-0">
            <p class="text-sm text-gray-500">{{ $season->name }}</p>
            <h1 class="mt-1 text-lg font-semibold text-gray-900">{{ $section->name }}</h1>
        </div>
    </div>

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
        <section class="mx-auto mt-10 w-full max-w-4xl border-t border-gray-200 px-4 pt-6 sm:px-6 sm:pt-8 lg:px-6" data-section-see-also>
            <div class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Other sections in {{ $ruleset->name }}</h2>
                <p class="-mt-3 text-sm leading-6 text-gray-500">
                    Browse the other sections in this archived ruleset.
                </p>
                <ul class="text-base leading-6 text-gray-700" data-section-see-also-links>
                    @foreach ($this->relatedSections as $relatedSection)
                        <li class="inline">
                            <a href="{{ $this->sectionUrl($relatedSection) }}"
                                class="font-semibold underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                {{ $relatedSection->name }}
                            </a>
                            @unless ($loop->last)
                                <span class="mx-2 text-gray-300" aria-hidden="true">/</span>
                            @endunless
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</div>
