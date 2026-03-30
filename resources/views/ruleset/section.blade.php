@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <livewire:ruleset-section-page
        :ruleset="$ruleset"
        :section="$activeSection"
        :initial-tab="$activeTab"
    />
@endsection
