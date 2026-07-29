<?php

namespace App\Services\Gpt;

use App\Models\GptActionAudit;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateTeamCaptain
{
    public function handle(User $administrator, Team $team, ?User $captain, ?int $expectedCaptainId, ?string $ipAddress, ?string $userAgent): GptActionAudit
    {
        return DB::transaction(function () use ($administrator, $team, $captain, $expectedCaptainId, $ipAddress, $userAgent): GptActionAudit {
            $lockedTeam = Team::query()->lockForUpdate()->findOrFail($team->id);

            if ($lockedTeam->captain_id !== $expectedCaptainId) {
                throw ValidationException::withMessages(['expected_current_captain_id' => 'The team’s captain changed after it was inspected. Inspect the team again before retrying.']);
            }

            if ($captain !== null && $captain->team_id !== $lockedTeam->id) {
                throw ValidationException::withMessages(['captain_id' => 'The captain must be a current player on this team.']);
            }

            if ($lockedTeam->captain_id === $captain?->id) {
                throw ValidationException::withMessages(['captain_id' => 'This player is already the team captain.']);
            }

            $before = ['captain_id' => $lockedTeam->captain_id, 'captain_name' => $lockedTeam->captain?->name];
            $lockedTeam->update(['captain_id' => $captain?->id]);

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'update_team_captain',
                'subject_type' => Team::class,
                'subject_id' => $lockedTeam->id,
                'before' => $before,
                'after' => ['captain_id' => $captain?->id, 'captain_name' => $captain?->name],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
