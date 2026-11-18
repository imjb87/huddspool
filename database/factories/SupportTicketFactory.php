<?php

namespace Database\Factories;

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'requester_name' => fake()->name(),
            'requester_email' => fake()->safeEmail(),
            'message' => fake()->paragraph(),
            'status' => SupportTicketStatus::Open,
            'is_seen' => false,
            'seen_by' => null,
            'seen_at' => null,
            'last_activity_at' => now(),
        ];
    }
}
