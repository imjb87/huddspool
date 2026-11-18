<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\SupportTicketActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicketActivity>
 */
class SupportTicketActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_ticket_id' => SupportTicket::factory(),
            'user_id' => User::factory(),
            'description' => 'Ticket updated',
            'old_value' => null,
            'new_value' => null,
        ];
    }
}
