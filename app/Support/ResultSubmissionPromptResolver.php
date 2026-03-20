<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ResultSubmissionPromptResolver
{
    public function promptFor(User $user): ?array
    {
        $fixture = $this->pendingFixtureFor($user);

        if (! $fixture) {
            return null;
        }

        return [
            'message' => 'A team result is ready to submit.',
            'fixture_label' => sprintf(
                '%s vs %s',
                $fixture->homeTeam?->name ?? 'TBC',
                $fixture->awayTeam?->name ?? 'TBC',
            ),
            'url' => route('result.create', $fixture),
        ];
    }

    public function pendingFixtureFor(User $user): ?Fixture
    {
        if (! $user->isTeamAdmin() && ! $user->isCaptain()) {
            return null;
        }

        if (! $user->team) {
            return null;
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
            ->first(fn (Fixture $fixture) => $this->actionUrlFor($user, $fixture) !== null);
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
