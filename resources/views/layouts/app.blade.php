<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-100 text-gray-900 dark:bg-neutral-950">

<head>
    @php
        $metaDescription = trim($__env->yieldContent('meta_description')) ?: config('app.description');
        $metaOgTitle = trim($__env->yieldContent('og_title')) ?: config('app.name', 'Huddersfield & District Tuesday Night Pool League');
        $metaOgDescription = trim($__env->yieldContent('og_description')) ?: $metaDescription;
        $metaOgType = trim($__env->yieldContent('og_type')) ?: 'website';
        $metaOgUrl = trim($__env->yieldContent('og_url')) ?: config('app.frontend_url');
        $metaOgImage = trim($__env->yieldContent('og_image')) ?: asset('images/og-image.jpg');
        $metaOgImageType = trim($__env->yieldContent('og_image_type'));
        $metaOgImageWidth = trim($__env->yieldContent('og_image_width'));
        $metaOgImageHeight = trim($__env->yieldContent('og_image_height'));
        $metaFacebookAppId = trim($__env->yieldContent('facebook_app_id')) ?: config('services.facebook.client_id');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="{{ $metaOgTitle }}" />
    <meta property="og:description" content="{{ $metaOgDescription }}" />
    <meta property="og:type" content="{{ $metaOgType }}" />
    <meta property="og:url" content="{{ $metaOgUrl }}" />
    <meta property="og:image" content="{{ $metaOgImage }}" />
    @if ($metaOgImageType !== '')
        <meta property="og:image:type" content="{{ $metaOgImageType }}" />
    @endif
    @if ($metaOgImageWidth !== '')
        <meta property="og:image:width" content="{{ $metaOgImageWidth }}" />
    @endif
    @if ($metaOgImageHeight !== '')
        <meta property="og:image:height" content="{{ $metaOgImageHeight }}" />
    @endif
    @if ($metaFacebookAppId !== '')
        <meta property="fb:app_id" content="{{ $metaFacebookAppId }}" />
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaOgTitle }}">
    <meta name="twitter:description" content="{{ $metaOgDescription }}">
    <meta name="twitter:image" content="{{ $metaOgImage }}">

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    @if (Route::currentRouteNamed('home'))
        @php
            $logo160PngUrl = asset('images/logo-160.png') . '?v=' . filemtime(public_path('images/logo-160.png'));
            $logo160WebpUrl = asset('images/logo-160.webp') . '?v=' . filemtime(public_path('images/logo-160.webp'));
            $logo320WebpUrl = asset('images/logo-320.webp') . '?v=' . filemtime(public_path('images/logo-320.webp'));
        @endphp

        <link
            rel="preload"
            as="image"
            href="{{ $logo320WebpUrl }}"
            imagesrcset="{{ $logo160WebpUrl }} 160w, {{ $logo320WebpUrl }} 320w"
            imagesizes="(min-width: 1024px) 160px, (min-width: 640px) 144px, 128px"
            fetchpriority="high"
        >
    @endif

    <title>
        @hasSection('title')
            {{ trim($__env->yieldContent('title')) }} |
        @elseif (isset($title))
            {{ $title }} |
        @endif
        {{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}
    </title>

    @include('layouts.partials.theme-head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/livewire-app.js'])
    @livewireStyles
    @laravelPWA
</head>

<body
    class="bg-neutral-100 font-sans antialiased text-gray-900 dark:bg-neutral-950 dark:text-gray-100"
    @if (filled(config('services.google_analytics.measurement_id')))
        data-google-analytics-measurement-id="{{ config('services.google_analytics.measurement_id') }}"
    @endif
>
    @php
        /** @var \App\Models\User|null $authenticatedUser */
        $authenticatedUser = auth()->user();
        $shouldAutoPromptForPushNotifications = $authenticatedUser
            && filled(config('services.web_push.public_key'))
            && filled(config('services.web_push.private_key'))
            && filled(config('services.web_push.subject'))
            && $authenticatedUser->push_prompted_at === null
            && ! $authenticatedUser->pushSubscriptions()->exists();
    @endphp
    <div class="min-h-screen bg-neutral-100 dark:bg-neutral-950">
        @include('layouts.navigation')
        @if ($shouldAutoPromptForPushNotifications)
            @include('layouts.partials.push-notification-native-prompt')
        @endif
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        @include('layouts.footer')
        @include('layouts.partials.site-search')
    </div>
    @livewireScriptConfig
</body>

</html>
