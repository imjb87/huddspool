<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'Laravel') }}</title>

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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
</head>

<body class="font-sans text-gray-900 antialiased [&_[x-cloak]]:hidden">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">

        <div>
            <a href="/">
                <img class="w-36" src="{{ asset('images/logo.png') }}" alt="Huddersfield Pool Logo" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-4 py-4 bg-white shadow-md overflow-hidden rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
