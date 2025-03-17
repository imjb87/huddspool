@extends('layouts.app')

@section('content')
    <livewire:outstanding-fixtures />
    <x-news :news="$news" />
@endsection