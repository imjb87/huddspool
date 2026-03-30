@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-home-page>
        @include('home.partials.hero')
        @include('home.partials.live-scores')
        @include('home.partials.news')

        <x-logo-clouds />
    </div>
@endsection
