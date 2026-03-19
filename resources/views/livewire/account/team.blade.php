<div class="pb-10 lg:pb-14" data-account-team-page>
    @if ($this->resultSubmissionPrompt)
        <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-900" data-account-result-submission-prompt>
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-medium">{{ $this->resultSubmissionPrompt->message }}</p>
                        <p class="mt-1 truncate text-xs text-red-800">{{ $this->resultSubmissionPrompt->fixture_label }}</p>
                    </div>
                    <a href="{{ $this->resultSubmissionPrompt->url }}"
                        class="inline-flex h-8 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                        Submit result
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
        data-section-shared-header
        data-account-header>
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900">Your team</h1>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="-ml-3 flex gap-2">
                <a href="{{ route('account.show') }}"
                    class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                    Profile
                </a>
                <a href="{{ route('account.team') }}"
                    class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-900 transition">
                    Team
                </a>
                <a href="{{ route('support.tickets') }}"
                    class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                    Support
                </a>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-6">
            <section class="py-1" data-account-team-info-section>
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-900">Team Information</h3>
                        <p class="max-w-sm text-sm leading-6 text-gray-500">
                            Current team details for the open season, including your section and standing.
                        </p>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <a href="{{ route('team.show', $this->team) }}"
                                    class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                    {{ $this->team->name }}
                                </a>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Section</p>
                                @if ($this->currentSection)
                                    <a href="{{ route('ruleset.section.show', ['ruleset' => $this->currentSection->ruleset, 'section' => $this->currentSection]) }}"
                                        class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                        {{ $this->currentSection->name }}
                                    </a>
                                @else
                                    <p class="mt-2 text-sm text-gray-900">No open section</p>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Venue</p>
                                @if ($this->team->venue)
                                    <a href="{{ route('venue.show', $this->team->venue) }}"
                                        class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                        {{ $this->team->venue->name }}
                                    </a>
                                @else
                                    <p class="mt-2 text-sm text-gray-900">Venue TBC</p>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Captain</p>
                                @if ($this->team->captain)
                                    <a href="{{ route('player.show', $this->team->captain) }}"
                                        class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                        {{ $this->team->captain->name }}
                                    </a>
                                @else
                                    <p class="mt-2 text-sm text-gray-900">Captain TBC</p>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500">Current standing</p>
                                @if ($this->currentStanding)
                                    <p class="mt-2 text-sm text-gray-900">
                                        {{ $this->currentStanding->label }}
                                        <span class="text-gray-500">· {{ $this->currentStanding->points }} pts from {{ $this->currentStanding->played }} played</span>
                                    </p>
                                @else
                                    <p class="mt-2 text-sm text-gray-900">No standing available yet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-t border-gray-200 pt-6" data-account-team-section>
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-900">Team members</h3>
                        <p class="max-w-sm text-sm leading-6 text-gray-500">
                            Current squad members, their role on the team, and this season's P/W/L record.
                        </p>
                    </div>

                    <div class="divide-y divide-gray-200 lg:col-span-2" data-account-team-management>
                        @foreach ($this->teamMembers as $member)
                            @php
                                $framesPlayed = (int) $member->frames_played;
                                $framesWon = (int) $member->frames_won;
                                $framesLost = (int) $member->frames_lost;
                                $rawWonPercentage = $framesPlayed > 0 ? ($framesWon / $framesPlayed) * 100 : 0;
                                $rawLostPercentage = $framesPlayed > 0 ? ($framesLost / $framesPlayed) * 100 : 0;
                                $wonPercentage = fmod($rawWonPercentage, 1.0) === 0.0
                                    ? number_format($rawWonPercentage, 0)
                                    : number_format($rawWonPercentage, 1);
                                $lostPercentage = fmod($rawLostPercentage, 1.0) === 0.0
                                    ? number_format($rawLostPercentage, 0)
                                    : number_format($rawLostPercentage, 1);
                            @endphp
                            <div class="py-4">
                                <div class="flex items-center gap-4">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <img class="h-8 w-8 shrink-0 rounded-full object-cover"
                                                src="{{ $member->avatar_url }}"
                                                alt="{{ $member->name }} avatar">
                                            <div class="min-w-0">
                                                <a href="{{ route('player.show', $member->id) }}"
                                                    class="block truncate text-sm font-semibold text-gray-900 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500">
                                                    {{ $member->name }}
                                                </a>
                                                <p class="mt-1 text-xs text-gray-500">{{ \App\Enums\UserRole::labelFor($member->role) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-auto flex shrink-0 items-center gap-5 text-center">
                                        <div class="w-20">
                                            <p class="text-xs font-medium text-gray-500">Played</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $framesPlayed }}</p>
                                        </div>
                                        <div class="w-20">
                                            <p class="text-xs font-medium text-gray-500">Won</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                <span class="text-green-700">{{ $framesWon }}</span>
                                                <span class="ml-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700">{{ $wonPercentage }}%</span>
                                            </p>
                                        </div>
                                        <div class="w-20">
                                            <p class="text-xs font-medium text-gray-500">Lost</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                <span class="text-red-700">{{ $framesLost }}</span>
                                                <span class="ml-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">{{ $lostPercentage }}%</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="border-t border-gray-200 pt-6" data-account-team-fixtures-section>
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-900">Fixtures</h3>
                        <p class="max-w-sm text-sm leading-6 text-gray-500">
                            Current season fixtures and results for your team. Submission actions appear once a fixture date is due.
                        </p>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="divide-y divide-gray-200" data-account-team-fixtures-shell>
                            @forelse ($this->fixtures as $item)
                                @php
                                    $fixture = $item->fixture;
                                    $rowUrl = $fixture->result ? route('result.show', $fixture->result) : (! $item->action_url ? route('fixture.show', $fixture) : null);
                                @endphp
                                <div class="py-4" wire:key="account-team-fixture-{{ $fixture->id }}">
                                    @if ($rowUrl)
                                        <a class="block hover:cursor-pointer hover:bg-gray-50" href="{{ $rowUrl }}">
                                    @endif
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $fixture->homeTeam?->name }} <span class="font-normal text-gray-400">vs</span> {{ $fixture->awayTeam?->name }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">{{ $fixture->fixture_date->format('j M Y') }}</p>
                                        </div>

                                        <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                            @if ($fixture->result)
                                                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                                    data-section-fixtures-score-pill>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixture->result->home_score ?? '' }}</div>
                                                    <div class="w-px bg-white/25"></div>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixture->result->away_score ?? '' }}</div>
                                                </div>
                                            @elseif ($item->action_url)
                                                <a href="{{ $item->action_url }}"
                                                    class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-3 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                                    {{ $item->action_label }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($rowUrl)
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="px-4 py-10 text-center sm:px-6">
                                    <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8">
                                        <h3 class="text-sm font-semibold text-gray-900">No fixtures available.</h3>
                                        <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                                            Team fixtures will appear here once the current season schedule has been generated.
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            @if ($this->teamKnockoutMatches->isNotEmpty())
                <section class="border-t border-gray-200 pt-6" data-account-team-knockout-section>
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-900">Team knockouts</h3>
                            <p class="max-w-sm text-sm leading-6 text-gray-500">
                                Your team's recent knockout matches and any ties that still need a result submitting.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="divide-y divide-gray-200">
                                @foreach ($this->teamKnockoutMatches as $match)
                                    @php
                                        $hasResult = $match->home_score !== null && $match->away_score !== null;
                                        $canSubmit = ! $hasResult && \Illuminate\Support\Facades\Gate::allows('submitResult', $match);
                                        $rowUrl = $canSubmit
                                            ? route('knockout.matches.submit', $match)
                                            : ($hasResult ? route('knockout.show', $match->round->knockout) : null);
                                        $teamParticipantId = null;

                                        if ($match->homeParticipant?->team_id === $this->team->id) {
                                            $teamParticipantId = $match->homeParticipant?->id;
                                        } elseif ($match->awayParticipant?->team_id === $this->team->id) {
                                            $teamParticipantId = $match->awayParticipant?->id;
                                        }

                                        $wonMatch = $hasResult && $match->winner_participant_id && $teamParticipantId === $match->winner_participant_id;
                                        $resultPillClasses = $wonMatch
                                            ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                            : 'bg-linear-to-br from-red-800 via-red-700 to-red-600';
                                    @endphp
                                    <div wire:key="account-team-knockout-{{ $match->id }}">
                                        @if ($rowUrl)
                                            <a href="{{ $rowUrl }}" class="block transition hover:bg-gray-50">
                                        @endif
                                        <div class="flex items-start gap-3 py-4 sm:items-center sm:gap-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900">
                                                    <span>
                                                        {{ $match->homeParticipant?->display_name ?? 'TBC' }}
                                                    </span>
                                                    <span class="px-1 font-normal text-gray-400">vs</span>
                                                    <span>
                                                        {{ $match->awayParticipant?->display_name ?? 'TBC' }}
                                                    </span>
                                                </p>
                                                <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500">
                                                    {{ $match->round?->knockout?->name ?? 'Knockout' }}
                                                    <span class="text-gray-300">/</span>
                                                    {{ $match->round?->name ?? 'Round TBC' }}
                                                </p>
                                            </div>

                                            <div class="shrink-0 text-right">
                                                @if ($hasResult)
                                                    <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                                        <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                                            {{ $match->home_score }}
                                                        </div>
                                                        <div class="w-px bg-white/25"></div>
                                                        <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                                            {{ $match->away_score }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500">
                                                        {{ $match->starts_at ? $match->starts_at->format('j M') : 'Date TBC' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($rowUrl)
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
