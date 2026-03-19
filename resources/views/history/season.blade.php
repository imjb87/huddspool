@extends('layouts.app')

@section('content')
<div class="bg-gray-50 pt-[80px] dark:bg-zinc-900">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">Season overview</h3>
                    <p class="ml-2 mt-1 truncate text-sm text-gray-500 dark:text-gray-400">{{ $season->name }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-x-6 gap-y-6">
                <section class="-mx-4 sm:m-0">
                    <div class="overflow-hidden bg-white shadow-md sm:rounded-lg dark:bg-zinc-800/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
                        <div class="px-4 py-4 bg-green-700 flex justify-between items-center">
                            <h2 class="text-sm font-medium leading-6 text-white">Season Summary</h2>
                        </div>
                        <div class="border-t border-gray-200 dark:border-zinc-800/80">
                            <div class="w-full max-w-full overflow-hidden">
                                <div class="bg-gray-50 dark:bg-zinc-800/70">
                                    <div class="flex">
                                        <div class="flex w-3/12 pl-4">
                                            <div scope="col"
                                                class="py-2 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 w-full truncate">
                                                Section
                                            </div>
                                        </div>
                                        <div class="flex w-9/12 pr-2">
                                            <div scope="col"
                                                class="py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 w-full truncate">
                                                Winner
                                            </div>
                                            <div scope="col"
                                                class="py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 w-full truncate">
                                                Runner-up
                                            </div>
                                            <div scope="col"
                                                class="py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 w-full truncate">
                                                Averages Winner
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white dark:bg-transparent">
                                    @foreach ($overview as $entry)
                                    <div class="flex border-t border-gray-200 dark:border-zinc-800/80">
                                        <div class="flex w-3/12 pl-4 items-center">
                                            <div
                                                class="py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100 w-full truncate">
                                                {{ $entry['section']->name }}
                                            </div>
                                        </div>
                                        <div class="flex w-9/12 pr-2 items-center">
                                            <div
                                                class="py-2 text-sm text-gray-900 dark:text-gray-100 w-full truncate">
                                                {{ $entry['winner']->name }}
                                            </div>
                                            <div
                                                class="py-2 text-sm text-gray-900 dark:text-gray-100 w-full truncate">
                                                {{ $entry['runner_up']->name }}
                                            </div>
                                            <div
                                                class="py-2 text-sm text-gray-900 dark:text-gray-100 w-full truncate">
                                                @if ($entry['average_winner'])
                                                <div class="flex items-center gap-x-2">
                                                    <img class="h-6 w-6 rounded-full object-cover hidden sm:inline"
                                                        src="{{ $entry['average_winner']->avatar_url }}"
                                                        alt="{{ $entry['average_winner']->name }} avatar">
                                                    <span class="truncate">{{ $entry['average_winner']->name }}</span>
                                                    @if (! empty($entry['average_winner']->team_name))
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap -mx-1 hidden sm:inline">
                                                        ({{ $entry['average_winner']->team_name }})
                                                    </span>
                                                    @endif
                                                </div>
                                                @else
                                                <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <x-logo-clouds variant="section-showcase" />
</div>
@endsection
