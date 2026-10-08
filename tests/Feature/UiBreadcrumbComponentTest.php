<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class UiBreadcrumbComponentTest extends TestCase
{
    public function test_ui_breadcrumb_renders_links_and_current_item(): void
    {
        $html = Blade::render(
            '<x-ui-breadcrumb :items="$items" />',
            [
                'items' => [
                    ['label' => 'History'],
                    ['label' => 'Summer 2026', 'url' => '/history/summer-2026/international-rules/summer-section'],
                    ['label' => 'International Rules', 'current' => true],
                ],
            ],
        );

        $html = preg_replace('/\s+/', ' ', $html) ?? $html;

        $this->assertStringContainsString('aria-label="Breadcrumb"', $html);
        $this->assertStringContainsString('break-words text-sm', $html);
        $this->assertStringContainsString('> History </span>', $html);
        $this->assertStringNotContainsString('href="/history"', $html);
        $this->assertStringContainsString('> Summer 2026 </a>', $html);
        $this->assertStringContainsString('href="/history/summer-2026/international-rules/summer-section"', $html);
        $this->assertStringContainsString('> International Rules </span>', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertSame(2, substr_count($html, 'role="presentation"'));
    }
}
