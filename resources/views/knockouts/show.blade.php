@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 lg:pb-14 dark:bg-zinc-900">
        <livewire:knockout.show :knockout="$knockout" />

        <x-logo-clouds  />
    </div>
@endsection
