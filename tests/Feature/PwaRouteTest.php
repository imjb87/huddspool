<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaRouteTest extends TestCase
{
    public function test_manifest_route_is_served_before_the_page_catch_all(): void
    {
        $this->get('/manifest.json')
            ->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonFragment([
                'short_name' => 'Huddspool',
            ]);
    }

    public function test_offline_route_is_served_before_the_page_catch_all(): void
    {
        $this->get('/offline')
            ->assertOk();
    }
}
