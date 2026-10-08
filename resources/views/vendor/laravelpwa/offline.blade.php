<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#166534">
    <meta name="color-scheme" content="light dark">
    <title>You’re offline | {{ config('app.name', 'Huddspool') }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: #f5f5f5;
            color: #171717;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
                background: #0a0a0a;
                color: #fafafa;
            }
        }

        body {
            box-sizing: border-box;
            display: grid;
            min-height: 100svh;
            margin: 0;
            padding: 1rem;
            place-items: center;
        }

        main {
            width: min(100%, 28rem);
            padding: 2rem;
            border: 1px solid color-mix(in srgb, currentColor 12%, transparent);
            border-radius: 0.75rem;
            background: color-mix(in srgb, currentColor 4%, transparent);
            text-align: center;
        }

        img {
            width: 4rem;
            height: 4rem;
            margin-bottom: 1.5rem;
            border-radius: 1rem;
        }

        h1 {
            margin: 0;
            font-size: 1.5rem;
            line-height: 2rem;
            letter-spacing: -0.025em;
        }

        p {
            margin: 0.75rem 0 0;
            color: color-mix(in srgb, currentColor 65%, transparent);
            font-size: 0.9375rem;
            line-height: 1.5rem;
        }

        a {
            display: inline-flex;
            margin-top: 1.5rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            background: #166534;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.25rem;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <main>
        <img src="{{ asset('images/icons/icon-192-192.png') }}" alt="">
        <h1>You’re offline</h1>
        <p>Reconnect to the internet to view the latest league information.</p>
        <a href="{{ route('home') }}">Try again</a>
    </main>
</body>

</html>
