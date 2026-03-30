@if ($player->email && auth()->check())
    <div class="ui-card-row block px-4 sm:px-5">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Email address</p>
        <p class="text-sm text-gray-900 dark:text-gray-100">
            <a href="mailto:{{ $player->email }}"
                class="ui-link">
                {{ $player->email }}
            </a>
        </p>
    </div>
@endif

@if ($player->telephone && auth()->check())
    <div class="ui-card-row block px-4 sm:px-5">
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Phone number</p>
        <p class="text-sm text-gray-900 dark:text-gray-100">
            <a href="tel:{{ $player->telephone }}"
                class="ui-link">
                {{ $player->telephone }}
            </a>
        </p>
    </div>
@endif
