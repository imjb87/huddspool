@extends('layouts.app')

@section('content')
    @include('home.partials.hero')
    @include('home.partials.live-scores')
    @include('home.partials.news')

    <x-logo-clouds class="pt-10 sm:pt-12"  />
@endsection
