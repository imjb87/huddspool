<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => Str::slug($this->faker->unique()->sentence(3)),
            'dates' => [],
            'signup_opens_at' => now()->subDay(),
            'signup_closes_at' => now()->addWeek(),
            'team_entry_fee' => 25,
            'is_open' => true,
        ];
    }
}
