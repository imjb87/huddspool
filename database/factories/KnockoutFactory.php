<?php

namespace Database\Factories;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Knockout>
 */
class KnockoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'name' => fake()->unique()->words(2, true),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'type' => KnockoutType::Singles,
            'best_of' => 5,
            'entry_fee' => 10,
            'published_at' => now(),
        ];
    }
}
