<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\User;

class ResultFormLockManager
{
    /**
     * @return array{
     *     lock: ?FixtureResultLock,
     *     canEdit: bool,
     *     lockedByAnother: bool,
     *     lockOwnerName: ?string,
     *     lockExpiresAtHuman: ?string
     * }
     */
    public function acquire(Fixture $fixture, User $user, int $timeoutMinutes): array
    {
        $lock = FixtureResultLock::firstOrNew(['fixture_id' => $fixture->id]);

        if ($lock->exists && $lock->isActive() && $lock->locked_by !== $user->id) {
            $lock->loadMissing('user');

            return [
                'lock' => $lock,
                'canEdit' => false,
                'lockedByAnother' => true,
                'lockOwnerName' => $lock->user?->name ?? 'Another team admin',
                'lockExpiresAtHuman' => optional($lock->locked_until)?->diffForHumans(),
            ];
        }

        $lock->locked_by = $user->id;
        $lock->locked_until = now()->addMinutes($timeoutMinutes);
        $lock->save();

        return [
            'lock' => $lock,
            'canEdit' => true,
            'lockedByAnother' => false,
            'lockOwnerName' => $user->name,
            'lockExpiresAtHuman' => optional($lock->locked_until)?->diffForHumans(),
        ];
    }

    public function release(?FixtureResultLock $lock, int $userId): void
    {
        if ($lock && $lock->locked_by === $userId) {
            $lock->delete();
        }
    }
}
