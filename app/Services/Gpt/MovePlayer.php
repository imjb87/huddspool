<?php

namespace App\Services\Gpt;

use App\Models\GptActionAudit;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovePlayer
{
    public function handle(
        User $administrator,
        User $player,
        Team $destinationTeam,
        ?int $expectedCurrentTeamId,
        bool $makeDestinationCaptain,
        ?string $ipAddress,
        ?string $userAgent,
    ): GptActionAudit {
        return DB::transaction(function () use ($administrator, $player, $destinationTeam, $expectedCurrentTeamId, $makeDestinationCaptain, $ipAddress, $userAgent): GptActionAudit {
            $lockedPlayer = User::query()->lockForUpdate()->findOrFail($player->id);

            if ($lockedPlayer->team_id !== $expectedCurrentTeamId) {
                throw ValidationException::withMessages([
                    'expected_current_team_id' => 'The player’s current team changed after it was inspected. Search again before retrying.',
                ]);
            }

            if ($lockedPlayer->team_id === $destinationTeam->id) {
                throw ValidationException::withMessages([
                    'destination_team_id' => 'The player already belongs to the destination team.',
                ]);
            }

            $currentTeam = $lockedPlayer->team()->withTrashed()->first();
            $wasCurrentCaptain = $currentTeam?->captain_id === $lockedPlayer->id;
            $previous = [
                'player_id' => $lockedPlayer->id,
                'team_id' => $lockedPlayer->team_id,
                'team_name' => $currentTeam?->name,
                'was_captain' => $wasCurrentCaptain,
            ];

            if ($wasCurrentCaptain) {
                $currentTeam->update(['captain_id' => null]);
            }

            $lockedPlayer->update(['team_id' => $destinationTeam->id]);

            if ($makeDestinationCaptain) {
                $destinationTeam->update(['captain_id' => $lockedPlayer->id]);
            }

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'move_player',
                'subject_type' => User::class,
                'subject_id' => $lockedPlayer->id,
                'before' => $previous,
                'after' => [
                    'player_id' => $lockedPlayer->id,
                    'team_id' => $destinationTeam->id,
                    'team_name' => $destinationTeam->name,
                    'is_captain' => $makeDestinationCaptain,
                ],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
