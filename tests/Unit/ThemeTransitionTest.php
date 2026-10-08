<?php

namespace Tests\Unit;

use Tests\TestCase;

class ThemeTransitionTest extends TestCase
{
    public function test_theme_switching_enables_a_global_transition_class(): void
    {
        $script = file_get_contents(resource_path('views/layouts/partials/theme-head.blade.php'));

        $this->assertIsString($script);
        $this->assertStringContainsString("const transitionClass = 'theme-transitioning';", $script);
        $this->assertStringContainsString('const startThemeTransition = () => {', $script);
        $this->assertStringContainsString('startThemeTransition();', $script);
    }

    public function test_theme_transition_class_animates_common_dark_mode_style_changes(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('html.theme-transitioning *', $css);
        $this->assertStringContainsString('transition-property: background-color, border-color, color, fill, stroke, box-shadow, text-decoration-color;', $css);
        $this->assertStringContainsString('transition-duration: 500ms;', $css);
    }

    public function test_motion_system_defines_shared_timings_and_reduced_motion_fallbacks(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('--motion-duration-fast: 120ms;', $css);
        $this->assertStringContainsString('--motion-duration-panel: 240ms;', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('animation-duration: 0.01ms !important;', $css);
        $this->assertStringContainsString('.ui-motion-panel-enter-start', $css);
        $this->assertStringContainsString('.ui-live-score-item--updated', $css);
        $this->assertStringContainsString('.ui-result-form-frame-item--updated', $css);
        $this->assertStringContainsString('transition-property: transform, opacity;', $css);
        $this->assertStringContainsString('@keyframes ui-search-shell-enter', $css);
        $this->assertStringContainsString('@keyframes ui-search-shell-leave', $css);
    }

    public function test_search_and_notification_surfaces_keep_their_transform_transitions(): void
    {
        $search = file_get_contents(resource_path('views/layouts/partials/site-search.blade.php'));
        $notifications = file_get_contents(resource_path('views/components/account/notifications-drawer.blade.php'));

        $this->assertIsString($search);
        $this->assertIsString($notifications);
        $this->assertStringContainsString('x-transition:enter="ui-motion-search-shell-in"', $search);
        $this->assertStringContainsString('x-transition:leave="ui-motion-search-shell-out"', $search);
        $this->assertStringContainsString('x-transition:enter="ui-motion-drawer-in"', $notifications);
        $this->assertStringContainsString('ui-motion-drawer-enter-start', $notifications);
        $this->assertStringContainsString('ui-motion-drawer-leave-end', $notifications);
        $this->assertStringNotContainsString('transition-[background-color,border-color,box-shadow,color]', $notifications);
    }
}
