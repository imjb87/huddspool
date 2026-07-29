<?php

namespace Tests\Feature;

use Tests\TestCase;

class PrivacyPageTest extends TestCase
{
    public function test_privacy_page_is_public_and_covers_the_chatgpt_connection(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('data-privacy-page', false)
            ->assertSee('ChatGPT administrator connection')
            ->assertSee('OAuth');
    }
}
