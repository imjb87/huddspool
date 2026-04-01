<ul class="max-h-[28rem] overflow-y-auto" id="options"
    x-on:keydown.up.prevent="$focus.wrap().previous()"
    x-on:keydown.down.prevent="$focus.wrap().next()"
    role="listbox"
    data-search-results-shell>
    @foreach ($resultGroups as $name => $group)
        <li class="border-t border-gray-200/80 first:border-t-0 dark:border-neutral-800/75" wire:key="search-group-{{ $name }}">
            <div class="ui-card-column-headings justify-start px-4 sm:px-5">
                <h2 class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    {{ $group['heading'] }}
                </h2>
            </div>
            <div class="ui-card-rows" data-search-result-group>
                @foreach ($group['results'] as $item)
                    @if (is_object($item))
                        <a class="ui-card-row-link focus:outline-none"
                            href="{{ route($group['route'] . '.show', $item->id) }}"
                            data-search-result-link
                            wire:key="search-result-{{ $name }}-{{ $item->id }}"
                            x-on:click="close()">
                            <div class="ui-card-row items-start">
                                @if ($name === 'players')
                                    <img src="{{ $item->avatar_url }}"
                                        alt="{{ $item->name }}"
                                        class="mt-0.5 h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-gray-200 dark:ring-neutral-800"
                                        data-search-player-avatar>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
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
