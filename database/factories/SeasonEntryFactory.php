<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeasonEntry>
 */
class SeasonEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'reference' => null,
            'contact_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_telephone' => fake()->phoneNumber(),
            'existing_venue_id' => Venue::factory(),
            'venue_name' => fake()->company(),
            'venue_address' => fake()->address(),
            'venue_telephone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'total_amount' => 25,
            'paid_at' => null,
        ];
    }
}
