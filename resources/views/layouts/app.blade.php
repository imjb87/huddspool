<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 text-gray-900 dark:bg-zinc-900">

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

    @if (Route::currentRouteName() == 'result.create')
        <meta http-equiv="refresh" content="600">
    @endif

    <title>
        @isset($title) 
            {{ $title }} | 
        @endisset 
        {{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}
    </title>

    @include('layouts.partials.theme-head')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', @js(config('services.google_analytics.measurement_id')));
    </script>

    <!-- Hotjar Tracking Code for https://huddspool.co.uk -->
    <script>
        (function(h, o, t, j, a, r) {
            h.hj = h.hj || function() {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {
                hjid: @js(config('services.hotjar.site_id')),
                hjsv: @js(config('services.hotjar.snippet_version'))
            };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>

    <!-- Fonts -->
    <script src="{{ config('services.font_awesome.kit_url') }}" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
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
        <livewire:search />
    </div>
    @livewireScripts
</body>

</html>
