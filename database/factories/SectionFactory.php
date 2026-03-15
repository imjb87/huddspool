<?php

namespace Database\Factories;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'season_id' => Season::factory(),
            'ruleset_id' => Ruleset::factory(),
        ];
    }
}
