<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        User::query()
            ->whereNotNull('avatar_path')
            ->lazyById()
            ->each(function (User $user): void {
                if ($user->hasMedia('avatars')) {
                    $user->forceFill([
                        'avatar_path' => null,
                    ])->saveQuietly();

                    return;
                }

                $path = (string) $user->avatar_path;

                if (! Storage::disk('public')->exists($path)) {
                    $user->forceFill([
                        'avatar_path' => null,
                    ])->saveQuietly();

                    return;
                }

                $user->addMediaFromDisk($path, 'public')
                    ->usingFileName(basename($path))
                    ->usingName(pathinfo($path, PATHINFO_FILENAME))
                    ->withCustomProperties([
                        'legacy_avatar_path' => $path,
                    ])
                    ->toMediaCollection('avatars', 'public');

                Storage::disk('public')->delete($path);

                $user->forceFill([
                    'avatar_path' => null,
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        //
    }
};
