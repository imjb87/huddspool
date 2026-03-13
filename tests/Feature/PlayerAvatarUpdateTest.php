<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlayerAvatarUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_update_their_own_avatar(): void
    {
        Storage::fake('public');

        $player = User::factory()->create([
            'avatar_path' => 'avatars/old-avatar.jpg',
        ]);

        Storage::disk('public')->put('avatars/old-avatar.jpg', 'old-avatar');

        $response = $this
            ->actingAs($player)
            ->post(route('player.avatar', $player), [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response
            ->assertRedirect(route('player.show', $player))
            ->assertSessionHas('status', 'Avatar updated');

        $player->refresh();

        $this->assertNotSame('avatars/old-avatar.jpg', $player->avatar_path);
        $this->assertStringStartsWith('avatars/', $player->avatar_path);
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
        Storage::disk('public')->assertExists($player->avatar_path);
    }

    public function test_avatar_upload_requires_an_image_file(): void
    {
        Storage::fake('public');

        $player = User::factory()->create();

        $response = $this
            ->actingAs($player)
            ->from(route('player.show', $player))
            ->post(route('player.avatar', $player), []);

        $response
            ->assertRedirect(route('player.show', $player))
            ->assertSessionHasErrors(['avatar']);

        $this->assertNull($player->fresh()->avatar_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_player_profile_shows_avatar_upload_for_the_player(): void
    {
        $player = User::factory()->create();

        $this->actingAs($player)
            ->get(route('player.show', $player))
            ->assertOk()
            ->assertSee('avatar-upload-'.$player->id, false);
    }

    public function test_player_profile_hides_avatar_upload_for_other_non_admin_users(): void
    {
        $player = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('player.show', $player))
            ->assertOk()
            ->assertDontSee('avatar-upload-'.$player->id, false);
    }

    public function test_non_admin_can_not_update_another_players_avatar(): void
    {
        Storage::fake('public');

        $player = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this
            ->actingAs($otherUser)
            ->post(route('player.avatar', $player), [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response->assertForbidden();

        $this->assertNull($player->fresh()->avatar_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_admin_can_update_another_players_avatar(): void
    {
        Storage::fake('public');

        $player = User::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('player.avatar', $player), [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response
            ->assertRedirect(route('player.show', $player))
            ->assertSessionHas('status', 'Avatar updated');

        $this->assertStringStartsWith('avatars/', $player->fresh()->avatar_path);
        Storage::disk('public')->assertExists($player->fresh()->avatar_path);
    }

    public function test_player_profile_shows_avatar_upload_for_admin_viewing_another_player(): void
    {
        $player = User::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('player.show', $player))
            ->assertOk()
            ->assertSee('avatar-upload-'.$player->id, false);
    }
}
