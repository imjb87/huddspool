@extends('layouts.app')

@section('content')
    <livewire:ruleset-section-page
        :ruleset="$ruleset"
        :section="$activeSection"
        :initial-tab="$activeTab"
    />
@endsection
