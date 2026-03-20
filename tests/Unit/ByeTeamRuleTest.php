<?php

namespace Tests\Unit;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Rules\UniqueTeamInSeason;
use App\Rules\UniqueTeamsInSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ByeTeamRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unique_teams_in_section_ignores_duplicate_bye_entries(): void
    {
        $byeTeam = Team::factory()->create(['name' => Team::BYE_NAME]);
        $team = Team::factory()->create();

        $rule = new UniqueTeamsInSection([
            $team->id,
            $byeTeam->id,
            $byeTeam->id,
        ]);

        $this->assertTrue($rule->passes('teams', []));
    }

    public function test_unique_team_in_season_ignores_bye_entries(): void
    {
        $season = Season::factory()->create();
        $ruleset = Ruleset::factory()->create();
        $existingSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $editingSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $byeTeam = Team::factory()->create(['name' => Team::BYE_NAME]);

        $existingSection->teams()->attach($byeTeam->id);

        $rule = new UniqueTeamInSeason($season->load('sections.teams'), [$byeTeam->id], $editingSection->id);

        $this->assertTrue($rule->passes('teams', []));
    }
}
