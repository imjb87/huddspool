@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-account-page>
        <livewire:account.show />

        <x-logo-clouds />
    </div>
@endsection
