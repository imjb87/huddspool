<!DOCTYPE html>
<html class="h-full bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <script src="https://kit.fontawesome.com/b12bfcfdee.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tiny.cloud/1/gj75uu41to8xotbxkn157xgangktxdjx2xrng6bs3qm02mll/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="font-sans antialiased h-full [&_[x-cloak]]:hidden">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Content -->
        <main>
            <div>
                <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
                <div class="relative z-40 hidden" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

                    <div class="fixed inset-0 z-40 flex">
                        <div class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-800 pt-5 pb-4">
                            <div class="absolute top-0 right-0 -mr-12 pt-2">
                                <button type="button" x-on:click="open = false"
                                    class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                    <span class="sr-only">Close sidebar</span>
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex flex-shrink-0 items-center px-4">
                                <x-application-logo />
                            </div>
                            <div class="mt-5 h-0 flex-1 overflow-y-auto">
                                <nav class="space-y-1 px-2">
                                    <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="{{ Route::is('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <!-- Current: "text-gray-300", Default: "text-gray-400 group-hover:text-gray-300" -->
                                        <svg class="text-gray-300 mr-4 h-6 w-6 flex-shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Dashboard
                                    </a>

                                    <a href="{{ route('admin.venues.index') }}"
                                        class="{{ Route::is('admin.venues.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <svg class="text-gray-400 group-hover:text-gray-300 mr-4 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M208 0c-26.8 0-51.1 11-68.5 28.8l-.1 0C126.9 20.7 112 16 96 16C51.8 16 16 51.8 16 96s35.8 80 80 80c16 0 30.9-4.7 43.4-12.8l.1 0C156.9 181 181.2 192 208 192c26.5 0 50.6-10.8 68-28.2l0 0c12.8 8.5 28.2 13.5 44.6 13.5c44.5 0 80.6-36.1 80.6-80.6s-36.1-80.6-80.6-80.6c-16.1 0-31.2 4.8-43.9 13l0 0C259.3 11.1 234.9 0 208 0zM173.1 63c8.8-9.3 21.1-15 34.9-15c13.8 0 26.2 5.8 35 15.1c8.4 8.9 19.6 12.8 29.3 13.7c9.7 .9 20.9-1 30.4-7.4c5.2-3.4 11.3-5.4 18-5.4c18 0 32.6 14.6 32.6 32.6s-14.6 32.6-32.6 32.6c-6.8 0-13.1-2.1-18.3-5.6c-9.4-6.4-20.6-8.4-30.3-7.7c-9.7 .7-20.9 4.5-29.4 13.3c-8.8 9.1-21 14.7-34.6 14.7c-13.7 0-26.1-5.7-34.9-15c-8.4-8.9-19.6-12.7-29.2-13.6c-9.7-.8-20.8 1.1-30.3 7.3c-5 3.3-11.1 5.3-17.6 5.3c-17.7 0-32-14.3-32-32s14.3-32 32-32c6.5 0 12.6 1.9 17.6 5.3c9.5 6.3 20.6 8.2 30.3 7.3c9.7-.8 20.8-4.7 29.2-13.6zM32 187.9V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64v-.3l86.8-38c25-11 41.2-35.7 41.2-63V228.8c0-38-30.8-68.8-68.8-68.8H413.8c-17.5 25.7-45.5 43.8-77.8 48.2V431.5c0 .4 0 .7 0 1.1V448c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16V206.9c-17.7-2.5-34-9.2-48-18.9zM208 224c-8.8 0-16 7.2-16 16V400c0 8.8 7.2 16 16 16s16-7.2 16-16V240c0-8.8-7.2-16-16-16zM320.3 97.2c.1 0 .1 .1 .1 .1l0 0c0 0 .1 0 .2 0c.1 0 .2 0 .2 0l0 0c0 0 .1-.1 .2-.2s.1-.2 .2-.2l0 0c0 0 0-.1 0-.2s0-.2 0-.2l0 0c0 0-.1-.1-.2-.2s-.2-.1-.2-.2l0 0c0 0-.1 0-.2 0c-.1 0-.1 0-.2 0l0 0s0 0 0 0s0 0-.1 0l-.9 .6 .8 .6zM96 96l0 0 0 0 0 0s0 0 0 0s0 0 0 0l0 0 0 0 0 0 0 0 0 0zM443.2 208c11.5 0 20.8 9.3 20.8 20.8V346.7c0 8.3-4.9 15.7-12.5 19.1L384 395.3V208h59.2zM160 240c0-8.8-7.2-16-16-16s-16 7.2-16 16V400c0 8.8 7.2 16 16 16s16-7.2 16-16V240zm128 0c0-8.8-7.2-16-16-16s-16 7.2-16 16V400c0 8.8 7.2 16 16 16s16-7.2 16-16V240z"/></svg>
                                        Venues
                                    </a>

                                    <a href="{{ route('admin.teams.index') }}"
                                        class="{{ Route::is('admin.teams.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <svg fill="currentColor" class="text-gray-400 group-hover:text-gray-300 mr-4 h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M72 88a56 56 0 1 1 112 0A56 56 0 1 1 72 88zM64 245.7C54 256.9 48 271.8 48 288s6 31.1 16 42.3V245.7zm144.4-49.3C178.7 222.7 160 261.2 160 304c0 34.3 12 65.8 32 90.5V416c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V389.2C26.2 371.2 0 332.7 0 288c0-61.9 50.1-112 112-112h32c24 0 46.2 7.5 64.4 20.3zM448 416V394.5c20-24.7 32-56.2 32-90.5c0-42.8-18.7-81.3-48.4-107.7C449.8 183.5 472 176 496 176h32c61.9 0 112 50.1 112 112c0 44.7-26.2 83.2-64 101.2V416c0 17.7-14.3 32-32 32H480c-17.7 0-32-14.3-32-32zm8-328a56 56 0 1 1 112 0A56 56 0 1 1 456 88zM576 245.7v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM320 32a64 64 0 1 1 0 128 64 64 0 1 1 0-128zM240 304c0 16.2 6 31 16 42.3V261.7c-10 11.3-16 26.1-16 42.3zm144-42.3v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM448 304c0 44.7-26.2 83.2-64 101.2V448c0 17.7-14.3 32-32 32H288c-17.7 0-32-14.3-32-32V405.2c-37.8-18-64-56.5-64-101.2c0-61.9 50.1-112 112-112h32c61.9 0 112 50.1 112 112z"/></svg>
                                        Teams
                                    </a>

                                    <a href="{{ route('admin.users.index') }}"
                                        class="{{ Route::is('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <svg class="text-gray-400 group-hover:text-gray-300 mr-4 h-6 w-6 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        Players
                                    </a>

                                    <a href="{{ route('admin.seasons.index') }}"
                                        class="{{ Route::is('admin.[seasons,sections,fixtures].*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <svg class="text-gray-400 group-hover:text-gray-300 mr-4 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M176.9 48c6.4 160.7 44.3 231.4 71.8 261.7c13.7 15.1 25.9 21.4 33.1 24.1c2.6 1 4.7 1.5 6.1 1.9c1.4-.3 3.5-.9 6.1-1.9c7.2-2.7 19.4-9 33.1-24.1c27.5-30.3 65.5-101 71.8-261.7H176.9zM176 0H400c26.5 0 48.1 21.8 47.1 48.2c-.2 5.3-.4 10.6-.7 15.8H552c13.3 0 24 10.7 24 24c0 108.5-45.9 177.7-101.4 220.6c-53.9 41.7-115.7 57.6-149.5 63.7c-4.7 2.5-9.1 4.5-13.1 6.1V464h80c13.3 0 24 10.7 24 24s-10.7 24-24 24H288 184c-13.3 0-24-10.7-24-24s10.7-24 24-24h80V378.4c-4-1.6-8.4-3.6-13.1-6.1c-33.8-6-95.5-22-149.5-63.7C45.9 265.7 0 196.5 0 88C0 74.7 10.7 64 24 64H129.6c-.3-5.2-.5-10.4-.7-15.8C127.9 21.8 149.5 0 176 0zM390.8 302.6c18.1-8 36.8-18.4 54.4-32c40.6-31.3 75.9-80.2 81.9-158.6H442.7c-9.1 90.1-29.2 150.3-51.9 190.6zm-260-32c17.5 13.6 36.3 24 54.4 32c-22.7-40.3-42.8-100.5-51.9-190.6H48.9c6 78.4 41.3 127.3 81.9 158.6zM295.2 102.5l14.5 29.3c1.2 2.4 3.4 4 6 4.4l32.4 4.7c6.6 1 9.2 9 4.4 13.6l-23.4 22.8c-1.9 1.8-2.7 4.5-2.3 7.1l5.5 32.2c1.1 6.5-5.7 11.5-11.6 8.4l-29-15.2c-2.3-1.2-5.1-1.2-7.4 0l-29 15.2c-5.9 3.1-12.7-1.9-11.6-8.4l5.5-32.2c.4-2.6-.4-5.2-2.3-7.1l-23.4-22.8c-4.7-4.6-2.1-12.7 4.4-13.6l32.4-4.7c2.6-.4 4.9-2 6-4.4l14.5-29.3c2.9-5.9 11.4-5.9 14.3 0z"/></svg>
                                        Seasons
                                    </a>

                                    <a href="{{ route('admin.news.index') }}"
                                        class="{{ Route::is('admin.[news].*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-base font-medium">
                                        <svg class="text-gray-400 group-hover:text-gray-300 mr-4 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/></svg>
                                        News
                                    </a>

                                </nav>
                            </div>
                        </div>

                        <div class="w-14 flex-shrink-0" aria-hidden="true">
                            <!-- Dummy element to force sidebar to shrink to fit close icon -->
                        </div>
                    </div>
                </div>

                <!-- Static sidebar for desktop -->
                <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                    <!-- Sidebar component, swap this element with another sidebar if you like -->
                    <div class="flex min-h-0 flex-1 flex-col bg-gray-800">
                        <div class="flex h-16 flex-shrink-0 items-center bg-gray-900 px-4">
                            <x-application-logo />
                        </div>
                        <div class="flex flex-1 flex-col overflow-y-auto">
                            <nav class="flex-1 space-y-1 px-2 py-4">
                                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                                <a href="{{ route('admin.dashboard') }}"
                                    class="{{ Route::is('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <!-- Current: "text-gray-300", Default: "text-gray-400 group-hover:text-gray-300" -->
                                    <svg class="text-gray-300 mr-3 h-6 w-6 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Dashboard
                                </a>

                                <a href="{{ route('admin.venues.index') }}"
                                    class="{{ Route::is('admin.venues.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M32 0C23.1 0 14.6 3.7 8.6 10.2S-.6 25.4 .1 34.3L28.9 437.7c3 41.9 37.8 74.3 79.8 74.3H272c-10-13.4-16-30-16-48c0-14.7 6.6-27.8 17-36.6c-10.5-11.4-17-26.6-17-43.4c0-18.5 7.8-35.1 20.3-46.8c-12.4-10.9-20.3-26.9-20.3-44.8c0-6.3 1-13.8 4.4-21.5c5.5-12.5 20.5-40.4 52.7-65.3c16.2-12.5 36.3-23.8 60.9-32l10-139.4c.6-8.9-2.4-17.6-8.5-24.1S360.9 0 352 0H32zM73.2 160L66.4 64H317.6l-6.9 96H73.2zM640 292.3c0-3-.5-5.9-1.7-8.6c-8.1-18.4-48.4-91.9-174.3-91.9s-166.2 73.5-174.3 91.9c-1.2 2.7-1.7 5.7-1.7 8.6c0 15.2 12.3 27.5 27.5 27.5H612.5c15.2 0 27.5-12.3 27.5-27.5zM384 239.8a16 16 0 1 1 0 32 16 16 0 1 1 0-32zm64 0a16 16 0 1 1 32 0 16 16 0 1 1 -32 0zm96 0a16 16 0 1 1 0 32 16 16 0 1 1 0-32zm-224 112c-17.7 0-32 14.3-32 32s14.3 32 32 32H608c17.7 0 32-14.3 32-32s-14.3-32-32-32H320zm-16 96c-8.8 0-16 7.2-16 16c0 26.5 21.5 48 48 48H592c26.5 0 48-21.5 48-48c0-8.8-7.2-16-16-16H304z"/></svg>
                                    Venues
                                </a>

                                <a href="{{ route('admin.teams.index') }}"
                                    class="{{ Route::is('admin.teams.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg fill="currentColor" class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 128a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm-16 32C50.1 160 0 210.1 0 272c0 44.7 26.2 83.2 64 101.2V416c0 17.7 14.3 32 32 32h64c17.7 0 32-14.3 32-32V384c0-13.3-10.7-24-24-24s-24 10.7-24 24v16H112l0-16V336l0-128h32c12.6 0 24.3 3.6 34.2 9.9c7.9-14.2 18.1-26.9 30.2-37.6C190.2 167.5 168 160 144 160H112zM64 229.7v84.7C54 303.1 48 288.2 48 272s6-31.1 16-42.3zM496 208h32V336v48 16H496V384c0-13.3-10.7-24-24-24s-24 10.7-24 24v32c0 17.7 14.3 32 32 32h64c17.7 0 32-14.3 32-32V373.2c37.8-18 64-56.5 64-101.2c0-61.9-50.1-112-112-112H496c-24 0-46.2 7.5-64.4 20.3c12 10.7 22.3 23.4 30.2 37.6c9.9-6.3 21.6-9.9 34.2-9.9zm96 64c0 16.2-6 31.1-16 42.3V229.7c10 11.3 16 26.1 16 42.3zM560 80a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM320 144a56 56 0 1 0 0-112 56 56 0 1 0 0 112zm-16 32c-61.9 0-112 50.1-112 112c0 44.7 26.2 83.2 64 101.2V448c0 17.7 14.3 32 32 32h64c17.7 0 32-14.3 32-32V389.2c37.8-18 64-56.5 64-101.2c0-61.9-50.1-112-112-112H304zm0 224V352 224h32l0 128v48 32H304V400zm-48-69.7c-10-11.3-16-26.1-16-42.3s6-31.1 16-42.3v84.7zm128 0V245.7c10 11.3 16 26.1 16 42.3s-6 31.1-16 42.3z"/></svg>
                                    Teams
                                </a>

                                <a href="{{ route('admin.users.index') }}"
                                    class="{{ Route::is('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Players
                                </a>

                                <a href="{{ route('admin.rulesets.index') }}"
                                    class="{{ Route::is('admin.rulesets.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M384 32H512c17.7 0 32 14.3 32 32s-14.3 32-32 32H398.4c-5.2 25.8-22.9 47.1-46.4 57.3V448H512c17.7 0 32 14.3 32 32s-14.3 32-32 32H320 128c-17.7 0-32-14.3-32-32s14.3-32 32-32H288V153.3c-23.5-10.3-41.2-31.6-46.4-57.3H128c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c14.6-19.4 37.8-32 64-32s49.4 12.6 64 32zm55.6 288H584.4L512 195.8 439.6 320zM512 416c-62.9 0-115.2-34-126-78.9c-2.6-11 1-22.3 6.7-32.1l95.2-163.2c5-8.6 14.2-13.8 24.1-13.8s19.1 5.3 24.1 13.8l95.2 163.2c5.7 9.8 9.3 21.1 6.7 32.1C627.2 382 574.9 416 512 416zM126.8 195.8L54.4 320H199.3L126.8 195.8zM.9 337.1c-2.6-11 1-22.3 6.7-32.1l95.2-163.2c5-8.6 14.2-13.8 24.1-13.8s19.1 5.3 24.1 13.8l95.2 163.2c5.7 9.8 9.3 21.1 6.7 32.1C242 382 189.7 416 126.8 416S11.7 382 .9 337.1z"/></svg>
                                    Rulesets
                                </a>

                                <a href="{{ route('admin.seasons.index') }}"
                                    class="{{ Route::is('admin.seasons.*','admin.sections.*','admin.fixtures.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M176.9 48c6.4 160.7 44.3 231.4 71.8 261.7c13.7 15.1 25.9 21.4 33.1 24.1c2.6 1 4.7 1.5 6.1 1.9c1.4-.3 3.5-.9 6.1-1.9c7.2-2.7 19.4-9 33.1-24.1c27.5-30.3 65.5-101 71.8-261.7H176.9zM176 0H400c26.5 0 48.1 21.8 47.1 48.2c-.2 5.3-.4 10.6-.7 15.8H552c13.3 0 24 10.7 24 24c0 108.5-45.9 177.7-101.4 220.6c-53.9 41.7-115.7 57.6-149.5 63.7c-4.7 2.5-9.1 4.5-13.1 6.1V464h80c13.3 0 24 10.7 24 24s-10.7 24-24 24H288 184c-13.3 0-24-10.7-24-24s10.7-24 24-24h80V378.4c-4-1.6-8.4-3.6-13.1-6.1c-33.8-6-95.5-22-149.5-63.7C45.9 265.7 0 196.5 0 88C0 74.7 10.7 64 24 64H129.6c-.3-5.2-.5-10.4-.7-15.8C127.9 21.8 149.5 0 176 0zM390.8 302.6c18.1-8 36.8-18.4 54.4-32c40.6-31.3 75.9-80.2 81.9-158.6H442.7c-9.1 90.1-29.2 150.3-51.9 190.6zm-260-32c17.5 13.6 36.3 24 54.4 32c-22.7-40.3-42.8-100.5-51.9-190.6H48.9c6 78.4 41.3 127.3 81.9 158.6zM295.2 102.5l14.5 29.3c1.2 2.4 3.4 4 6 4.4l32.4 4.7c6.6 1 9.2 9 4.4 13.6l-23.4 22.8c-1.9 1.8-2.7 4.5-2.3 7.1l5.5 32.2c1.1 6.5-5.7 11.5-11.6 8.4l-29-15.2c-2.3-1.2-5.1-1.2-7.4 0l-29 15.2c-5.9 3.1-12.7-1.9-11.6-8.4l5.5-32.2c.4-2.6-.4-5.2-2.3-7.1l-23.4-22.8c-4.7-4.6-2.1-12.7 4.4-13.6l32.4-4.7c2.6-.4 4.9-2 6-4.4l14.5-29.3c2.9-5.9 11.4-5.9 14.3 0z"/></svg>
                                    Seasons
                                </a>

                                <a href="{{ route('admin.news.index') }}"
                                    class="{{ Route::is('admin.news.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center rounded-md px-2 py-2 text-sm font-medium">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 h-6 w-6 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/></svg>
                                    News
                                </a>                                
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col lg:pl-64">
                    <div
                        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                        <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" x-on:click="open = true">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <!-- Separator -->
                        <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>

                        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-end">
                            <div class="flex items-center gap-x-2">
                                <!-- Profile dropdown -->
                                <div class="relative">
                                    <button type="button" class="-m-1.5 flex items-center p-1.5"
                                        id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="hidden lg:flex lg:items-center">
                                            <span class="ml-4 text-sm font-semibold leading-6 text-gray-900"
                                                aria-hidden="true">{{ Auth::user()->name }}</span>
                                        </span>
                                    </button>
                                </div>
                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left p-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-md"
                                        role="menuitem">
                                        <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 512 512">
                                            <!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                            <path
                                                d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <main class="flex-1">
                        <div class="py-6">
                            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                {{ $slot }}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </main>
    </div>
    @livewireScripts
</body>

</html>
