<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 text-gray-900 dark:bg-zinc-900">

@php
    $usesLivewire = trim($__env->yieldContent('uses-livewire')) === 'true';
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:title" content="{{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}" />
    <meta property="og:description" content="{{ config('app.description') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ config('app.frontend_url') }}" />
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}" />

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
            href="{{ $logo160PngUrl }}"
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

    @if (filled(config('services.google_analytics.measurement_id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.measurement_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', @js(config('services.google_analytics.measurement_id')));
        </script>
    @endif

    <!-- Scripts -->
    @if ($usesLivewire)
        @vite(['resources/css/app.css', 'resources/js/livewire-app.js'])
        @livewireStyles
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @laravelPWA
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-zinc-900 dark:text-gray-100">
    <div class="min-h-screen bg-gray-100 dark:bg-zinc-900">
        @include('layouts.navigation')
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        @include('layouts.footer')
        @include('layouts.partials.site-search')
    </div>
    @if ($usesLivewire)
        @livewireScriptConfig
    @endif
</body>

</html>
