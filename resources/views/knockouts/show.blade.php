@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell">
        <div class="ui-section" data-section-shared-header data-knockout-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Knockouts', 'url' => route('knockout.index')],
                    ['label' => $knockout->name, 'current' => true],
                ]" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $knockout->name }}</h1>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <livewire:knockout.show :knockout="$knockout" />
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
