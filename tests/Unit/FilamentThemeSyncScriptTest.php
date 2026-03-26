<?php

namespace Tests\Unit;

use Tests\TestCase;

class FilamentThemeSyncScriptTest extends TestCase
{
    private function script(): string
    {
        $path = public_path('js/filament-theme-sync.js');

        $this->assertFileExists($path);

        $script = file_get_contents($path);

        $this->assertIsString($script);

        return $script;
    }

    public function test_filament_theme_sync_script_uses_shared_frontend_storage_key(): void
    {
        $script = $this->script();

        $this->assertStringContainsString("const storageKey = 'site-theme';", $script);
        $this->assertStringContainsString("window.localStorage.setItem(storageKey, theme);", $script);
        $this->assertStringContainsString("window.addEventListener('storage', (event) => {", $script);
    }

    public function test_filament_theme_sync_script_tracks_filament_theme_mutations(): void
    {
        $script = $this->script();

        $this->assertStringContainsString('new MutationObserver(() => {', $script);
        $this->assertStringContainsString("attributeFilter: ['class'],", $script);
        $this->assertStringContainsString("document.documentElement.classList.contains('dark')", $script);
    }
}
