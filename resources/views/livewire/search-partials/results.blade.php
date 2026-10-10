<ul class="min-h-80 max-h-[28rem] overflow-y-auto scroll-py-1.5" id="options"
    x-on:keydown.up.prevent="$focus.wrap().previous()"
    x-on:keydown.down.prevent="$focus.wrap().next()"
    role="listbox"
    data-search-results-shell>
    @foreach ($resultGroups as $name => $group)
        <li wire:key="search-group-{{ $name }}">
            <div class="px-3 pt-3 pb-1">
                <h2 class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    {{ $group['heading'] }}
                </h2>
            </div>
            <div class="space-y-0.5 pb-1.5" data-search-result-group>
                @foreach ($group['results'] as $item)
                    @if (is_object($item))
                        <a class="flex h-9 w-full items-center justify-between gap-4 rounded-md border border-transparent px-3 text-sm font-medium outline-none transition-colors hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100"
                            href="{{ route($group['route'] . '.show', $item->id) }}"
                            data-search-result-link
                            wire:key="search-result-{{ $name }}-{{ $item->id }}"
                            x-on:click="navigateToResult($event)">
                            <div class="flex min-w-0 flex-1 items-center gap-2">
                                @if ($name === 'players')
                                    <span class="relative flex size-6 shrink-0 overflow-hidden rounded-full bg-gray-100 select-none dark:bg-neutral-800" data-search-player-avatar aria-hidden="true">
                                        <img src="{{ $item->avatar_url }}" alt="" class="aspect-square size-full object-cover">
                                    </span>
                                @endif
                                <p class="min-w-0 truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
                            </div>
                            <div class="max-w-[45%] shrink-0 truncate text-xs font-normal text-gray-500 dark:text-gray-400">
                                @if ($name === 'players')
                                    {{ $item->team?->name ?? 'No team assigned' }}
                                @elseif ($name === 'teams')
                                    {{ $item->openSection()?->name ?? 'No open section' }}
                                @elseif ($name === 'venues')
                                    {{ $item->address }}
                                @endif
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </li>
    @endforeach
</ul>
