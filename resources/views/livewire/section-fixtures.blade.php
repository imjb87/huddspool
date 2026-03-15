<section data-section-fixtures-view class="mt-0">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-section-fixtures-shell>
        <div class="min-w-full overflow-hidden">
            <div class="bg-linear-to-b from-gray-50 to-gray-100">
                <div class="mx-auto flex w-full max-w-4xl" data-section-fixtures-band>
                    <div scope="col" class="w-[40%] py-2 pl-4 text-right text-sm font-semibold text-gray-900 sm:pl-6">
                        Home
                    </div>
                    <div scope="col" class="w-[20%] px-1 py-2 text-center text-sm font-semibold text-gray-900"></div>
                    <div scope="col" class="w-[40%] py-2 pr-4 text-left text-sm font-semibold text-gray-900 sm:pr-6">
                        Away
                    </div>
                </div>
            </div>

            <div class="bg-white border-b border-gray-300" wire:loading.remove wire:target="previousWeek, nextWeek">
                @forelse ($fixtures as $fixture)
                    @php
                        $isByeFixture = $fixture->home_team_id == 1 || $fixture->away_team_id == 1;
                    @endphp

                    @if ($isByeFixture)
                        <div class="block w-full border-t border-gray-300 bg-gray-50"
                            wire:key="section-fixture-{{ $section->id }}-{{ $fixture->id }}">
                    @else
                        <a class="block w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                            wire:key="section-fixture-{{ $section->id }}-{{ $fixture->id }}"
                            href="{{ $fixture->result ? route('result.show', $fixture->result) : route('fixture.show', $fixture) }}">
                    @endif

                    <div class="mx-auto flex w-full max-w-4xl" data-section-fixtures-band>
                        <div class="w-[40%] py-4 pl-4 text-right text-sm text-gray-900 sm:pl-6">
                            <span class="{{ $fixture->homeTeam->shortname ? 'hidden md:inline' : '' }}">
                                {{ $fixture->homeTeam->name }}
                            </span>
                            @if ($fixture->homeTeam->shortname)
                                <span class="md:hidden">
                                    {{ $fixture->homeTeam->shortname }}
                                </span>
                            @endif
                        </div>

                        <div class="flex w-[20%] items-center justify-center px-1 py-3 text-sm font-semibold text-gray-500">
                            @if ($fixture->result)
                                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                    data-section-fixtures-score-pill>
                                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                        {{ $fixture->result->home_score ?? '' }}
                                    </div>
                                    <div class="w-px bg-white/25"></div>
                                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                        {{ $fixture->result->away_score ?? '' }}
                                    </div>
                                </div>
                            @else
                                <span class="whitespace-nowrap text-center text-sm font-semibold text-gray-500">
                                    {{ $fixture->fixture_date->format('d/m') }}
                                </span>
                            @endif
                        </div>

                        <div class="w-[40%] py-4 pr-4 text-left text-sm text-gray-900 sm:pr-6">
                            <span class="{{ $fixture->awayTeam->shortname ? 'hidden md:inline' : '' }}">
                                {{ $fixture->awayTeam->name }}
                            </span>
                            @if ($fixture->awayTeam->shortname)
                                <span class="md:hidden">
                                    {{ $fixture->awayTeam->shortname }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($isByeFixture)
                        </div>
                    @else
                        </a>
                    @endif
                @empty
                    <div class="px-4 py-10 text-center sm:px-6">
                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8">
                            <h3 class="text-sm font-semibold text-gray-900">No fixtures available for this week.</h3>
                            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                                Try another week to see upcoming fixtures or submitted results for this section.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="animate-pulse border-b border-gray-300 bg-white" wire:loading.block wire:target="previousWeek, nextWeek" data-section-fixtures-row-skeleton>
                @foreach (range(1, 5) as $row)
                    <div class="border-t border-gray-300" data-section-fixtures-row-skeleton-row>
                        <div class="mx-auto flex w-full max-w-4xl items-center" data-section-fixtures-band>
                            <div class="w-[41%] py-3.5 pl-4 text-right sm:pl-6">
                                <div class="ml-auto h-4 w-28 rounded-full bg-gray-200"></div>
                            </div>

                            <div class="flex w-[18%] items-center justify-center px-1 py-2.5">
                                <div class="h-7 w-[44px] rounded-sm bg-gray-200"></div>
                            </div>

                            <div class="w-[41%] py-3.5 pr-4 text-left sm:pr-6">
                                <div class="h-4 w-28 rounded-full bg-gray-200"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 py-4 sm:px-6 lg:px-6 lg:py-6" data-section-fixtures-controls>
        <div class="flex w-full" data-section-fixtures-band>
            <div class="flex w-[41%] justify-start">
                <button wire:click="previousWeek" wire:loading.attr="disabled"
                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Previous"
                    {{ $week == 1 ? 'disabled' : '' }}>
                    Previous
                </button>
            </div>

            <div class="flex w-[18%] items-center justify-center">
                <span class="text-sm font-semibold text-gray-900">
                    Week {{ $week }}
                </span>
            </div>

            <div class="flex w-[41%] justify-end">
                <button wire:click="nextWeek" wire:loading.attr="disabled"
                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Next"
                    {{ $week >= 18 ? 'disabled' : '' }}>
                    Next
                </button>
            </div>
        </div>
    </div>
</section>
