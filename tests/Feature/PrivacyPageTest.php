<?php

namespace Tests\Feature;

use Tests\TestCase;

class PrivacyPageTest extends TestCase
{
    public function test_privacy_page_is_public_and_covers_support_contact(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('data-privacy-page', false)
            ->assertSee('support requests')
            ->assertSee(route('support.tickets'));
    }
}
