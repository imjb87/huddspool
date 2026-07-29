<?php

namespace App\Services\Gpt;

use App\Enums\RoleName;
use App\Models\GptActionAudit;
use App\Models\User;
use App\Support\SiteAuthorization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagePlayerAccount
{
    public function create(User $administrator, array $attributes, ?string $ipAddress, ?string $userAgent): array
    {
        return DB::transaction(function () use ($administrator, $attributes, $ipAddress, $userAgent): array {
            $role = RoleName::from($attributes['site_role']);
            unset($attributes['site_role']);
            $player = User::query()->create(SiteAuthorization::applyLegacyColumnsForRole($attributes, $role));
            SiteAuthorization::syncSpatieRoleFromLegacyColumns($player);
            $audit = $this->audit($administrator, 'create_player', $player, null, $this->snapshot($player), $ipAddress, $userAgent);

            return [$player, $audit];
        });
    }

    public function update(User $administrator, User $player, array $attributes, Carbon $expectedUpdatedAt, ?string $ipAddress, ?string $userAgent): array
    {
        return DB::transaction(function () use ($administrator, $player, $attributes, $expectedUpdatedAt, $ipAddress, $userAgent): array {
            $lockedPlayer = User::query()->lockForUpdate()->findOrFail($player->id);

            if (! $lockedPlayer->updated_at?->equalTo($expectedUpdatedAt)) {
                throw ValidationException::withMessages(['expected_updated_at' => 'The account changed after it was inspected. Inspect it again before retrying.']);
            }

            $before = $this->snapshot($lockedPlayer);
            if (array_key_exists('site_role', $attributes)) {
                $role = RoleName::from($attributes['site_role']);
                unset($attributes['site_role']);
                $attributes = SiteAuthorization::applyLegacyColumnsForRole($attributes, $role);
            }

            if ($attributes === []) {
                throw ValidationException::withMessages(['account' => 'Supply at least one account field to update.']);
            }

            $lockedPlayer->update($attributes);
            SiteAuthorization::syncSpatieRoleFromLegacyColumns($lockedPlayer);
            $audit = $this->audit($administrator, 'update_player', $lockedPlayer, $before, $this->snapshot($lockedPlayer->refresh()), $ipAddress, $userAgent);

            return [$lockedPlayer, $audit];
        });
    }

    private function snapshot(User $player): array
    {
        return [
            'name' => $player->name,
            'email' => $player->email,
            'telephone' => $player->telephone,
            'team_id' => $player->team_id,
            'site_role' => $player->roleLabel(),
            'updated_at' => $player->updated_at?->toAtomString(),
        ];
    }

    private function audit(User $administrator, string $action, User $player, ?array $before, array $after, ?string $ipAddress, ?string $userAgent): GptActionAudit
    {
        return GptActionAudit::query()->create([
            'administrator_id' => $administrator->id,
            'action' => $action,
            'subject_type' => User::class,
            'subject_id' => $player->id,
            'before' => $before,
            'after' => $after,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
