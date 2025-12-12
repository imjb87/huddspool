@extends('layouts.app')

@section('content')
    <div class="pt-[80px]">
        <div class="py-8 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <div class="border-b border-gray-200 pb-2 mb-8">
                    <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                        <h1 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Knockouts</h1>
                        <p class="ml-2 mt-2 text-sm text-gray-500">Browse every active knockout competition.</p>
                    </div>
                </div>

                @forelse ($knockoutGroups as $seasonName => $seasonKnockouts)
                    <div class="mb-10">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ $seasonName }}</h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($seasonKnockouts as $knockout)
                                <a href="{{ route('knockout.show', $knockout) }}"
                                    class="rounded-lg border border-gray-200 bg-white px-4 py-4 shadow-sm transition hover:border-green-600 hover:shadow-md">
                                    <p class="text-base font-semibold text-gray-900">{{ $knockout->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $knockout->type?->getLabel() }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">No knockouts have been published yet.</p>
                @endforelse
            </div>
        </div>
        <x-logo-clouds />
    </div>
@endsection
