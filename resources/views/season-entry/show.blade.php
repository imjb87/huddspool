@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <livewire:season-entry.wizard :season="$season" />
    </div>
@endsection
