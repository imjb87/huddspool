<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Fixture>
 */
class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition(): array
    {
        return [
            'week' => $this->faker->numberBetween(1, 20),
            'fixture_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'season_id' => Season::factory(),
            'section_id' => Section::factory(),
            'venue_id' => Venue::factory(),
            'ruleset_id' => Ruleset::factory(),
        ];
    }
}
