<?php

namespace Tests\Feature;

use Tests\TestCase;

class PoolGamePageTest extends TestCase
{
    public function test_local_game_is_public_and_embedded_in_the_application(): void
    {
        $this->get(route('games.eight-ball'))
            ->assertOk()
            ->assertSee('data-pool-host', false)
            ->assertSee('data-game-state="loading"', false)
            ->assertDontSee('<iframe', false)
            ->assertSee('data-player-name="Player 1"', false);
    }
}
