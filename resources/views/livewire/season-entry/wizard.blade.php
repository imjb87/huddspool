@php
    $inputClasses = 'mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20';
    $sectionHeadingClasses = 'text-sm font-semibold text-gray-900 dark:text-gray-100';
    $sectionBodyClasses = 'max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400';
    $buttonClasses = 'inline-flex items-center rounded-full bg-green-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-500';
    $secondaryButtonClasses = 'inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-200 dark:hover:bg-neutral-900';
@endphp

<div class="pb-10 lg:pb-14 dark:bg-neutral-950" data-season-entry-page>
    <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4">
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Season registration</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $season->name }}</p>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white dark:border-neutral-800/80 dark:bg-neutral-900/75">
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="-ml-3 flex gap-2">
                @foreach ($steps as $wizardStep)
                    <button
                        type="button"
                        wire:click="goToStep('{{ $wizardStep }}')"
                        @disabled(! $this->canAccessStep($wizardStep))
                        class="{{ $step === $wizardStep ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : 'text-gray-600 hover:bg-gray-200/70 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-neutral-800 dark:hover:text-gray-100' }} inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-gray-600 dark:disabled:hover:text-gray-300"
                    >
                        {{ ucfirst($wizardStep) }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
        @if (! $season->acceptingEntries())
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-4 text-sm text-yellow-900 dark:border-yellow-900/60 dark:bg-yellow-950/40 dark:text-yellow-200">
                Sign-ups for {{ $season->name }} are currently closed.
            </div>
        @else
            <div class="space-y-6">
                <section wire:key="season-entry-step-{{ $step }}" wire:transition.opacity.duration.300ms class="pt-6">
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            @if ($step === 'details')
                                <h2 class="{{ $sectionHeadingClasses }}">Contact details</h2>
                                <p class="{{ $sectionBodyClasses }}">Enter the main contact details for this registration.</p>
                            @elseif ($step === 'venue')
                                <h2 class="{{ $sectionHeadingClasses }}">Venue</h2>
                                <p class="{{ $sectionBodyClasses }}">Enter the venue details for this registration.</p>
                            @elseif ($step === 'teams')
                                <h2 class="{{ $sectionHeadingClasses }}">Teams</h2>
                                <p class="{{ $sectionBodyClasses }}">Add each team and choose its first and second ruleset options.</p>
                            @elseif ($step === 'knockouts')
                                <h2 class="{{ $sectionHeadingClasses }}">Knockouts</h2>
                                <p class="{{ $sectionBodyClasses }}">Add any knockout entries you want to include in this registration.</p>
                            @else
                                <h2 class="{{ $sectionHeadingClasses }}">Review</h2>
                                <p class="{{ $sectionBodyClasses }}">Review the details and total before you confirm the registration.</p>
                                <div class="mt-4 max-w-sm space-y-2 text-sm leading-6 text-amber-700 dark:text-amber-300">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10A8 8 0 11.001 10 8 8 0 0118 10zm-8.75-3.75a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0v-4zM10 13a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                    </svg>
                                    <p>Make sure everything is correct and spelled properly, as incorrect information may delay or prevent your registration.</p>
                                </div>
                            @endif
                        </div>

                        <div class="lg:col-span-2">
                            @if ($step === 'details')
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <input wire:model.live="contact.name" type="text" class="{{ $inputClasses }}">
                                        @error('contact.name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <input wire:model.live="contact.email" type="email" class="{{ $inputClasses }}">
                                        @error('contact.email') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Telephone</label>
                                        <input wire:model.live="contact.telephone" type="text" class="{{ $inputClasses }}">
                                        @error('contact.telephone') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                        <textarea wire:model.live="contact.notes" rows="4" class="{{ $inputClasses }}"></textarea>
                                        @error('contact.notes') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @elseif ($step === 'venue')
                                <div class="space-y-6">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Venue name</label>
                                            <input wire:model.live="registrationVenue.venue_name" type="text" class="{{ $inputClasses }}">
                                            @error('registrationVenue.venue_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Venue address</label>
                                            <textarea wire:model.live="registrationVenue.venue_address" rows="3" class="{{ $inputClasses }}"></textarea>
                                            @error('registrationVenue.venue_address') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Venue telephone</label>
                                            <input wire:model.live="registrationVenue.venue_telephone" type="text" class="{{ $inputClasses }}">
                                            @error('registrationVenue.venue_telephone') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            @elseif ($step === 'teams')
                                <div class="space-y-8">
                                    <div class="space-y-6">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="sm:col-span-2">
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Team name</label>
                                                <input wire:model.live="teamDraft.team_name" type="text" class="{{ $inputClasses }}">
                                                @error('teamDraft.team_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Contact name</label>
                                                <input wire:model.live="teamDraft.contact_name" type="text" class="{{ $inputClasses }}">
                                                @error('teamDraft.contact_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Contact telephone</label>
                                                <input wire:model.live="teamDraft.contact_telephone" type="text" class="{{ $inputClasses }}">
                                                @error('teamDraft.contact_telephone') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">First ruleset option</label>
                                                <select wire:model.live="teamDraft.ruleset_id" class="{{ $inputClasses }}">
                                                    <option value="">Select a ruleset</option>
                                                    @foreach ($availableRulesets as $ruleset)
                                                        <option value="{{ $ruleset->id }}">{{ $ruleset->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('teamDraft.ruleset_id') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Second ruleset option</label>
                                                <select wire:model.live="teamDraft.second_ruleset_id" class="{{ $inputClasses }}">
                                                    <option value="">Select a second ruleset</option>
                                                    @foreach ($availableRulesets as $ruleset)
                                                        <option value="{{ $ruleset->id }}">{{ $ruleset->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('teamDraft.second_ruleset_id') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        <div class="flex justify-start">
                                            <button type="button" wire:click="addTeamRegistration" class="{{ $buttonClasses }}">
                                                Add team
                                            </button>
                                        </div>
                                    </div>

                                    <div class="divide-y divide-gray-200 dark:divide-neutral-800/80">
                                        @forelse ($teamRegistrations as $index => $registration)
                                            <div class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0" wire:key="season-entry-team-{{ $index }}">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $registration['team_name'] }}</p>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $registration['contact_name'] }}
                                                        <span class="text-gray-400 dark:text-gray-500">·</span>
                                                        {{ $registration['contact_telephone'] }}
                                                    </p>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ optional($availableRulesets->firstWhere('id', (int) $registration['ruleset_id']))->name }}
                                                        @if (filled($registration['second_ruleset_id']))
                                                            <span class="text-gray-400 dark:text-gray-500">/</span>
                                                            {{ optional($availableRulesets->firstWhere('id', (int) $registration['second_ruleset_id']))->name }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">£{{ number_format((float) $season->team_entry_fee, 2) }}</span>
                                                    <button type="button" wire:click="removeTeamRegistration({{ $index }})" class="text-sm font-medium text-red-700 transition hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No teams added yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @elseif ($step === 'knockouts')
                                <div class="space-y-8">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Knockout</label>
                                            <select wire:model.live="knockoutDraft.knockout_id" class="{{ $inputClasses }}">
                                                <option value="">Select a knockout</option>
                                                @foreach ($availableKnockouts as $knockout)
                                                    <option value="{{ $knockout->id }}">{{ $knockout->name }} (£{{ number_format((float) $knockout->entry_fee, 2) }})</option>
                                                @endforeach
                                            </select>
                                            @error('knockoutDraft.knockout_id') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Entrant name(s)</label>
                                            <input wire:model.live="knockoutDraft.entrant_name" type="text" class="{{ $inputClasses }}">
                                            @error('knockoutDraft.entrant_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="flex justify-start">
                                        <button type="button" wire:click="addKnockoutRegistration" class="{{ $buttonClasses }}">
                                            Add knockout
                                        </button>
                                    </div>

                                    <div class="divide-y divide-gray-200 dark:divide-neutral-800/80">
                                        @forelse ($knockoutRegistrations as $index => $registration)
                                            @php($knockout = $availableKnockouts->firstWhere('id', (int) $registration['knockout_id']))
                                            <div class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0" wire:key="season-entry-knockout-{{ $index }}">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $knockout?->name }}</p>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $registration['entrant_name'] }}</p>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">£{{ number_format((float) ($knockout?->entry_fee ?? 0), 2) }}</span>
                                                    <button type="button" wire:click="removeKnockoutRegistration({{ $index }})" class="text-sm font-medium text-red-700 transition hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No knockout entries added yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @else
                                <div class="space-y-8">
                                    @error('cart') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                                    <section>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Contact</h3>
                                        <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                            <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contact['name'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contact['email'] }}</span>
                                            </div>
                                            @if (filled($contact['telephone']))
                                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Telephone</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contact['telephone'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </section>

                                    <section>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Venue</h3>
                                        @if (filled($registrationVenue['venue_name']))
                                            <div class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $registrationVenue['venue_name'] }}</p>
                                                @if (filled($registrationVenue['venue_address']))
                                                    <p>{{ $registrationVenue['venue_address'] }}</p>
                                                @endif
                                                @if (filled($registrationVenue['venue_telephone']))
                                                    <p>{{ $registrationVenue['venue_telephone'] }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No venue details added yet.</p>
                                        @endif
                                    </section>

                                    <section>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Teams</h3>
                                        <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                            @forelse ($teamRegistrations as $registration)
                                                <div class="flex items-start justify-between gap-4 py-4 first:pt-0">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $registration['team_name'] }}</p>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $registration['contact_name'] }}
                                                            <span class="text-gray-400 dark:text-gray-500">·</span>
                                                            {{ $registration['contact_telephone'] }}
                                                        </p>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ optional($availableRulesets->firstWhere('id', (int) $registration['ruleset_id']))->name }}
                                                            @if (filled($registration['second_ruleset_id']))
                                                                <span class="text-gray-400 dark:text-gray-500">/</span>
                                                                {{ optional($availableRulesets->firstWhere('id', (int) $registration['second_ruleset_id']))->name }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">£{{ number_format((float) $season->team_entry_fee, 2) }}</span>
                                                </div>
                                            @empty
                                                <p class="py-3 text-sm text-gray-500 dark:text-gray-400">No teams added.</p>
                                            @endforelse
                                        </div>
                                    </section>

                                    <section>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Knockouts</h3>
                                        <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                            @forelse ($knockoutRegistrations as $registration)
                                                @php($knockout = $availableKnockouts->firstWhere('id', (int) $registration['knockout_id']))
                                                <div class="flex items-start justify-between gap-4 py-4 first:pt-0">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $knockout?->name }}</p>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $registration['entrant_name'] }}</p>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">£{{ number_format((float) ($knockout?->entry_fee ?? 0), 2) }}</span>
                                                </div>
                                            @empty
                                                <p class="py-3 text-sm text-gray-500 dark:text-gray-400">No knockouts added.</p>
                                            @endforelse
                                        </div>
                                    </section>

                                    <section>
                                        <div class="mt-3 flex items-center justify-between gap-4">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">£{{ number_format((float) $grandTotal, 2) }}</span>
                                        </div>
                                    </section>

                                    <div class="flex justify-end">
                                        <button type="button" wire:click="submit" class="{{ $buttonClasses }}">
                                            Confirm registration
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-between gap-3">
                    <button
                        type="button"
                        wire:click="previousStep"
                        @disabled($step === 'details')
                        class="{{ $secondaryButtonClasses }} disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Back
                    </button>

                    @if ($step !== 'review')
                        <button type="button" wire:click="nextStep" class="{{ $buttonClasses }}">
                            Continue
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
