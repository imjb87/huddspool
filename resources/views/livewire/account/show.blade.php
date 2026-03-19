<div class="pb-10 lg:pb-14" data-account-page>
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
            <h1 class="text-lg font-semibold text-gray-900">Your profile</h1>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="-ml-3 flex gap-2">
                <a href="{{ route('account.show') }}"
                    class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-900 transition">
                    Profile
                </a>
                @if ($this->user->isCaptain() || $this->user->isTeamAdmin())
                    <a href="{{ route('account.team') }}"
                        class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                        Team
                    </a>
                @endif
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
                <section id="account-profile" class="py-1" data-account-profile-section>
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-900">Personal Information</h3>
                            <p class="max-w-sm text-sm leading-6 text-gray-500">
                                Manage the profile details shown for your account and keep your avatar up to date.
                            </p>
                        </div>

                        <div class="space-y-6 lg:col-span-2">
                            <div class="space-y-3">
                                <div class="flex items-end justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                    <div class="shrink-0">
                                        @if ($removeAvatar)
                                            <img src="{{ asset('/images/user.jpg') }}"
                                                alt="Avatar preview"
                                                class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200">
                                        @elseif ($avatarUpload)
                                            <img src="{{ $avatarUpload->temporaryUrl() }}"
                                                alt="Avatar preview"
                                                class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200">
                                        @else
                                                <img src="{{ $this->user->avatar_url }}"
                                                    alt="{{ $this->user->name }} avatar"
                                                    class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200">
                                            @endif
                                        </div>

                                        <div class="space-y-1.5">
                                            <label class="inline-flex cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-200 hover:text-gray-900">
                                                <span>Change avatar</span>
                                                <input type="file" wire:model="avatarUpload" class="hidden" accept="image/*">
                                            </label>

                                            <p class="text-xs text-gray-500">PNG, JPG or GIF up to 5MB.</p>
                                        </div>
                                    </div>

                                    @if ($this->user->avatar_path || $avatarUpload)
                                        <button type="button"
                                            wire:click="clearAvatar"
                                            class="shrink-0 self-end text-sm font-medium text-gray-600 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                            </div>

                            @error('avatarUpload')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="pt-1">
                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $this->user->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Team</p>
                                    @if ($this->team)
                                        <a href="{{ route('team.show', $this->team) }}"
                                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500">
                                            {{ $this->team->name }}
                                        </a>
                                    @else
                                        <p class="mt-2 text-sm text-gray-900">Free agent</p>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Role</p>
                                    <p class="mt-2 text-sm text-gray-900">{{ $this->user->roleLabel() }}</p>
                                </div>

                                <div class="pt-2 pb-2 sm:col-span-2">
                                    @php
                                        $framesWonPercentage = fmod((float) $this->record->frames_won_percentage, 1.0) === 0.0
                                            ? number_format($this->record->frames_won_percentage, 0)
                                            : number_format($this->record->frames_won_percentage, 1);
                                        $framesLostPercentage = fmod((float) $this->record->frames_lost_percentage, 1.0) === 0.0
                                            ? number_format($this->record->frames_lost_percentage, 0)
                                            : number_format($this->record->frames_lost_percentage, 1);
                                    @endphp
                                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                                        <div class="grid grid-cols-3 divide-x divide-gray-200">
                                            <div class="px-4 py-4 sm:px-5">
                                                <p class="text-xs font-medium text-gray-500">Played</p>
                                                <p class="mt-1 text-base font-semibold text-gray-900">{{ $this->record->frames_played }}</p>
                                            </div>
                                            <div class="px-4 py-4 sm:px-5">
                                                <p class="text-xs font-medium text-gray-500">Won</p>
                                                <div class="mt-1 flex items-end justify-between gap-2">
                                                    <p class="text-base font-semibold text-green-700">{{ $this->record->frames_won }}</p>
                                                    <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700">
                                                        {{ $framesWonPercentage }}%
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="px-4 py-4 sm:px-5">
                                                <p class="text-xs font-medium text-gray-500">Lost</p>
                                                <div class="mt-1 flex items-end justify-between gap-2">
                                                    <p class="text-base font-semibold text-red-700">{{ $this->record->frames_lost }}</p>
                                                    <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">
                                                        {{ $framesLostPercentage }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="account-email" class="block text-sm font-medium text-gray-900">Email address</label>
                                    <input type="email"
                                        id="account-email"
                                        wire:model.live="email"
                                        class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="account-telephone" class="block text-sm font-medium text-gray-900">Phone number</label>
                                    <input type="text"
                                        id="account-telephone"
                                        wire:model.live="telephone"
                                        class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20">
                                    @error('telephone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                </div>

                                <div class="mt-5">
                                    <button type="button"
                                        wire:click="saveProfile"
                                        class="inline-flex min-w-24 items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @if ($this->knockoutMatches->isNotEmpty())
                    <section class="border-t border-gray-200 pt-6" data-account-knockout-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">Knockouts</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Your recent knockout matches and any results that still need submitting.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200">
                                    @foreach ($this->knockoutMatches as $match)
                                        @php
                                            $hasResult = $match->home_score !== null && $match->away_score !== null;
                                            $canSubmit = ! $hasResult && \Illuminate\Support\Facades\Gate::allows('submitResult', $match);
                                            $rowUrl = $canSubmit
                                                ? route('knockout.matches.submit', $match)
                                                : ($hasResult ? route('knockout.show', $match->round->knockout) : null);
                                            $userParticipantId = null;

                                            if ($match->homeParticipant?->includesPlayer($this->user) || $match->homeParticipant?->team_id === $this->user->team_id) {
                                                $userParticipantId = $match->homeParticipant?->id;
                                            } elseif ($match->awayParticipant?->includesPlayer($this->user) || $match->awayParticipant?->team_id === $this->user->team_id) {
                                                $userParticipantId = $match->awayParticipant?->id;
                                            }

                                            $wonMatch = $hasResult && $match->winner_participant_id && $userParticipantId === $match->winner_participant_id;
                                            $resultPillClasses = $wonMatch
                                                ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                                : 'bg-linear-to-br from-red-800 via-red-700 to-red-600';
                                        @endphp
                                        <div wire:key="account-knockout-{{ $match->id }}">
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

                @if ($this->frames->isNotEmpty())
                    <section class="border-t border-gray-200 pt-6" data-account-frames-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">Frames</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Recent frames you have played this season.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200">
                                    @foreach ($this->frames as $frame)
                                        @php
                                            $wonFrame = $frame->home_player_id === $this->user->id
                                                ? $frame->home_score > $frame->away_score
                                                : $frame->away_score > $frame->home_score;
                                            $opponentName = $frame->home_player_id === $this->user->id ? $frame->away_player_name : $frame->home_player_name;
                                            $opponentTeam = $frame->home_player_id === $this->user->id ? $frame->away_team_name : $frame->home_team_name;
                                        @endphp
                                        <a href="{{ route('result.show', $frame->result_id) }}"
                                            class="block hover:bg-gray-50"
                                            wire:key="account-frame-{{ $frame->result_id }}-{{ $loop->index }}">
                                            <div class="flex items-center gap-4 py-4">
                                                <div class="shrink-0">
                                                    <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full px-2 text-xs font-bold text-white shadow-sm ring-1 ring-black/10 {{ $wonFrame ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700' : 'bg-linear-to-br from-red-800 via-red-700 to-red-600' }}">
                                                        {{ $wonFrame ? 'W' : 'L' }}
                                                    </span>
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $opponentName }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">{{ $opponentTeam }}</p>
                                                </div>

                                                <div class="shrink-0 text-right text-sm text-gray-500">
                                                    {{ $frame->fixture_date->format('j M') }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @if ($this->history->isNotEmpty())
                    <section class="border-t border-gray-200 pt-6" data-account-history-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900">History</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500">
                                    Season-by-season playing record using the archived team and section details from recorded results.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200">
                                    @foreach ($this->history as $entry)
                                        @php
                                            $winPercentage = fmod((float) $entry['win_percentage'], 1.0) === 0.0
                                                ? number_format($entry['win_percentage'], 0)
                                                : number_format($entry['win_percentage'], 1);
                                            $lossPercentage = fmod((float) $entry['loss_percentage'], 1.0) === 0.0
                                                ? number_format($entry['loss_percentage'], 0)
                                                : number_format($entry['loss_percentage'], 1);
                                        @endphp
                                        <div class="flex items-center gap-4 py-4" wire:key="account-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ md5((string) $entry['team_name']) }}">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ $entry['season_name'] }}</p>
                                                <p class="mt-1 truncate text-sm text-gray-700">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                                <p class="mt-1 text-xs text-gray-500">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 items-start gap-3 self-center text-center">
                                                <div class="w-16">
                                                    <p class="text-xs font-medium text-gray-500">Played</p>
                                                    <p class="mt-1 text-sm font-semibold leading-5 text-gray-900">{{ $entry['played'] }}</p>
                                                    <span class="mt-1 inline-flex h-[18px] items-center text-[10px] font-semibold text-transparent">
                                                        0%
                                                    </span>
                                                </div>
                                                <div class="w-16">
                                                    <p class="text-xs font-medium text-gray-500">Won</p>
                                                    <p class="mt-1 text-sm font-semibold leading-5 text-green-700">{{ $entry['wins'] }}</p>
                                                    <span class="mt-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700">{{ $winPercentage }}%</span>
                                                </div>
                                                <div class="w-16">
                                                    <p class="text-xs font-medium text-gray-500">Lost</p>
                                                    <p class="mt-1 text-sm font-semibold leading-5 text-red-700">{{ $entry['losses'] }}</p>
                                                    <span class="mt-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">{{ $lossPercentage }}%</span>
                                                </div>
                                            </div>
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
