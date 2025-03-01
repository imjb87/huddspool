<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                  <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">{{ $knockout->name }}</h3>
                </div>
            </div>    
            <div class="grid grid-cols-1 gap-x-6 gap-y-6">
                @foreach ( $knockout->rounds as $round )
                    <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
                        <div class="px-4 py-4 bg-green-700">
                            <h2 class="text-sm font-medium leading-6 text-white">{{ $round->name }}</h2>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="bg-white border-b border-gray-300">
                                @if ( $round->knockout->type == 'team')
                                @foreach ( $matches as $match )
                                    <a href="{{ route('team.show', $match->team1) }}" class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50">
                                        <div class="whitespace-nowrap py-2 text-sm text-gray-900 text-right font-semibold w-5/12">
                                            {{ $match->team1->name }}
                                        </div>
                                        <div class="whitespace nowrap py-2 text-sm text-gray-500 text-center font-semibold w-[100px]">
                                            <div class="inline-flex bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold divide-x-2 divide-x-white">
                                                <div class="w-1/2">{{ $match->score1 }}</div>
                                                <div class="w-1/2">{{ $match->score2 }}</div>
                                            </div>
                                        </div>
                                        <div class="whitespace-nowrap py-2 text-sm text-gray-900 text-left font-semibold w-5/12">
                                            {{ $match->team2->name }}
                                        </div>
                                    </a>
                                @endforeach
                                @else
                                @foreach ( $round->matches->load('venue')->groupBy('venue') as $venue => $matches )
                                    <div class="border-t border-gray-200">
                                        <a href="{{ route('venue.show', $matches->first()->venue) }}" colspan="5" scope="colgroup"
                                            class="block bg-gray-50 py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3 text-center">
                                            {{ $matches->first()->venue->name }}
                                        </a>
                                        @foreach( $matches as $match)
                                            <div
                                                class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 items-center justify-center min-h-[45px]">
                                                <div
                                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-right font-semibold w-5/12">
                                                    @if ( $match->round->knockout->type->value == 'singles')
                                                        @if ( !$match->player1 )
                                                            Winner of above
                                                        @else
                                                            {{ $match->player1->name }}
                                                        @endif
                                                    @elseif ( $match->round->knockout->type->value == 'doubles')
                                                        @if (empty(array_filter($match->pair1)))
                                                            Winner of above
                                                        @else
                                                            {{ $match->pair1Player1->name }}<br>{{ $match->pair1Player2->name }}
                                                        @endif
                                                    @else
                                                        {{ $match->team1->name }}
                                                    @endif
                                                </div>
                                                <div
                                                    class="whitespace-nowrap py-2 text-sm text-gray-500 text-center font-semibold w-[100px]">
                                                    @if ( !$match->score1 && !$match->score2 )
                                                        {{ $match->round->date->format('d/m') }}
                                                    @else
                                                        <div class="inline-flex bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold divide-x-2 divide-x-white">
                                                            <div class="w-1/2">{{ $match->score1 }}</div>
                                                            <div class="w-1/2">{{ $match->score2 }}</div>
                                                        </div>
                                                    @endif
                                                    </div>
                                                <div
                                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-left font-semibold w-5/12">
                                                    @if ( $match->round->knockout->type->value == 'singles')
                                                        @if ( !$match->player2 )
                                                            Winner of above
                                                        @else
                                                            {{ $match->player2->name }}
                                                        @endif
                                                    @elseif ( $match->round->knockout->type->value == 'doubles')
                                                        @if (empty(array_filter($match->pair2)))
                                                            Winner of above
                                                        @else
                                                            {{ $match->pair2Player1->name }}<br>{{ $match->pair2Player2->name }}
                                                        @endif
                                                    @else
                                                        {{ $match->team2->name }}
                                                    @endif
                                                    </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>                
        </div>
    </div>
    <x-logo-clouds />
</div>