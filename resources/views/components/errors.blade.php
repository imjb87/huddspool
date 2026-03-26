<div
    class="rounded-2xl border border-red-200/80 bg-red-50/80 px-4 py-3.5 text-sm shadow-xs dark:border-red-950/60 dark:bg-red-950/20"
    role="alert"
>
    <div class="flex items-start gap-3">
        <div class="shrink-0 pt-0.5">
            <svg class="h-5 w-5 text-red-500 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                    clip-rule="evenodd" />
            </svg>
        </div>

        <div class="min-w-0 space-y-2">
            <h3 class="font-semibold text-red-900 dark:text-red-100">
                {{ count($errors->all()) === 1 ? 'There is 1 problem with your submission' : 'There are '.count($errors->all()).' problems with your submission' }}
            </h3>

            <ul role="list" class="space-y-1.5 text-red-800 dark:text-red-200">
                @foreach ($errors->all() as $error)
                    <li class="leading-6">{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
