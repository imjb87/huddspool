<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:title" content="Huddersfield & District Tuesday Night Pool League" />
    <meta property="og:description"
        content="Every Tuesday night, teams from across Huddersfield and the surrounding areas compete in the Huddersfield & District Tuesday Night Pool League. Check here for the latest tables, fixtures, results and averages." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.huddspool.co.uk" />
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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-620MNWY15S"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-620MNWY15S');
    </script>

    <!-- Hotjar Tracking Code for https://huddspool.co.uk -->
    <script>
        (function(h, o, t, j, a, r) {
            h.hj = h.hj || function() {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {
                hjid: 3651301,
                hjsv: 6
            };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>

    <!-- Fonts -->
    <script src="https://kit.fontawesome.com/b12bfcfdee.js" crossorigin="anonymous"></script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
</head>

<body class="font-sans antialiased [&_[x-cloak]]:hidden">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
        @include('layouts.footer')
        <livewire:search />
    </div>
    @livewireScripts
</body>

</html>
