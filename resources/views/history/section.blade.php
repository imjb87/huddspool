@extends('layouts.app')

@section('content')
    <livewire:history.section-page
        :season="$season"
        :ruleset="$ruleset"
        :section="$section"
        :initial-tab="request('tab', 'tables')"
    />

    <x-logo-clouds  />
@endsection
