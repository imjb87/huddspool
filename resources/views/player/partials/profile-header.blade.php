<div class="flex items-start gap-8">
    <div class="shrink-0">
        <img class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80"
            src="{{ $player->avatar_url }}"
            alt="{{ $player->name }} avatar">
    </div>

    <div class="min-w-0 flex-1">
        <div class="grid grid-cols-2 gap-x-6 gap-y-5">
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $player->roleLabel() }}</p>
            </div>

            <div class="col-span-2 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Team</p>
                @if ($player->team)
                    <a href="{{ route('team.show', $player->team) }}"
                        class="mt-2 inline-flex max-w-full text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                        <span>{{ $player->team->name }}</span>
                    </a>
                @else
                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Free agent</p>
                @endif
            </div>
        </div>
    </div>
</div>
