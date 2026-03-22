<?php

namespace App\Support;

use App\Enums\PermissionName;
use App\Models\Fixture;
use App\Models\KnockoutMatch;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ResultSubmissionPromptResolver
{
    public function promptFor(User $user): ?array
    {
        $fixtures = $this->outstandingFixturesFor($user);
        $knockoutMatches = $this->outstandingKnockoutMatchesFor($user);

        if ($fixtures->isEmpty() && $knockoutMatches->isEmpty()) {
            return null;
        }

        $fixtureCount = $fixtures->count();
        $knockoutCount = $knockoutMatches->count();
        $fixture = $fixtures->first();

        $message = match (true) {
            $fixtureCount > 0 && $knockoutCount > 0 => sprintf(
                '%d team result%s and %d knockout result%s are ready to submit.',
                $fixtureCount,
                $fixtureCount === 1 ? '' : 's',
                $knockoutCount,
                $knockoutCount === 1 ? '' : 's',
            ),
            $fixtureCount > 0 => $fixtureCount === 1
                ? 'A team result is ready to submit.'
                : sprintf('%d team results are ready to submit.', $fixtureCount),
            default => $knockoutCount === 1
                ? 'A knockout result is ready to submit.'
                : sprintf('%d knockout results are ready to submit.', $knockoutCount),
        };

        return [
            'message' => $message,
            'fixture_label' => $fixtureCount === 1 && $knockoutCount === 0
                ? sprintf('%s vs %s', $fixture->homeTeam?->name ?? 'TBC', $fixture->awayTeam?->name ?? 'TBC')
                : null,
            'url' => $fixtureCount === 1 && $knockoutCount === 0 && $fixture instanceof Fixture
                ? route('result.create', $fixture)
                : null,
            'button_label' => $fixtureCount === 1 && $knockoutCount === 0 ? 'Submit result' : null,
            'fixtures_heading' => $fixtureCount > 0 ? 'League matches' : null,
            'fixtures' => $fixtures
                ->map(fn (Fixture $outstandingFixture) => [
                    'label' => sprintf(
                        '%s vs %s',
                        $outstandingFixture->homeTeam?->name ?? 'TBC',
                        $outstandingFixture->awayTeam?->name ?? 'TBC',
                    ),
                    'url' => route('result.create', $outstandingFixture),
                ])
                ->all(),
            'knockouts_heading' => $knockoutCount > 0 ? 'Knockouts' : null,
            'knockouts' => $knockoutMatches
                ->map(fn (KnockoutMatch $match) => [
                    'label' => sprintf(
                        '%s / %s: %s',
                        $match->knockout?->name ?? 'Knockout',
                        $match->round?->name ?? 'Round TBC',
                        $match->title(),
                    ),
                    'url' => route('knockout.matches.submit', $match),
                ])
                ->all(),
        ];
    }

    public function pendingFixtureFor(User $user): ?Fixture
    {
        return $this->outstandingFixturesFor($user)->first();
    }

    public function outstandingFixturesFor(User $user): Collection
    {
        if (! $user->team) {
            return collect();
        }

        if (! $user->can(PermissionName::SubmitLeagueResults->value)) {
            return collect();
        }

        return Fixture::query()
            ->with(['result', 'homeTeam', 'awayTeam'])
            ->inOpenSeason()
            ->forTeam($user->team)
            ->whereHas('homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('awayTeam', fn (Builder $query) => $query->notBye())
            ->whereDate('fixture_date', '<=', now()->toDateString())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->filter(fn (Fixture $fixture) => $this->actionUrlFor($user, $fixture) !== null)
            ->values();
    }

    public function outstandingKnockoutMatchesFor(User $user): Collection
    {
        return KnockoutMatch::query()
            ->with([
                'knockout.season',
                'round',
                'homeParticipant.playerOne',
                'homeParticipant.playerTwo',
                'homeParticipant.team',
                'awayParticipant.playerOne',
                'awayParticipant.playerTwo',
                'awayParticipant.team',
            ])
            ->whereHas('knockout.season', fn (Builder $query) => $query->where('is_open', true))
            ->whereNull('winner_participant_id')
            ->orderByRaw('case when starts_at is null then 1 else 0 end')
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get()
            ->filter(fn (KnockoutMatch $match) => $match->isDueForSubmission() && $match->userShouldBePromptedToSubmit($user))
            ->values();
    }

    public function actionUrlFor(User $user, Fixture $fixture): ?string
    {
        $isDue = $fixture->fixture_date?->isPast() || $fixture->fixture_date?->isToday();

        if (! $isDue) {
            return null;
        }

        if ($fixture->result instanceof Result) {
            return ! $fixture->result->is_confirmed && Gate::forUser($user)->allows('resumeSubmission', $fixture->result)
                ? route('result.create', $fixture)
                : null;
        }

        return Gate::forUser($user)->allows('createResult', $fixture)
            ? route('result.create', $fixture)
            : null;
    }
}
