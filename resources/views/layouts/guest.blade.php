<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background text-foreground">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.description') }}">

    @php
        $guestTitle = match (true) {
            request()->routeIs('password.request') => 'Forgot password',
            request()->routeIs('password.reset') => 'Reset password',
            request()->routeIs('invite.register') => 'Set password',
            request()->routeIs('passport.authorizations.authorize') => 'Connect Huddspool',
            default => 'Log in',
        };
    @endphp

    <title>{{ $guestTitle }} | {{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}</title>

    @include('layouts.partials.theme-head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
</head>

<body
    class="bg-background font-sans text-foreground antialiased"
    @if (filled(config('services.google_analytics.measurement_id')))
        data-google-analytics-measurement-id="{{ config('services.google_analytics.measurement_id') }}"
    @endif
>
    <div class="min-h-svh bg-background">
        <header class="mx-auto flex h-16 w-full max-w-4xl items-center px-4 sm:px-6 lg:px-6">
            <a href="{{ route('home') }}"
                class="inline-flex h-8 w-10 items-center justify-center rounded-md transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
            >
                <span class="sr-only">Huddersfield &amp; District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </header>

        <main class="mx-auto w-full max-w-4xl px-4 pt-10 pb-12 sm:px-6 sm:pt-12 lg:px-6 lg:pt-14">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
