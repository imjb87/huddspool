@if ($this->submissionActions)
    <section class="ui-section" data-account-action-centre>
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-check size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3.5 5.5l1.5 1.5l3 -3" />
                        <path d="M3.5 12.5l1.5 1.5l3 -3" />
                        <path d="M3.5 19.5l1.5 1.5l3 -3" />
                        <path d="M11 6l9 0" />
                        <path d="M11 12l9 0" />
                        <path d="M11 18l9 0" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Action centre</h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        Start with the fixtures and knockout results that still need submitting.
                    </p>
                </div>
            </div>

            <div class="space-y-3 lg:col-span-2">
                @if (filled($this->submissionActions['fixtures_heading']))
                    <div class="ui-account-action-card" data-account-action-fixtures>
                        <div class="flex items-center gap-3 border-b border-gray-200/80 px-4 py-3 sm:px-5 dark:border-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-alert-circle size-4 text-amber-800 dark:text-amber-200" aria-hidden="true">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 8v4" />
                                <path d="M12 16h.01" />
                            </svg>
                            <p class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                {{ count($this->submissionActions['fixtures']) }} {{ \Illuminate\Support\Str::plural('league match', count($this->submissionActions['fixtures'])) }} need{{ count($this->submissionActions['fixtures']) === 1 ? 's' : '' }} submitting
                            </p>
                        </div>

                        <div class="flex flex-col gap-2 p-3 sm:p-4">
                            @foreach ($this->submissionActions['fixtures'] as $fixture)
                                <div wire:key="account-action-fixture-{{ md5($fixture['url']) }}">
                                    <a href="{{ $fixture['url'] }}" class="ui-account-action-item group">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $fixture['label'] }}
                                            </p>
                                            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                {{ $fixture['date_label'] }}
                                            </p>
                                        </div>

                                        <span class="shrink-0 text-gray-500 transition-transform duration-100 group-hover:translate-x-0.5 dark:text-gray-400" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 6l6 6l-6 6" />
                                            </svg>
                                        </span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (filled($this->submissionActions['knockouts_heading']))
                    <div class="ui-account-action-card" data-account-action-knockouts>
                        <div class="flex items-center gap-3 border-b border-gray-200/80 px-4 py-3 sm:px-5 dark:border-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-alert-circle size-4 text-amber-800 dark:text-amber-200" aria-hidden="true">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 8v4" />
                                <path d="M12 16h.01" />
                            </svg>
                            <p class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                {{ count($this->submissionActions['knockouts']) }} {{ \Illuminate\Support\Str::plural('knockout result', count($this->submissionActions['knockouts'])) }} need{{ count($this->submissionActions['knockouts']) === 1 ? 's' : '' }} submitting
                            </p>
                        </div>

                        <div class="flex flex-col gap-2 p-3 sm:p-4">
                            @foreach ($this->submissionActions['knockouts'] as $knockout)
                                <div wire:key="account-action-knockout-{{ md5($knockout['url']) }}">
                                    <a href="{{ $knockout['url'] }}" class="ui-account-action-item group">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                {{ $knockout['knockout_name'] }} / {{ $knockout['round_name'] }}
                                            </p>
                                            <p class="[overflow-wrap:anywhere] text-sm font-medium leading-5 text-gray-900 dark:text-gray-100">
                                                {{ $knockout['participants_label'] }}
                                            </p>
                                            <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                Venue: {{ $knockout['venue_label'] }}
                                            </p>
                                            <p class="[overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                {{ $knockout['date_label'] }}
                                            </p>
                                        </div>

                                        <span class="shrink-0 text-gray-500 transition-transform duration-100 group-hover:translate-x-0.5 dark:text-gray-400" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 6l6 6l-6 6" />
                                            </svg>
                                        </span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
