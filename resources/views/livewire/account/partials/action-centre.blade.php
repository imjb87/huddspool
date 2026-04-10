@if ($this->submissionActions)
    <section class="ui-section" data-account-action-centre>
        <div class="ui-shell-grid">
            <div class="ui-section-intro">
                <div class="ui-section-intro-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="ui-section-intro-copy">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Action centre</h3>
                    <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Keep track of the things that currently need your attention.
                    </p>
                </div>
            </div>

            <div class="space-y-4 lg:col-span-2">
                @if (filled($this->submissionActions['fixtures_heading']))
                    <div class="ui-card border-red-200 bg-red-50 dark:border-red-900/60 dark:bg-red-950/20">
                        <div class="px-4 py-3 sm:px-5">
                            <p class="text-xs font-medium text-red-800 dark:text-red-200">
                                {{ count($this->submissionActions['fixtures']) }} {{ \Illuminate\Support\Str::plural('league match', count($this->submissionActions['fixtures'])) }} need{{ count($this->submissionActions['fixtures']) === 1 ? 's' : '' }} submitting
                            </p>
                        </div>

                        <div class="ui-card-rows">
                            @foreach ($this->submissionActions['fixtures'] as $fixture)
                                <div wire:key="account-action-fixture-{{ md5($fixture['url']) }}">
                                    <a href="{{ $fixture['url'] }}" class="ui-card-row-link">
                                        <div class="ui-card-row items-start px-4 sm:items-center sm:px-5">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                                                    {{ $fixture['label'] }}
                                                </p>
                                                <p class="mt-1 text-xs leading-5 text-red-700 dark:text-red-300">
                                                    {{ $fixture['date_label'] }}
                                                </p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center text-right">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-red-700 dark:text-red-300" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (filled($this->submissionActions['knockouts_heading']))
                    <div class="ui-card border-red-200 bg-red-50 dark:border-red-900/60 dark:bg-red-950/20">
                        <div class="px-4 py-3 sm:px-5">
                            <p class="text-xs font-medium text-red-800 dark:text-red-200">
                                {{ count($this->submissionActions['knockouts']) }} {{ \Illuminate\Support\Str::plural('knockout result', count($this->submissionActions['knockouts'])) }} need{{ count($this->submissionActions['knockouts']) === 1 ? 's' : '' }} submitting
                            </p>
                        </div>

                        <div class="ui-card-rows">
                            @foreach ($this->submissionActions['knockouts'] as $knockout)
                                <div wire:key="account-action-knockout-{{ md5($knockout['url']) }}">
                                    <a href="{{ $knockout['url'] }}" class="ui-card-row-link">
                                        <div class="ui-card-row items-start px-4 sm:items-center sm:px-5">
                                            <div class="min-w-0 flex-1">
                                                <p class="mb-1 text-xs leading-5 text-red-700 dark:text-red-300">
                                                    {{ $knockout['knockout_name'] }} / {{ $knockout['round_name'] }}
                                                </p>
                                                <p class="[overflow-wrap:anywhere] text-sm font-semibold leading-5 text-red-800 dark:text-red-200">
                                                    {{ $knockout['participants_label'] }}
                                                </p>
                                                <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-red-700 dark:text-red-300">
                                                    Venue: {{ $knockout['venue_label'] }}
                                                </p>
                                                <p class="[overflow-wrap:anywhere] text-xs leading-5 text-red-700 dark:text-red-300">
                                                    {{ $knockout['date_label'] }}
                                                </p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center text-right">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-red-700 dark:text-red-300" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5" />
                                                </svg>
                                            </div>
                                        </div>
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
