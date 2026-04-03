<?php

namespace App\Support\Notifications;

use App\KnockoutType;
use App\Models\Fixture;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\Result;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationAudienceResolver
{
    /**
     * @return Collection<int, User>
     */
    public function leagueNightRecipientsForFixture(Fixture $fixture): Collection
    {
        $fixture->loadMissing([
            'homeTeam.players',
            'homeTeam.captain',
            'awayTeam.players',
            'awayTeam.captain',
        ]);

        return collect([
            $fixture->homeTeam,
            $fixture->awayTeam,
        ])
            ->filter(fn ($team): bool => $team instanceof Team)
            ->flatMap(fn (Team $team): Collection => $this->playersForTeam($team))
            ->unique(fn (User $user): int => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function teamAdminsForFixture(Fixture $fixture): Collection
    {
        $fixture->loadMissing([
            'homeTeam.players',
            'homeTeam.captain',
            'awayTeam.players',
            'awayTeam.captain',
        ]);

        return collect([
            $fixture->homeTeam,
            $fixture->awayTeam,
        ])
            ->filter(fn ($team): bool => $team instanceof Team)
            ->flatMap(fn (Team $team): Collection => $this->adminsForTeam($team))
            ->unique(fn (User $user): int => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function resultSubmissionRecipients(Result $result): Collection
    {
        $result->loadMissing([
            'fixture.homeTeam.players',
            'fixture.homeTeam.captain',
            'fixture.awayTeam.players',
            'fixture.awayTeam.captain',
        ]);

        return $this->leagueNightRecipientsForFixture($result->fixture);
    }

    /**
     * @return Collection<int, User>
     */
    public function opposingTeamAdminsForResult(Result $result): Collection
    {
        $result->loadMissing([
            'fixture.homeTeam.players',
            'fixture.homeTeam.captain',
            'fixture.awayTeam.players',
            'fixture.awayTeam.captain',
            'submittedBy',
        ]);

        $submittedByTeamId = $result->submittedBy?->team_id;

        $targetTeams = match ($submittedByTeamId) {
            $result->home_team_id => collect([$result->fixture?->awayTeam]),
            $result->away_team_id => collect([$result->fixture?->homeTeam]),
            default => collect([$result->fixture?->homeTeam, $result->fixture?->awayTeam]),
        };

        return $targetTeams
            ->filter(fn ($team): bool => $team instanceof Team)
            ->flatMap(fn (Team $team): Collection => $this->adminsForTeam($team))
            ->reject(fn (User $user): bool => $result->submittedBy?->is($user) ?? false)
            ->unique(fn (User $user): int => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function playersForTeam(Team $team): Collection
    {
        $players = $team->relationLoaded('players') ? $team->players : $team->players()->get();
        $captain = $team->relationLoaded('captain') ? $team->captain : $team->captain()->first();

        return $players
            ->when($captain instanceof User, fn (Collection $users): Collection => $users->push($captain))
            ->unique(fn (User $user): int => $user->getKey())
            ->filter(fn (User $user): bool => $this->canReceiveNotifications($user))
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function adminsForTeam(Team $team): Collection
    {
        return $this->playersForTeam($team)
            ->filter(fn (User $user): bool => $user->isTeamAdmin())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function participantsForKnockoutMatch(KnockoutMatch $match): Collection
    {
        $match->loadMissing([
            'knockout',
            'homeParticipant.team.players',
            'homeParticipant.team.captain',
            'homeParticipant.playerOne',
            'homeParticipant.playerTwo',
            'awayParticipant.team.players',
            'awayParticipant.team.captain',
            'awayParticipant.playerOne',
            'awayParticipant.playerTwo',
        ]);

        return collect([
            $match->homeParticipant,
            $match->awayParticipant,
        ])
            ->filter(fn ($participant): bool => $participant instanceof KnockoutParticipant)
            ->flatMap(fn (KnockoutParticipant $participant): Collection => $this->readyRecipientsForKnockoutParticipant($participant))
            ->unique(fn (User $user): int => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    public function overdueRecipientsForKnockoutMatch(KnockoutMatch $match): Collection
    {
        $match->loadMissing([
            'knockout',
            'homeParticipant.team.players',
            'homeParticipant.team.captain',
            'homeParticipant.playerOne',
            'homeParticipant.playerTwo',
            'awayParticipant.team.players',
            'awayParticipant.team.captain',
            'awayParticipant.playerOne',
            'awayParticipant.playerTwo',
        ]);

        return collect([
            $match->homeParticipant,
            $match->awayParticipant,
        ])
            ->filter(fn ($participant): bool => $participant instanceof KnockoutParticipant)
            ->flatMap(fn (KnockoutParticipant $participant): Collection => $this->overdueRecipientsForKnockoutParticipant($match, $participant))
            ->unique(fn (User $user): int => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function readyRecipientsForKnockoutParticipant(KnockoutParticipant $participant): Collection
    {
        if ($participant->team instanceof Team) {
            return $this->playersForTeam($participant->team);
        }

        return collect([
            $participant->playerOne,
            $participant->playerTwo,
        ])->filter(fn ($user): bool => $user instanceof User && $this->canReceiveNotifications($user))
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function overdueRecipientsForKnockoutParticipant(KnockoutMatch $match, KnockoutParticipant $participant): Collection
    {
        if ($match->knockout?->type === KnockoutType::Team && $participant->team instanceof Team) {
            return $this->adminsForTeam($participant->team);
        }

        return $this->readyRecipientsForKnockoutParticipant($participant);
    }

    private function canReceiveNotifications(User $user): bool
    {
        return filled($user->email) && $user->deleted_at === null;
    }
}
