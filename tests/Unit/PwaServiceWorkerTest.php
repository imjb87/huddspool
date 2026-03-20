<?php

namespace Tests\Unit;

use Tests\TestCase;

class PwaServiceWorkerTest extends TestCase
{
    public function test_service_worker_precaches_current_pwa_assets(): void
    {
        $serviceWorker = file_get_contents(public_path('serviceworker.js'));

        $this->assertIsString($serviceWorker);
        $this->assertStringContainsString('const OFFLINE_URL = "/offline";', $serviceWorker);
        $this->assertStringContainsString('"/manifest.json"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-48-48.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-72-72.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-96-96.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-144-144.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-192-192.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-512-512.png"', $serviceWorker);
        $this->assertStringNotContainsString('"/css/app.css"', $serviceWorker);
        $this->assertStringNotContainsString('"/js/app.js"', $serviceWorker);
        $this->assertStringNotContainsString('icon-72x72.png', $serviceWorker);
        $this->assertStringNotContainsString('icon-192x192.png', $serviceWorker);
        $this->assertStringNotContainsString('icon-512x512.png', $serviceWorker);
    }
}
