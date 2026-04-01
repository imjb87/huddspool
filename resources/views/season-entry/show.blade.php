@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-neutral-950">
        <livewire:season-entry.wizard :season="$season" />
    </div>
@endsection
