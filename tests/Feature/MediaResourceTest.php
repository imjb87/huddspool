<?php

namespace Tests\Feature;

use App\Filament\Resources\Media\Pages\ManageMedia;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_media_library_records(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();
        $user->replaceAvatarWithContents('avatar-binary', 'member-avatar.jpg');
        $media = $user->getFirstMedia('avatars');

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(ManageMedia::class)
            ->assertCanSeeTableRecords([$media]);
    }

    public function test_avatar_url_prefers_media_library_avatar_over_legacy_avatar_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/old-avatar.jpg', 'old-avatar');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/old-avatar.jpg',
        ]);

        $user->replaceAvatarWithContents('avatar-binary', 'member-avatar.jpg');
        $media = $user->fresh()->getFirstMedia('avatars');

        $this->assertNotNull($media);
        $this->assertNull($user->fresh()->avatar_path);
        $this->assertSame($media->getUrl(), $user->fresh()->avatar_url);
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }
}
