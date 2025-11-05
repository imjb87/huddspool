@extends('layouts.app')

@section('content')
<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl font-serif">History</h1>
                <p class="mt-2 text-sm text-gray-600">Browse archived seasons and revisit standings and player averages for each ruleset.</p>
            </div>

            @forelse ($seasonGroups as $group)
                <section class="mb-8 bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 bg-green-700">
                        <h2 class="text-sm font-medium leading-6 text-white flex items-center justify-between">
                            <span>{{ $group['season']->name }}</span>
                            <span class="text-xs text-green-100">{{ $group['season']->slug }}</span>
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse ($group['rulesets'] as $ruleset)
                            <a href="{{ route('history.show', [$group['season'], $ruleset]) }}"
                                class="flex items-center justify-between px-4 sm:px-6 py-4 hover:bg-gray-50 transition">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $ruleset->name }}</h3>
                                    <p class="text-xs text-gray-500">View standings & averages</p>
                                </div>
                                <x-heroicon-o-arrow-right class="h-4 w-4 text-gray-400" />
                            </a>
                        @empty
                            <div class="px-4 sm:px-6 py-6 text-sm text-gray-500">No rulesets recorded for this season.</div>
                        @endforelse
                    </div>
                </section>
            @empty
                <div class="text-center text-sm text-gray-500">No historical seasons available yet.</div>
            @endforelse
        </div>
    </div>
    <x-logo-clouds class="pt-8 sm:pt-10 pb-12 sm:pb-16" />
</div>
@endsection
