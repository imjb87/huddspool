<?php

namespace App\Services\Gpt;

use App\Models\Fixture;
use App\Models\GptActionAudit;
use App\Models\Result;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Support\CompetitionCacheInvalidator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReplaceSectionTeam
{
    public function handle(
        User $administrator,
        SectionTeam $sectionTeam,
        Team $replacementTeam,
        int $expectedCurrentTeamId,
        Carbon $expectedUpdatedAt,
        string $reason,
        ?string $ipAddress,
        ?string $userAgent,
    ): GptActionAudit {
        [$audit, $oldTeamId] = DB::transaction(function () use ($administrator, $sectionTeam, $replacementTeam, $expectedCurrentTeamId, $expectedUpdatedAt, $reason, $ipAddress, $userAgent): array {
            $lockedSectionTeam = SectionTeam::query()->lockForUpdate()->findOrFail($sectionTeam->id);
            $lockedReplacementTeam = Team::query()->lockForUpdate()->findOrFail($replacementTeam->id);

            if ((int) $lockedSectionTeam->team_id !== $expectedCurrentTeamId) {
                throw ValidationException::withMessages(['expected_current_team_id' => 'The section team changed after it was inspected. Inspect it again before retrying.']);
            }

            if (! $lockedSectionTeam->updated_at?->equalTo($expectedUpdatedAt)) {
                throw ValidationException::withMessages(['expected_updated_at' => 'The section team changed after it was inspected. Inspect it again before retrying.']);
            }

            if ($expectedCurrentTeamId === $lockedReplacementTeam->id) {
                throw ValidationException::withMessages(['replacement_team_id' => 'The replacement team is already assigned to this section entry.']);
            }

            $section = $lockedSectionTeam->section()->lockForUpdate()->firstOrFail();
            $hasActiveConflict = SectionTeam::query()
                ->whereKeyNot($lockedSectionTeam->id)
                ->where('team_id', $lockedReplacementTeam->id)
                ->whereNull('withdrawn_at')
                ->whereHas('section', fn ($query) => $query->where('season_id', $section->season_id))
                ->lockForUpdate()
                ->exists();

            if ($hasActiveConflict) {
                throw ValidationException::withMessages(['replacement_team_id' => 'The replacement team is already assigned to another active section in this season.']);
            }

            $replacementAlreadyInFixtures = Fixture::query()
                ->where('section_id', $section->id)
                ->where(fn ($query) => $query->where('home_team_id', $lockedReplacementTeam->id)->orWhere('away_team_id', $lockedReplacementTeam->id))
                ->lockForUpdate()
                ->exists();

            if ($replacementAlreadyInFixtures) {
                throw ValidationException::withMessages(['replacement_team_id' => 'The replacement team already appears in fixtures for this section.']);
            }

            $fixtures = Fixture::query()
                ->where('section_id', $section->id)
                ->where(fn ($query) => $query->where('home_team_id', $expectedCurrentTeamId)->orWhere('away_team_id', $expectedCurrentTeamId))
                ->lockForUpdate()
                ->get();
            $results = Result::withTrashed()
                ->where('section_id', $section->id)
                ->where(fn ($query) => $query->where('home_team_id', $expectedCurrentTeamId)->orWhere('away_team_id', $expectedCurrentTeamId))
                ->lockForUpdate()
                ->get();
            $before = $lockedSectionTeam->only(['section_id', 'team_id', 'sort', 'deducted', 'withdrawn_at', 'updated_at']);

            $lockedSectionTeam->update(['team_id' => $lockedReplacementTeam->id]);
            $fixtures->each(function (Fixture $fixture) use ($expectedCurrentTeamId, $lockedReplacementTeam): void {
                $fixture->update([$fixture->home_team_id === $expectedCurrentTeamId ? 'home_team_id' : 'away_team_id' => $lockedReplacementTeam->id]);
            });
            $results->each(function (Result $result) use ($expectedCurrentTeamId, $lockedReplacementTeam): void {
                $side = $result->home_team_id === $expectedCurrentTeamId ? 'home' : 'away';
                $result->update(["{$side}_team_id" => $lockedReplacementTeam->id, "{$side}_team_name" => $lockedReplacementTeam->name]);
            });

            $after = $lockedSectionTeam->refresh()->only(['section_id', 'team_id', 'sort', 'deducted', 'withdrawn_at', 'updated_at']) + [
                'replacement_team_name' => $lockedReplacementTeam->name,
                'updated_fixture_count' => $fixtures->count(),
                'updated_result_count' => $results->count(),
                'reason' => $reason,
            ];
            $audit = GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'replace_section_team',
                'subject_type' => SectionTeam::class,
                'subject_id' => $lockedSectionTeam->id,
                'before' => $before + ['reason' => $reason],
                'after' => $after,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return [$audit, $expectedCurrentTeamId];
        });

        $cacheInvalidator = new CompetitionCacheInvalidator;
        $cacheInvalidator->forgetForTeam(Team::query()->withTrashed()->findOrFail($oldTeamId));
        $cacheInvalidator->forgetForTeam($replacementTeam);

        return $audit;
    }
}
