@extends('layouts.app')

@section('content')
    <livewire:ruleset-section-page
        :ruleset="$ruleset"
        :section="$activeSection"
        :initial-tab="$activeTab"
    />

    <x-logo-clouds variant="section-showcase" />
@endsection
