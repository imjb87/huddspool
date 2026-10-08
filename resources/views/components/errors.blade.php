<div
    class="rounded-lg border border-red-500/50 bg-red-50/50 px-4 py-3.5 text-sm dark:border-red-500/50 dark:bg-red-950/20"
    role="alert"
>
    <div class="flex items-start gap-3">
        <div class="shrink-0 pt-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-alert-circle size-5 text-red-600 dark:text-red-300" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                <path d="M12 8v4" />
                <path d="M12 16h.01" />
            </svg>
        </div>

        <div class="min-w-0 space-y-2">
            <h3 class="text-sm font-medium text-red-900 dark:text-red-100">
                {{ count($errors->all()) === 1 ? 'There is 1 problem with your submission' : 'There are '.count($errors->all()).' problems with your submission' }}
            </h3>

            <ul role="list" class="space-y-1.5 text-sm leading-5 text-red-800 dark:text-red-200">
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
