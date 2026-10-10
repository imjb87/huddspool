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
        $this->assertStringContainsString('--motion-duration-mobile-menu: 620ms;', $css);
        $this->assertStringContainsString('--motion-ease-mobile-menu: cubic-bezier(0.2, 1.12, 0.3, 1);', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('animation-duration: 0.01ms !important;', $css);
        $this->assertStringContainsString('.ui-motion-panel-enter-start', $css);
        $this->assertStringContainsString('.ui-live-score-item--updated', $css);
        $this->assertStringContainsString('.ui-result-form-frame-item--updated', $css);
        $this->assertStringContainsString('transition-property: transform, opacity;', $css);
        $this->assertStringContainsString('@keyframes ui-search-shell-enter', $css);
        $this->assertStringContainsString('@keyframes ui-search-shell-leave', $css);
        $this->assertStringContainsString('@keyframes ui-mobile-menu-enter', $css);
        $this->assertStringContainsString('@keyframes ui-mobile-menu-panel-enter', $css);
        $this->assertStringContainsString('@keyframes ui-mobile-menu-panel-leave', $css);
        $this->assertStringContainsString('--mobile-menu-enter-overshoot: -3%;', $css);
        $this->assertStringContainsString('--mobile-menu-enter-overshoot: 3%;', $css);
        $this->assertStringContainsString('--mobile-menu-enter-settle: 1%;', $css);
        $this->assertStringContainsString('--mobile-menu-leave-overshoot: -103%;', $css);
        $this->assertStringContainsString('--mobile-menu-leave-overshoot: 103%;', $css);
        $this->assertStringContainsString('--mobile-menu-enter-overshoot-small: -0.4%;', $css);
        $this->assertStringContainsString('--mobile-menu-enter-overshoot-small: 0.4%;', $css);
        $this->assertStringContainsString('--mobile-menu-leave-overshoot-small: -100.4%;', $css);
        $this->assertStringContainsString('--mobile-menu-leave-overshoot-small: 100.4%;', $css);
        $this->assertStringContainsString('52% {', $css);
        $this->assertStringContainsString('69% {', $css);
        $this->assertStringContainsString('81% {', $css);
        $this->assertStringContainsString('91% {', $css);
        $this->assertStringContainsString('animation: ui-mobile-menu-leave var(--motion-duration-mobile-menu) var(--motion-ease-mobile-menu) both;', $css);
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

    public function test_mobile_navigation_rows_match_card_surfaces_and_use_sixteen_pixel_text(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('.navigation-mobile-menu .ui-card-row', $css);
        $this->assertStringContainsString('@apply min-h-0 px-3 py-2.5 text-base leading-6 font-medium sm:px-4;', $css);
        $this->assertStringContainsString('@apply flex flex-col gap-2 divide-y-0 px-3 py-4 sm:px-5;', $css);
        $this->assertStringContainsString('.navigation-mobile-menu .ui-card-row-link,', $css);
        $this->assertStringContainsString('@apply rounded-lg transition-colors duration-100;', $css);
        $this->assertStringContainsString('background-color: color-mix(in oklab, lab(96.52% -0.0000298023 0.0000119209) 50%, transparent);', $css);
        $this->assertStringContainsString('.dark .navigation-mobile-menu .ui-card-row-link,', $css);
        $this->assertStringContainsString('background-color: color-mix(in oklab, lab(15.204% 0 -0.00000596046) 50%, transparent);', $css);
        $this->assertStringContainsString('.navigation-mobile-menu .ui-card {', $css);
        $this->assertStringContainsString('@apply p-0;', $css);
    }

    public function test_mobile_menu_icon_uses_morph_svg_plugin_with_damped_rebounds(): void
    {
        $script = file_get_contents(resource_path('js/mobile-menu-icon.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString("import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin';", $script);
        $this->assertStringContainsString('gsap.registerPlugin(MorphSVGPlugin);', $script);
        $this->assertStringContainsString('const wobbleShapes = {', $script);
        $this->assertStringContainsString("const springEase = 'elastic.out(1.2, 0.55)'", $script);
        $this->assertStringContainsString('data-mobile-menu-icon-group', $script);
        $this->assertStringContainsString('if (icon.dataset.mobileMenuIconState === nextState)', $script);
        $this->assertStringContainsString('rotation: -14, scale: 0.78', $script);
        $this->assertStringContainsString('rotation: 7, scale: 1.1', $script);
        $this->assertStringContainsString('rotation: -3.5, scale: 0.95', $script);
        $this->assertStringContainsString('rotation: 1.75, scale: 1.025', $script);
        $this->assertStringContainsString('rotation: -0.75, scale: 0.99', $script);
        $this->assertStringContainsString('rotation: 14, scale: 0.78', $script);
        $this->assertStringContainsString('rotation: -7, scale: 1.1', $script);
        $this->assertStringContainsString('rotation: 3.5, scale: 0.95', $script);
        $this->assertStringContainsString('rotation: -1.75, scale: 1.025', $script);
        $this->assertStringContainsString('rotation: 0.75, scale: 0.99', $script);
        $this->assertStringNotContainsString('x:', $script);
        $this->assertStringContainsString('setIconState(icon, isOpen);', $script);
        $this->assertStringContainsString('morphSVG: shapes.top', $script);
        $this->assertStringContainsString('morphSVG: shapes.bottom', $script);
    }
}
