<?php

namespace Database\Factories;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\SeasonKnockoutEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeasonKnockoutEntry>
 */
class SeasonKnockoutEntryFactory extends Factory
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
            'knockout_id' => Knockout::factory()->state([
                'season_id' => Season::factory(),
                'type' => KnockoutType::Singles,
            ]),
            'season_team_entry_id' => null,
            'existing_team_id' => null,
            'entrant_name' => fake()->name(),
            'player_one_name' => fake()->name(),
            'player_two_name' => null,
            'price' => 10,
        ];
    }
}
