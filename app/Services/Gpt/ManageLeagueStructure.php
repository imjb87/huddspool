<?php

namespace App\Services\Gpt;

use App\Models\GptActionAudit;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManageLeagueStructure
{
    public function createTeam(User $admin, array $data, ?string $ip, ?string $agent): array
    {
        return DB::transaction(function () use ($admin, $data, $ip, $agent): array {
            $team = Team::query()->create($data);

            return [$team, $this->audit($admin, 'create_team', $team, null, $this->teamData($team), $ip, $agent)];
        });
    }

    public function updateTeam(User $admin, Team $team, array $data, Carbon $expected, ?string $ip, ?string $agent): array
    {
        return DB::transaction(function () use ($admin, $team, $data, $expected, $ip, $agent): array {
            $locked = Team::query()->lockForUpdate()->findOrFail($team->id);
            $this->ensureUpdatedAt($locked, $expected);
            $before = $this->teamData($locked);
            $locked->update($data);

            return [$locked, $this->audit($admin, 'update_team', $locked, $before, $this->teamData($locked->refresh()), $ip, $agent)];
        });
    }

    public function foldTeam(User $admin, Team $team, ?string $ip, ?string $agent): GptActionAudit
    {
        return DB::transaction(function () use ($admin, $team, $ip, $agent): GptActionAudit {
            $locked = Team::query()->lockForUpdate()->findOrFail($team->id);
            if ($locked->folded_at !== null) {
                throw ValidationException::withMessages(['team' => 'This team is already folded.']);
            }
            $before = $this->teamData($locked);
            $locked->update(['folded_at' => now()]);

            return $this->audit($admin, 'fold_team', $locked, $before, $this->teamData($locked), $ip, $agent);
        });
    }

    public function createVenue(User $admin, array $data, ?string $ip, ?string $agent): array
    {
        return DB::transaction(function () use ($admin, $data, $ip, $agent): array {
            $venue = Venue::query()->create($data);

            return [$venue, $this->audit($admin, 'create_venue', $venue, null, $this->venueData($venue), $ip, $agent)];
        });
    }

    public function updateVenue(User $admin, Venue $venue, array $data, Carbon $expected, ?string $ip, ?string $agent): array
    {
        return DB::transaction(function () use ($admin, $venue, $data, $expected, $ip, $agent): array {
            $locked = Venue::query()->lockForUpdate()->findOrFail($venue->id);
            $this->ensureUpdatedAt($locked, $expected);
            $before = $this->venueData($locked);
            $locked->update($data);

            return [$locked, $this->audit($admin, 'update_venue', $locked, $before, $this->venueData($locked->refresh()), $ip, $agent)];
        });
    }

    public function addTeam(User $admin, Section $section, Team $team, ?string $ip, ?string $agent): array
    {
        return DB::transaction(function () use ($admin, $section, $team, $ip, $agent): array {
            if ($section->sectionTeams()->where('team_id', $team->id)->exists()) {
                throw ValidationException::withMessages(['team_id' => 'This team is already in the section.']);
            }
            $membership = $section->sectionTeams()->create(['team_id' => $team->id, 'sort' => ((int) $section->sectionTeams()->max('sort')) + 1, 'deducted' => 0]);

            return [$membership, $this->audit($admin, 'add_team_to_section', $membership, null, $this->membershipData($membership), $ip, $agent)];
        });
    }

    public function deduct(User $admin, SectionTeam $membership, int $points, int $expected, ?string $ip, ?string $agent): GptActionAudit
    {
        return DB::transaction(function () use ($admin, $membership, $points, $expected, $ip, $agent): GptActionAudit {
            $locked = SectionTeam::query()->lockForUpdate()->findOrFail($membership->id);
            $this->ensureOpenSeasonMembership($locked);
            if ((int) $locked->deducted !== $expected) {
                throw ValidationException::withMessages(['expected_current_deduction' => 'The deduction changed after it was inspected. Inspect it again before retrying.']);
            }
            $before = $this->membershipData($locked);
            $locked->update(['deducted' => $points]);

            return $this->audit($admin, 'update_points_deduction', $locked, $before, $this->membershipData($locked), $ip, $agent);
        });
    }

    public function withdraw(User $admin, SectionTeam $membership, ?string $ip, ?string $agent): GptActionAudit
    {
        return DB::transaction(function () use ($admin, $membership, $ip, $agent): GptActionAudit {
            $locked = SectionTeam::query()->lockForUpdate()->findOrFail($membership->id);
            $this->ensureOpenSeasonMembership($locked);
            if ($locked->withdrawn_at !== null) {
                throw ValidationException::withMessages(['section_team' => 'This team is already withdrawn from the section.']);
            }
            $section = Section::query()->with('season')->findOrFail($locked->section_id);
            $bye = Team::byeOrFail();
            $before = $this->membershipData($locked);
            $locked->update(['withdrawn_at' => now()->toDateString()]);

            $currentWeek = collect($section->season->dates ?? [])->flatten()->filter()->takeUntil(fn ($date) => Carbon::parse($date)->isFuture())->count() + 1;
            $results = $section->results()->where(fn ($query) => $query->where('results.home_team_id', $locked->team_id)->orWhere('results.away_team_id', $locked->team_id));
            if ($currentWeek >= 9) {
                $results->whereHas('fixture', fn ($query) => $query->where('week', '>', 9));
            }
            $results->get()->each(function ($result): void {
                $result->frames()->delete();
                $result->delete();
            });

            $section->fixtures()->whereDoesntHave('result')->where(fn ($query) => $query->where('home_team_id', $locked->team_id)->orWhere('away_team_id', $locked->team_id))->get()->each(function ($fixture) use ($locked, $bye): void {
                $fixture->update([$fixture->home_team_id === $locked->team_id ? 'home_team_id' : 'away_team_id' => $bye->id]);
            });

            return $this->audit($admin, 'withdraw_team_from_section', $locked, $before, $this->membershipData($locked), $ip, $agent);
        });
    }

    private function ensureUpdatedAt(Model $model, Carbon $expected): void
    {
        if (! $model->updated_at?->equalTo($expected)) {
            throw ValidationException::withMessages(['expected_updated_at' => 'The record changed after it was inspected. Inspect it again before retrying.']);
        }
    }

    private function ensureOpenSeasonMembership(SectionTeam $membership): void
    {
        if (! $membership->section()->whereHas('season', fn ($query) => $query->where('is_open', true))->exists()) {
            throw ValidationException::withMessages(['section_team' => 'Points deductions and withdrawals can only be applied to the current open season.']);
        }
    }

    private function teamData(Team $team): array
    {
        return $team->only(['name', 'shortname', 'venue_id', 'captain_id', 'folded_at', 'updated_at']);
    }

    private function venueData(Venue $venue): array
    {
        return $venue->only(['name', 'address', 'telephone', 'latitude', 'longitude', 'updated_at']);
    }

    private function membershipData(SectionTeam $membership): array
    {
        return $membership->only(['section_id', 'team_id', 'sort', 'deducted', 'withdrawn_at']);
    }

    private function audit(User $admin, string $action, Model $subject, ?array $before, array $after, ?string $ip, ?string $agent): GptActionAudit
    {
        return GptActionAudit::query()->create(['administrator_id' => $admin->id, 'action' => $action, 'subject_type' => $subject::class, 'subject_id' => $subject->getKey(), 'before' => $before, 'after' => $after, 'ip_address' => $ip, 'user_agent' => $agent]);
    }
}
