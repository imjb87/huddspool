<?php

namespace Database\Factories;

use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Result>
 */
class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        $homeName = $this->faker->company;
        $awayName = $this->faker->company . ' FC';

        return [
            'fixture_id' => \App\Models\Fixture::factory(),
            'home_team_id' => Team::factory(),
            'home_team_name' => $homeName,
            'home_score' => $this->faker->numberBetween(0, 10),
            'away_team_id' => Team::factory(),
            'away_team_name' => $awayName,
            'away_score' => $this->faker->numberBetween(0, 10),
            'is_confirmed' => true,
            'is_overridden' => false,
            'submitted_by' => User::factory(),
            'section_id' => Section::factory(),
            'ruleset_id' => Ruleset::factory(),
        ];
    }
}
