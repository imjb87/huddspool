@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border border-green-500/50 bg-green-50/50 px-4 py-3.5 text-sm text-green-900 dark:border-green-500/50 dark:bg-green-950/20 dark:text-green-200']) }} role="status">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check size-5 shrink-0 text-green-600 dark:text-green-300" aria-hidden="true">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
            <path d="M9 12l2 2l4 -4" />
        </svg>
        <p class="m-0 leading-5">{{ $status }}</p>
    </div>
@endif
