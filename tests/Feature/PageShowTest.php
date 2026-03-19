<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_show_uses_the_redesigned_public_layout(): void
    {
        $page = Page::query()->create([
            'title' => 'Knockout Dates',
            'slug' => 'knockout-dates',
            'content' => '<p>Important league dates.</p><p>Quarter-finals from next week.</p>',
        ]);

        $this->get(route('page.show', $page))
            ->assertOk()
            ->assertSee('data-page-show', false)
            ->assertSee('data-page-content-section', false)
            ->assertSee('data-page-content', false)
            ->assertSeeText('Knockout Dates')
            ->assertSeeText('Important league dates.')
            ->assertSeeText('Quarter-finals from next week.');
    }
}
