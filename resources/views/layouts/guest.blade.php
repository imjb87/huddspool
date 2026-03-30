<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 text-gray-900 dark:bg-zinc-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.description') }}">

    <title>Login | {{ config('app.name', 'Huddersfield & District Tuesday Night Pool League') }}</title>

    @include('layouts.partials.theme-head')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
</head>

<body
    class="bg-gray-50 font-sans text-gray-900 antialiased dark:bg-zinc-900 dark:text-gray-100"
    @if (filled(config('services.google_analytics.measurement_id')))
        data-google-analytics-measurement-id="{{ config('services.google_analytics.measurement_id') }}"
    @endif
>
    <div class="min-h-screen">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-12 lg:px-6 lg:py-14">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
