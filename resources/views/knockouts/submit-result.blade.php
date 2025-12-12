@extends('layouts.app')

@section('content')
    <div class="pt-[80px]">
        <div class="py-8 sm:py-16">
            <div class="mx-auto max-w-3xl px-4 lg:px-8">
                <div class="border-b border-gray-200 pb-2 mb-6">
                    <h1 class="text-base font-semibold leading-6 text-gray-900">Submit knockout result</h1>
                    <p class="text-sm text-gray-500">
                        {{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}
                        &middot;
                        {{ $match->round?->name ?? 'Unscheduled round' }}
                    </p>
                </div>
                <livewire:knockout.submit-result :match="$match" />
            </div>
        </div>
    </div>
@endsection
