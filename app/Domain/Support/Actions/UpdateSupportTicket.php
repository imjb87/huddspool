<?php

namespace App\Domain\Support\Actions;

use App\Domain\Support\Notifications\SupportTicketNotificationService;
use App\Models\SupportTicket;
use App\Models\User;
use BackedEnum;
use Illuminate\Support\Facades\DB;

final class UpdateSupportTicket
{
    public function __construct(
        private SupportTicketNotificationService $notifications,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function execute(SupportTicket $ticket, array $attributes, User $actor): SupportTicket
    {
        $changed = false;
        $updatedTicket = DB::transaction(function () use ($ticket, $attributes, $actor, &$changed): SupportTicket {
            $trackedFields = ['status'];
            $before = $ticket->only($trackedFields);

            $ticket->fill($attributes);
            $ticket->save();

            foreach ($trackedFields as $field) {
                if (! array_key_exists($field, $attributes) || $before[$field] === $ticket->getAttribute($field)) {
                    continue;
                }

                $changed = true;

                $ticket->activities()->create([
                    'user_id' => $actor->getKey(),
                    'description' => ucfirst(str_replace('_', ' ', $field)).' changed',
                    'old_value' => $this->activityValue($before[$field]),
                    'new_value' => $this->activityValue($ticket->getAttribute($field)),
                ]);
            }

            return $ticket->refresh();
        });

        if ($changed) {
            $this->notifications->ticketUpdated($updatedTicket->load('requester'), $actor);
        }

        return $updatedTicket;
    }

    private function activityValue(mixed $value): ?string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return $value === null ? null : (string) $value;
    }
}
