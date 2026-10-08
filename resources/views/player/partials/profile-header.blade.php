<div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:gap-8">
    <div class="shrink-0">
        <img class="size-24 rounded-full object-cover ring-1 ring-foreground/10"
            src="{{ $player->avatar_url }}"
            alt="{{ $player->name }} avatar">
    </div>

    <div class="min-w-0 flex-1">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
            <div class="col-span-full min-w-0 sm:col-span-1">
                <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Name</p>
                <p class="text-sm font-semibold break-words text-neutral-950 dark:text-neutral-50">{{ $player->name }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Role</p>
                <p class="text-sm text-neutral-950 dark:text-neutral-50">{{ $player->roleLabel() }}</p>
            </div>

            <div class="col-span-2 min-w-0">
                <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Team</p>
                @if ($player->team)
                    <a href="{{ route('team.show', $player->team) }}"
                        class="ui-link inline-flex max-w-full text-sm font-semibold">
                        <span>{{ $player->team->name }}</span>
                    </a>
                @else
                    <p class="text-sm text-neutral-950 dark:text-neutral-50">Free agent</p>
                @endif
            </div>
        </div>
    </div>
</div>
