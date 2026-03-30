@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <livewire:history.section-page
        :season="$season"
        :ruleset="$ruleset"
        :section="$section"
        :initial-tab="request('tab', 'tables')"
    />
@endsection
