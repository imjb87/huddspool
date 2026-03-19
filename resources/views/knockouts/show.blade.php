@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 lg:pb-14">
        <livewire:knockout.show :knockout="$knockout" />

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
