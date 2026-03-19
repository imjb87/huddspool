<?php

namespace Tests\Unit;

use Tests\TestCase;

class PwaBrandingTest extends TestCase
{
    public function test_pwa_manifest_uses_the_brand_green_for_app_chrome(): void
    {
        $manifest = config('laravelpwa.manifest');

        $this->assertSame('#166534', $manifest['background_color']);
        $this->assertSame('#166534', $manifest['theme_color']);
        $this->assertSame('any maskable', $manifest['icons']['192x192']['purpose']);
        $this->assertSame('any maskable', $manifest['icons']['512x512']['purpose']);
    }

    public function test_primary_pwa_assets_use_the_green_gradient_background(): void
    {
        $icon = imagecreatefrompng(public_path('images/icons/icon-512-512.png'));
        $splash = imagecreatefrompng(public_path('images/icons/splash-640x1136.png'));

        $this->assertSame([5, 46, 22], $this->rgbAt($icon, 0, 0));
        $this->assertSame([22, 163, 74], $this->rgbAt($icon, 511, 511));
        $this->assertSame([5, 46, 22], $this->rgbAt($splash, 0, 0));
        $this->assertSame([22, 163, 74], $this->rgbAt($splash, 639, 1135));
    }

    /**
     * @return array{0:int, 1:int, 2:int}
     */
    private function rgbAt(\GdImage $image, int $x, int $y): array
    {
        $colour = imagecolorat($image, $x, $y);

        return [
            ($colour >> 16) & 0xFF,
            ($colour >> 8) & 0xFF,
            $colour & 0xFF,
        ];
    }
}
