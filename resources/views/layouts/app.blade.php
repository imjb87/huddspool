<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta property="og:title" content="Huddersfield & District Tuesday Night Pool League" />
        <meta property="og:description" content="Every Tuesday night, teams from across Huddersfield and the surrounding areas compete in the Huddersfield & District Tuesday Night Pool League. Check here for the latest tables, fixtures, results and averages." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://www.huddspool.co.uk" />
        <meta property="og:image" content="{{ asset('images/og-image.jpg') }}" />
        
        <link rel="favicon" href="{{ asset('images/favicon.svg') }}" type="image/svg">

        @if (Route::currentRouteName() == 'result.create')
            <meta http-equiv="refresh" content="600">
        @endif

        <title>{{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}</title>

        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
      
            gtag('config', 'G-620MNWY15S');
        </script>    

        <!-- Fonts -->
        <script src="https://kit.fontawesome.com/b12bfcfdee.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
