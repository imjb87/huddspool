<?php

namespace Tests\Unit;

use Tests\TestCase;

class PwaServiceWorkerTest extends TestCase
{
    public function test_service_worker_precaches_the_offline_shell_and_discovers_built_assets(): void
    {
        $serviceWorker = file_get_contents(public_path('serviceworker.js'));

        $this->assertIsString($serviceWorker);
        $this->assertStringContainsString('const OFFLINE_URL = "/offline";', $serviceWorker);
        $this->assertStringContainsString('const BUILD_MANIFEST_URL = "/build/manifest.json";', $serviceWorker);
        $this->assertStringContainsString('const STATIC_CACHE_NAME = `${CACHE_PREFIX}static-v3`;', $serviceWorker);
        $this->assertStringContainsString('"/manifest.json"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-48-48.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-72-72.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-96-96.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-144-144.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-192-192.png"', $serviceWorker);
        $this->assertStringContainsString('"/images/icons/icon-512-512.png"', $serviceWorker);
        $this->assertStringNotContainsString('icon-72x72.png', $serviceWorker);
        $this->assertStringNotContainsString('icon-192x192.png', $serviceWorker);
        $this->assertStringNotContainsString('icon-512x512.png', $serviceWorker);
    }

    public function test_service_worker_does_not_cache_private_documents(): void
    {
        $serviceWorker = file_get_contents(public_path('serviceworker.js'));

        $this->assertIsString($serviceWorker);
        $this->assertStringContainsString('const PRIVATE_PATH_PREFIXES = [', $serviceWorker);
        $this->assertStringContainsString('"/account",', $serviceWorker);
        $this->assertStringContainsString('"/results/create",', $serviceWorker);
        $this->assertStringContainsString('function isPublicDocumentResponse(response)', $serviceWorker);
        $this->assertStringContainsString('return /(?:^|,)\\s*public\\b/i.test(cacheControl)', $serviceWorker);
        $this->assertStringContainsString('const MAX_RUNTIME_ENTRIES = 100;', $serviceWorker);
    }

    public function test_service_worker_keeps_notification_targets_on_the_same_origin(): void
    {
        $serviceWorker = file_get_contents(public_path('serviceworker.js'));

        $this->assertIsString($serviceWorker);
        $this->assertStringContainsString('function notificationTargetUrl(value)', $serviceWorker);
        $this->assertStringContainsString('if (targetUrl.origin !== self.location.origin)', $serviceWorker);
        $this->assertStringContainsString('clients.openWindow(targetUrl)', $serviceWorker);
    }
}
