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
