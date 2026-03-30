<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\User;
use App\Support\SiteAuthorization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignSystemPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_are_redirected_away_from_the_design_system_page(): void
    {
        $response = $this->get(route('design-system.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_can_not_view_the_design_system_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('design-system.index'))
            ->assertForbidden();
    }

    public function test_admin_users_can_view_the_design_system_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SiteAuthorization::assignRole($admin, RoleName::Admin);

        $this->actingAs($admin)
            ->get(route('design-system.index'))
            ->assertOk()
            ->assertSee('data-design-system-page', false)
            ->assertSee('data-design-system-card', false)
            ->assertSee('data-design-system-card-example', false)
            ->assertSee('data-design-system-card-branded', false)
            ->assertSee('data-design-system-card-column-headings', false)
            ->assertSee('data-design-system-card-rows', false)
            ->assertSee('data-design-system-button', false)
            ->assertSee('data-design-system-button-example', false)
            ->assertSeeText('Card')
            ->assertSeeText('League summary')
            ->assertSeeText('Branded card')
            ->assertSeeText('Register now')
            ->assertSeeText('Open sections')
            ->assertSeeText('Button')
            ->assertSeeText('Submit result')
            ->assertSeeText('Save draft')
            ->assertSeeText('Buttons use a rounded pill silhouette, not a square or card-like radius.')
            ->assertSeeText('Rows inside the card should be separated with standard dividers, using zinc-700 in dark mode.')
            ->assertSeeText('If a card row is linked, it gets a desktop hover state, resolving to zinc-700 in dark mode.')
            ->assertSeeText('If a row set includes data columns, the columns need a single header row using the same typography as the row sub-description.')
            ->assertSeeText('Column headers appear once above the first row and are never repeated per row.')
            ->assertSeeText('ui-card-branded')
            ->assertSeeText('Total')
            ->assertDontSeeText('Desktop row bands');
    }
}
