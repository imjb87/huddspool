@if ($player->email && auth()->check())
    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email address</p>
        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
            <a href="mailto:{{ $player->email }}"
                class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                {{ $player->email }}
            </a>
        </p>
    </div>
@endif

@if ($player->telephone && auth()->check())
    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone number</p>
        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
            <a href="tel:{{ $player->telephone }}"
                class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                {{ $player->telephone }}
            </a>
        </p>
    </div>
@endif
