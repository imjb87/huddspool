<?php

namespace Database\Factories;

use App\Models\Ruleset;
use App\Models\SeasonEntry;
use App\Models\SeasonTeamEntry;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeasonTeamEntry>
 */
class SeasonTeamEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_entry_id' => SeasonEntry::factory(),
            'existing_team_id' => Team::factory(),
            'ruleset_id' => Ruleset::factory(),
            'second_ruleset_id' => null,
            'existing_venue_id' => Venue::factory(),
            'team_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'contact_telephone' => fake()->phoneNumber(),
            'venue_name' => fake()->company(),
            'venue_address' => fake()->address(),
            'venue_telephone' => fake()->phoneNumber(),
            'price' => 25,
        ];
    }
}
