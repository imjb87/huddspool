<?php

namespace Tests\Unit;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class DarkModeBladeClassTest extends TestCase
{
    public function test_blade_views_do_not_use_the_solid_dark_neutral_800_background_class(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(resource_path('views'))
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            $this->assertIsString($contents);
            $this->assertDoesNotMatchRegularExpression('/dark:bg-neutral-900(?!\\/)/', $contents, $file->getPathname());
        }
    }
}
