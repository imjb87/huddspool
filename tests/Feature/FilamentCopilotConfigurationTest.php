<?php

namespace Tests\Feature;

use Tests\TestCase;

class FilamentCopilotConfigurationTest extends TestCase
{
    public function test_copilot_uses_the_huddspool_specific_system_prompt(): void
    {
        $prompt = (string) config('filament-copilot.system_prompt');

        $this->assertStringContainsString('Huddspool admin copilot', $prompt);
        $this->assertStringContainsString('Huddersfield Pool League', $prompt);
        $this->assertStringContainsString('British English', $prompt);
    }
}
