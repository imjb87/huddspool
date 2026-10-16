@extends('layouts.app')

@section('title', 'Play 8-ball')
@section('meta_description', 'Play a friendly two-player game of 8-ball in the Huddspool virtual clubroom.')

@section('content')
    <div class="ui-page-shell !max-w-none !px-0 pool-host" data-pool-host data-game-state="loading"
        data-player-id="{{ auth()->id() ? 'user-'.auth()->id() : 'local-1' }}"
        data-player-name="{{ auth()->user()?->name ?? 'Player 1' }}"
        data-player-avatar="{{ auth()->user()?->avatar_url ?? '' }}"
        data-return-url="{{ route('home') }}">
        <div class="pool-startup-message" role="status">Opening the table…</div>
        <noscript>Enable JavaScript to play 8-ball.</noscript>
    </div>
    @vite('resources/js/pool-host.js')
@endsection
