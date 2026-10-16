<?php

namespace Tests\Unit;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class PoolEngineTest extends TestCase
{
    public function test_game_controller_starts_shoots_renders_and_restores(): void
    {
        $process = new Process(['node', base_path('tests/fixtures/pool-ui.mjs')]);
        $process->setTimeout(30);
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getOutput().$process->getErrorOutput());
    }

    public function test_pool_engine_rules_physics_and_command_validation(): void
    {
        $process = new Process(['node', base_path('tests/fixtures/pool-engine.mjs')]);
        $process->setTimeout(60);
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getOutput().$process->getErrorOutput());
    }

    public function test_game_assets_and_update_policy_are_available_offline(): void
    {
        $worker = file_get_contents(public_path('serviceworker.js'));

        $this->assertStringContainsString('"/games/8-ball/index.html"', $worker);
        $this->assertStringContainsString('`/games/8-ball/v1/${file}`', $worker);
        $this->assertStringNotContainsString('self.skipWaiting()', $worker);

        $this->assertFileExists(public_path('games/8-ball/boot.js'));

        foreach (['config.mjs', 'physics.mjs', 'match.mjs', 'transport.mjs', 'render.mjs', 'audio.mjs', 'input.mjs', 'game.mjs', 'game.css', 'match-screen.css'] as $file) {
            $this->assertFileExists(public_path('games/8-ball/v1/'.$file));
        }
    }
}
