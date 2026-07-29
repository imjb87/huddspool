<?php

namespace App\Services\Gpt;

use App\Models\GptActionAudit;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateTeamVenue
{
    public function handle(User $administrator, Team $team, Venue $venue, ?int $expectedVenueId, ?string $ipAddress, ?string $userAgent): GptActionAudit
    {
        return DB::transaction(function () use ($administrator, $team, $venue, $expectedVenueId, $ipAddress, $userAgent): GptActionAudit {
            $lockedTeam = Team::query()->lockForUpdate()->findOrFail($team->id);

            if ($lockedTeam->venue_id !== $expectedVenueId) {
                throw ValidationException::withMessages(['expected_current_venue_id' => 'The team’s venue changed after it was inspected. Inspect the team again before retrying.']);
            }

            if ($lockedTeam->venue_id === $venue->id) {
                throw ValidationException::withMessages(['venue_id' => 'The team already uses this venue.']);
            }

            $before = ['venue_id' => $lockedTeam->venue_id, 'venue_name' => $lockedTeam->venue?->name];
            $lockedTeam->update(['venue_id' => $venue->id]);

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'update_team_venue',
                'subject_type' => Team::class,
                'subject_id' => $lockedTeam->id,
                'before' => $before,
                'after' => ['venue_id' => $venue->id, 'venue_name' => $venue->name],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
