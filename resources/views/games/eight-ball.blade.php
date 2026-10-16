@extends('layouts.app')

@section('title', 'Play 8-ball')
@section('meta_description', 'Play a friendly two-player game of 8-ball in the Huddspool virtual clubroom.')

@section('content')
    <div class="ui-page-shell !max-w-none !px-0" data-pool-host
        data-player-id="{{ auth()->id() ? 'user-'.auth()->id() : 'local-1' }}"
        data-player-name="{{ auth()->user()?->name ?? 'Player 1' }}"
        data-player-avatar="{{ auth()->user()?->avatar_url ?? '' }}"
        data-return-url="{{ route('home') }}">
        <iframe src="{{ asset('games/8-ball/index.html') }}" title="Huddspool two-player 8-ball"
            class="block min-h-[850px] w-full border-0 bg-[#101c19]" style="height: max(850px, calc(100dvh - 64px))"
            allow="autoplay; fullscreen" data-pool-frame></iframe>
        <noscript>Enable JavaScript to play 8-ball.</noscript>
    </div>
    @vite('resources/js/pool-host.js')
@endsection
