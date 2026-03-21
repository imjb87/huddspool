<ul class="max-h-[28rem] overflow-y-auto px-3 py-3 sm:px-4" id="options"
    x-on:keydown.up.prevent="$focus.wrap().previous()"
    x-on:keydown.down.prevent="$focus.wrap().next()"
    role="listbox"
    data-search-results-shell>
    @foreach ($resultGroups as $name => $group)
        <li class="border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80" wire:key="search-group-{{ $name }}">
            <div class="px-1 pb-2 pt-3">
                <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                    {{ $group['heading'] }}</h2>
            </div>
            <div class="space-y-1 text-sm text-gray-800 dark:text-gray-200" data-search-result-group>
                @foreach ($group['results'] as $item)
                    @if (is_object($item))
                        <a class="flex items-start justify-between gap-4 rounded-lg border border-transparent px-4 py-3 transition duration-200 hover:border-gray-200 hover:bg-gray-200/70 focus:border-gray-200 focus:bg-gray-200/75 focus:outline-none dark:hover:border-zinc-800 dark:hover:bg-zinc-800/80 dark:focus:border-zinc-800 dark:focus:bg-zinc-800/80"
                            href="{{ route($group['route'] . '.show', $item->id) }}"
                            data-search-result-link
                            wire:key="search-result-{{ $name }}-{{ $item->id }}"
                            x-on:click="close()">
                            <div class="min-w-0 flex flex-1 items-start gap-3">
                                @if ($name === 'players')
                                    <img src="{{ $item->avatar_url }}"
                                        alt="{{ $item->name }}"
                                        class="mt-0.5 h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700"
                                        data-search-player-avatar>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
                                    @if ($name === 'players')
                                        <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $item->team?->name ?? 'No team assigned' }}
                                        </p>
                                    @elseif ($name === 'teams')
                                        <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $item->openSection()?->name ?? 'Open section unavailable' }}
                                        </p>
                                    @elseif ($name === 'venues')
                                        <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ $item->address }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </li>
    @endforeach
</ul>
