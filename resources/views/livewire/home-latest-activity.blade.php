@php
    use Illuminate\Support\Str;
@endphp

<section class="bg-white shadow sm:rounded-lg overflow-hidden -mx-4 sm:mx-0" wire:poll.{{ $pollInterval }}s>
    <div class="px-4 py-4 sm:px-6 bg-green-700 flex flex-wrap items-center gap-3 text-white">
        <h2 class="text-sm font-medium leading-6 uppercase tracking-wide">Latest activity</h2>
        <span class="ml-auto text-xs font-semibold text-green-50">Auto refresh · {{ $pollInterval }}s</span>
    </div>
    <div class="border-t border-gray-200">
        <div class="hidden md:flex bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase tracking-wide text-gray-600">
            <div class="w-4/12 px-3 py-2 text-right">Home</div>
            <div class="w-2/12 px-3 py-2 text-center">Score</div>
            <div class="w-4/12 px-3 py-2 text-left">Away</div>
            <div class="w-2/12 px-3 py-2 text-center">Status</div>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse ($updates as $update)
                @php
                    /** @var \App\Data\HomeActivityItem $item */
                    $item = $update['item'];
                    $homeLabel = $item->home_team_shortname ?? Str::limit($item->home_team_name, 22);
                    $awayLabel = $item->away_team_shortname ?? Str::limit($item->away_team_name, 22);
                    $scoreboardColour = $item->is_confirmed ? 'bg-green-700' : 'bg-amber-500';
                    $statusCopy = $item->is_confirmed ? 'Final' : 'Draft';
                    $statusBadge = $item->is_confirmed ? 'text-green-700 bg-green-50 ring-green-600/20' : 'text-amber-700 bg-amber-50 ring-amber-600/20';
                @endphp
                <div class="flex flex-wrap md:flex-nowrap items-center w-full bg-white hover:bg-gray-50 transition">
                    <div class="w-full md:w-4/12 px-3 py-4 text-sm font-semibold text-right text-gray-900">
                        <span class="md:hidden block text-xs uppercase tracking-wide text-gray-500 mb-1">Home</span>
                        {{ $homeLabel }}
                    </div>
                    <div class="w-full md:w-2/12 px-3 py-4 flex justify-center">
                        <div class="inline-flex {{ $scoreboardColour }} text-white text-center text-sm leading-7 min-w-[52px] font-bold rounded-md divide-x-2 divide-x-white">
                            <div class="w-1/2">{{ $item->home_score }}</div>
                            <div class="w-1/2">{{ $item->away_score }}</div>
                        </div>
                    </div>
                    <div class="w-full md:w-4/12 px-3 py-4 text-sm font-semibold text-left text-gray-900">
                        <span class="md:hidden block text-xs uppercase tracking-wide text-gray-500 mb-1">Away</span>
                        {{ $awayLabel }}
                    </div>
                    <div class="w-full md:w-2/12 px-3 py-4 flex flex-col items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBadge }}">
                            {{ $statusCopy }}
                        </span>
                        <p class="text-[11px] text-gray-500">Updated {{ $item->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="w-full px-3 pb-4 md:pb-0 md:px-3 md:w-auto md:ml-auto flex justify-end gap-3">
                        @if ($update['canResume'])
                            <a href="{{ route('result.create', $item->fixture_id) }}"
                                class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-md text-white bg-green-700 hover:bg-green-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                                Resume
                            </a>
                        @endif
                        <a href="{{ route('result.show', $item->result_id) }}"
                            class="inline-flex items-center text-xs font-semibold text-green-700 hover:text-green-600">
                            Details
                            <svg class="ml-1 h-4 w-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M5.22 12.78a.75.75 0 0 1 0-1.06L8.94 8 5.22 4.28a.75.75 0 0 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0Z" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-600">
                    No recent updates yet — check back soon once results start coming in.
                </div>
            @endforelse
        </div>
    </div>
</section>
