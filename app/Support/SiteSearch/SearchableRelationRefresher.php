<?php

namespace App\\Support\\SiteSearch;

use App\\Models\\Ruleset;
use App\\Models\\Section;
use App\\Models\\SectionTeam;
use App\\Models\\Season;
use App\\Models\\Team;
use App\\Models\\User;
use App\\Models\\Venue;
use Illuminate\\Database\\Eloquent\\Model;

class SearchableRelationRefresher
{
    public function refreshTeam(Team $team): void
    {
        $this->refreshTeamAndPlayers($team);

        SectionTeam::query()
            ->where('team_id', $team->getKey())
            ->distinct()
            ->pluck('section_id')
            ->each(fn (int $sectionId) => $this->refreshSectionById($sectionId));
    }

    public function refreshSection(Section $section): void
    {
        $this->refreshSearchable($section);

        SectionTeam::query()
            ->where('section_id', $section->getKey())
            ->distinct()
            ->pluck('team_id')
            ->each(fn (int $teamId) => $this->refreshTeamAndPlayersById($teamId));
    }

    public function refreshSectionTeam(SectionTeam $sectionTeam): void
    {
        $this->refreshSectionById((int) $sectionTeam->section_id);
        $this->refreshTeamAndPlayersById((int) $sectionTeam->team_id);
    }

    public function refreshSeason(Season $season): void
    {
        $season->sections()
            ->withTrashed()
            ->get()
            ->each(fn (Section $section) => $this->refreshSection($section));
    }

    public function refreshRuleset(Ruleset $ruleset): void
    {
        $ruleset->sections()
            ->withTrashed()
            ->get()
            ->each(fn (Section $section) => $this->refreshSearchable($section));
    }

    public function refreshVenueTeams(Venue $venue): void
    {
        $venue->teams()
            ->withTrashed()
            ->get()
            ->each(fn (Team $team) => $this->refreshSearchable($team));
    }

    private function refreshTeamAndPlayers(Team $team): void
    {
        $this->refreshSearchable($team);

        User::query()
            ->where('team_id', $team->getKey())
            ->get()
            ->each(fn (User $user) => $this->refreshSearchable($user));
    }

    private function refreshTeamAndPlayersById(int $teamId): void
    {
        $team = Team::withTrashed()->find($teamId);

        if (! $team) {
            return;
        }

        $this->refreshTeamAndPlayers($team);
    }

    private function refreshSectionById(int $sectionId): void
    {
        $section = Section::withTrashed()->find($sectionId);

        if ($section) {
            $this->refreshSearchable($section);
        }
    }

    private function refreshSearchable(Model $model): void
    {
        if (! method_exists($model, 'shouldBeSearchable')) {
            return;
        }

        $model = $model->fresh() ?? $model;

        if ($model->shouldBeSearchable()) {
            $model->searchable();

            return;
        }

        $model->unsearchable();
    }
}
