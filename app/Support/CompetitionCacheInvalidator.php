<?php

namespace App\Support;

use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\Page;
use App\Models\Result;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;

class CompetitionCacheInvalidator
{
    private ?CompetitionDataCacheInvalidator $dataCaches = null;

    private ?CompetitionResponseCacheInvalidator $responseCaches = null;

    public function forgetForRulesetContent(?string $slug, ?int $sectionId, ?int $seasonId, ?int $rulesetId): void
    {
        $this->responseCaches()->forgetRulesetContent($slug);
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetNavigationCaches();
        $this->dataCaches()->forgetSectionRelatedCaches($sectionId, $seasonId, $rulesetId);
    }

    public function forgetForResult(Result $result): void
    {
        $this->responseCaches()->forgetResultContent();
        $this->dataCaches()->forgetTeamSeasonHistories([
            $result->home_team_id,
            $result->away_team_id,
        ]);
        $this->dataCaches()->forgetSeasonSeriesCaches();

        $result->loadMissing('section', 'fixture');

        $seasonId = $result->fixture?->season_id ?? $result->section?->season_id;
        $rulesetId = $result->fixture?->ruleset_id ?? $result->section?->ruleset_id;

        $this->dataCaches()->forgetSeasonRelatedCaches($seasonId, $rulesetId);
    }

    public function forgetForFrame(Frame $frame): void
    {
        $this->responseCaches()->forgetFrameContent();
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetSeasonSeriesCaches();
        $this->dataCaches()->forgetNavigationCaches();

        $frame->loadMissing('result.section', 'result.fixture');

        $sectionId = $frame->result?->section_id ?? $frame->result?->section?->id;
        $seasonId = $frame->result?->fixture?->season_id ?? $frame->result?->section?->season_id;
        $rulesetId = $frame->result?->fixture?->ruleset_id ?? $frame->result?->section?->ruleset_id;

        $this->dataCaches()->forgetSectionRelatedCaches($sectionId, $seasonId, $rulesetId);
        $this->dataCaches()->forgetPlayerSeasonHistories([
            $frame->home_player_id,
            $frame->away_player_id,
        ]);
        $this->dataCaches()->forgetTeamSeasonHistories([
            $frame->result?->home_team_id,
            $frame->result?->away_team_id,
        ]);
    }

    public function forgetForSection(Section $section): void
    {
        $this->responseCaches()->forgetSectionContent();
        $this->dataCaches()->forgetNavigationCaches();
        $this->dataCaches()->forgetSectionRelatedCaches($section->id, $section->season_id, $section->ruleset_id);
    }

    public function forgetForSectionTeam(SectionTeam $sectionTeam): void
    {
        $this->responseCaches()->forgetSectionContent();

        if (! $sectionTeam->section_id) {
            return;
        }

        $this->dataCaches()->forgetSectionCaches($sectionTeam->section_id);
        $this->dataCaches()->forgetNavigationCaches();
        $this->dataCaches()->forgetTeamSeasonHistories([$sectionTeam->team_id]);

        $section = Section::query()
            ->select('season_id', 'ruleset_id')
            ->find($sectionTeam->section_id);

        $this->dataCaches()->forgetSeasonRelatedCaches($section?->season_id, $section?->ruleset_id);
    }

    public function forgetForExpulsion(Expulsion $expulsion): void
    {
        $this->responseCaches()->forgetExpulsionContent();
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetNavigationCaches();

        if (! $expulsion->season_id) {
            return;
        }

        $this->dataCaches()->forgetSeasonHistory($expulsion->season_id);
        $this->dataCaches()->forgetSections($this->dataCaches()->sectionsForSeasonId($expulsion->season_id));
    }

    public function forgetForSeason(Season $season): void
    {
        $this->responseCaches()->forgetSeasonContent();
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetSeasonSeriesCaches();
        $this->dataCaches()->forgetNavigationCaches();
        $this->dataCaches()->forgetSeasonHistory($season->id);

        $season->loadMissing('sections');
        $this->dataCaches()->forgetSections($season->sections);
    }

    public function forgetForTeam(Team $team): void
    {
        $this->responseCaches()->forgetTeamContent();
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetPastSeasonNavigation();
        $this->dataCaches()->forgetTeamSeasonHistories([$team->id]);
        $this->dataCaches()->forgetSections($this->dataCaches()->sectionsForTeamIds([$team->id]));
    }

    public function forgetForUser(User $user): void
    {
        $this->responseCaches()->forgetUserContent();
        $this->dataCaches()->forgetOpenSeasonStats();
        $this->dataCaches()->forgetPlayerSeasonHistories([$user->id]);

        $teamIds = collect([
            $user->team_id,
            $user->getOriginal('team_id'),
        ])
            ->filter(fn (?int $id): bool => ! is_null($id))
            ->map(fn (int $id): int => $id)
            ->unique()
            ->values()
            ->all();

        if ($teamIds === []) {
            return;
        }

        $this->dataCaches()->forgetSections($this->dataCaches()->sectionsForTeamIds($teamIds));
    }

    public function forgetForNews(): void
    {
        $this->responseCaches()->forgetHomeContent();
    }

    public function forgetForVenue(Venue $venue): void
    {
        $this->responseCaches()->forgetVenueContent();
    }

    public function forgetForKnockout(Knockout $knockout): void
    {
        $this->responseCaches()->forgetKnockoutContent();
    }

    public function forgetForKnockoutMatch(KnockoutMatch $match): void
    {
        $this->responseCaches()->forgetKnockoutContent();
    }

    public function forgetForPage(Page $page): void
    {
        $this->responseCaches()->forgetPageContent();
    }

    private function dataCaches(): CompetitionDataCacheInvalidator
    {
        return $this->dataCaches ??= new CompetitionDataCacheInvalidator;
    }

    private function responseCaches(): CompetitionResponseCacheInvalidator
    {
        return $this->responseCaches ??= new CompetitionResponseCacheInvalidator;
    }
}
