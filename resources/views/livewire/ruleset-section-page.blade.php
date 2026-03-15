@php
    $contentPadding = in_array($activeTab, ['tables', 'averages'], true) ? 'pb-8 lg:pb-8' : 'pb-10 lg:pb-14';
@endphp

<div class="pt-[72px] {{ $contentPadding }}">
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
        <div class="mx-auto max-w-7xl px-4 py-3 lg:px-8">
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
                            wire:key="section-tab-{{ $tabKey }}"
                            data-section-tab="{{ $tabKey }}"
                            @if ($activeTab === $tabKey) aria-current="page" @endif
                            class="inline-flex min-w-0 w-full items-center justify-center rounded-full px-3 py-2 text-center text-[13px] font-semibold whitespace-nowrap transition sm:px-4 sm:text-sm data-loading:opacity-60 {{ $activeTab === $tabKey ? 'text-green-900' : 'text-white hover:bg-white/16 active:bg-white/20' }}">
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
        <h1 class="text-lg font-semibold text-gray-900">{{ $section->name }}</h1>

        @if ($activeTab === 'fixtures-results')
            <a href="{{ route('fixture.download', $section) }}"
                target="_blank"
                class="inline-flex items-center text-gray-700 hover:text-green-800"
                aria-label="Print fixtures">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M5 4.25A2.25 2.25 0 017.25 2h5.5A2.25 2.25 0 0115 4.25V6h.75A2.25 2.25 0 0118 8.25v4.5A2.25 2.25 0 0115.75 15H15v.75A2.25 2.25 0 0112.75 18h-5.5A2.25 2.25 0 014 15.75V15h-.75A2.25 2.25 0 011 12.75v-4.5A2.25 2.25 0 013.25 6H4V4.25zM13.5 6V4.25a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75V6h7zM5.5 14.5v1.25c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75V14.5h-7zm9.5-7H3.25a.75.75 0 00-.75.75v4.5c0 .414.336.75.75.75H4V12a1 1 0 011-1h9a1 1 0 011 1v1.5h.75a.75.75 0 00.75-.75v-4.5a.75.75 0 00-.75-.75z" />
                </svg>
            </a>
        @endif
    </div>

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
                ])
            @elseif ($activeTab === 'fixtures-results')
                @include('livewire.section-fixtures', [
                    'section' => $section,
                    'fixtures' => $this->fixtures,
                    'week' => $week,
                ])
            @else
                @include('livewire.section-averages', [
                    'section' => $section,
                    'players' => $this->players,
                    'page' => $page,
                    'perPage' => $perPage,
                ])
            @endif
        </div>
    </div>
</div>
