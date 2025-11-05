@extends('layouts.app')

@section('content')
<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h1 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">
                        {{ $season->name }}
                    </h1>
                    <p class="ml-2 mt-1 truncate text-sm text-gray-500">
                        {{ $ruleset->name }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 lg:grid-cols-2">
                @forelse ($sections as $section)
                    <livewire:standings.show :section="$section" :key="'standings-'.$section->id" />
                    <livewire:player.section-show :section="$section" :history="true" :key="'players-'.$section->id" />
                @empty
                    <div class="lg:col-span-2">
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            No historical standings available for this ruleset in {{ $season->name }}.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <x-logo-clouds class="pt-8 sm:pt-10 pb-12 sm:pb-16" />
</div>
@endsection
