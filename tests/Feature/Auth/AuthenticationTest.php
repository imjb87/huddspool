<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('data-login-page', false);
        $response->assertSeeText('Log in');
        $response->assertSeeText('Access your account to manage your profile');
        $response->assertSee('type="email"', false);
        $response->assertSee('type="password"', false);
        $response->assertSee(route('auth.google'), false);
        $response->assertSeeText('Continue with Google');
        $response->assertDontSee(route('auth.facebook'), false);
        $response->assertDontSeeText('Continue with Facebook');
        $response->assertSee('site-theme', false);
        $response->assertSee('prefers-color-scheme: dark', false);
    }

    public function test_users_can_start_google_authentication(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new SymfonyRedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('auth.google'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_existing_users_can_authenticate_with_google(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://example.com/avatar.jpg' => Http::response('avatar-binary', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $user = User::factory()->create();

        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn($user->email);
        $socialiteUser->shouldReceive('getAvatar')
            ->once()
            ->andReturn('https://example.com/avatar.jpg');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('account.show'));
        $this->assertStringStartsWith('avatars/google-', $user->fresh()->avatar_path);
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);
    }

    public function test_users_can_start_facebook_authentication(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new SymfonyRedirectResponse('https://www.facebook.com/v19.0/dialog/oauth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($provider);

        $this->get(route('auth.facebook'))
            ->assertRedirect('https://www.facebook.com/v19.0/dialog/oauth');
    }

    public function test_existing_users_can_authenticate_with_facebook(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://example.com/facebook-avatar.jpg' => Http::response('avatar-binary', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $user = User::factory()->create();

        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn($user->email);
        $socialiteUser->shouldReceive('getAvatar')
            ->once()
            ->andReturn('https://example.com/facebook-avatar.jpg');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($provider);

        $response = $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('account.show'));
        $this->assertStringStartsWith('avatars/facebook-', $user->fresh()->avatar_path);
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);
    }

    public function test_facebook_authentication_does_not_replace_an_existing_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/existing-avatar.jpg', 'existing-avatar');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/existing-avatar.jpg',
        ]);

        Http::fake();

        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn($user->email);
        $socialiteUser->shouldReceive('getAvatar')
            ->once()
            ->andReturn('https://example.com/new-facebook-avatar.jpg');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($provider);

        $response = $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('account.show'));
        $this->assertSame('avatars/existing-avatar.jpg', $user->fresh()->avatar_path);
        Storage::disk('public')->assertExists('avatars/existing-avatar.jpg');
        Http::assertNothingSent();
    }

    public function test_google_authentication_does_not_replace_an_existing_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/existing-avatar.jpg', 'existing-avatar');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/existing-avatar.jpg',
        ]);

        Http::fake();

        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn($user->email);
        $socialiteUser->shouldReceive('getAvatar')
            ->once()
            ->andReturn('https://example.com/new-avatar.jpg');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('account.show'));
        $this->assertSame('avatars/existing-avatar.jpg', $user->fresh()->avatar_path);
        Storage::disk('public')->assertExists('avatars/existing-avatar.jpg');
        Http::assertNothingSent();
    }

    public function test_google_authentication_fails_when_no_matching_user_exists(): void
    {
        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn('missing@example.com');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->from(route('login'))->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'google' => 'No account matches the email address on that Google account.',
        ]);
    }

    public function test_facebook_authentication_fails_when_no_matching_user_exists(): void
    {
        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getEmail')
            ->once()
            ->andReturn('missing@example.com');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($provider);

        $response = $this->from(route('login'))->get('/auth/facebook/callback');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'facebook' => 'No account matches the email address on that Facebook account.',
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('account.show'));
    }

    public function test_team_admin_sees_result_submission_shortcut_after_login_when_a_fixture_is_due(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
        $user = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
            'fixture_date' => now(),
        ]);

        $response = $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response
            ->assertOk()
            ->assertSeeText('A team result is ready to submit.')
            ->assertSee(route('result.create', $fixture), false);
    }

    public function test_login_with_remember_me_sets_a_recaller_cookie_and_token(): void
    {
        $user = User::factory()->create([
            'remember_token' => null,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh()->remember_token);

        $rememberCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_'));

        $this->assertNotNull($rememberCookie);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
