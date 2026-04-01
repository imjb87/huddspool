@props([
    'mobile' => false,
    'grouped' => false,
    'fullWidth' => false,
])

@php
    $buttonClasses = 'flex justify-center rounded-md p-2 outline-hidden transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5';
    $activeClasses = 'text-green-600 bg-gray-50 dark:bg-white/5 dark:text-green-400';
    $inactiveClasses = 'text-gray-400 hover:text-gray-500 focus-visible:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:text-gray-400';
    $themes = [
        [
            'name' => 'light',
            'label' => 'Enable light theme',
            'icon' => '<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z"></path></svg>',
        ],
        [
            'name' => 'dark',
            'label' => 'Enable dark theme',
            'icon' => '<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"></path></svg>',
        ],
        [
            'name' => 'system',
            'label' => 'Enable system theme',
            'icon' => '<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v6.5A1.75 1.75 0 0 1 15.25 13h-3.19l1.72 2.398a.75.75 0 1 1-1.218.874L11.134 14h-2.268l-1.428 2.272a.75.75 0 1 1-1.219-.874L7.94 13H4.75A1.75 1.75 0 0 1 3 11.25v-6.5ZM4.5 5.25v5.5c0 .138.112.25.25.25h10.5a.25.25 0 0 0 .25-.25v-5.5a.25.25 0 0 0-.25-.25H4.75a.25.25 0 0 0-.25.25Z" clip-rule="evenodd"></path></svg>',
        ],
    ];
@endphp

<div
    class="{{ $mobile ? ($grouped ? '' : 'ui-card overflow-hidden') : ($fullWidth ? 'hidden w-full lg:block' : 'hidden lg:block') }}"
    @if (! $mobile)
        data-theme-toggle
    @endif
>
    <div class="{{ $mobile ? ($grouped ? 'px-4 py-4 sm:px-5' : 'ui-card-row px-4 sm:px-5') : '' }}">
        <div
            class="{{ $mobile || $fullWidth ? 'grid w-full grid-cols-3 gap-x-2' : 'grid grid-flow-col gap-x-1' }}"
            @if ($mobile) data-mobile-theme-toggle @endif
        >
            @foreach ($themes as $themeOption)
                <button
                    type="button"
                    aria-label="{{ $themeOption['label'] }}"
                    title="{{ $themeOption['label'] }}"
                    @click="setTheme('{{ $themeOption['name'] }}')"
                    :class="themePreference === '{{ $themeOption['name'] }}' ? '{{ $activeClasses }}' : '{{ $inactiveClasses }}'"
                    class="{{ $buttonClasses }} {{ $mobile || $fullWidth ? 'w-full' : '' }}"
                >
                    {!! $themeOption['icon'] !!}
                </button>
            @endforeach
        </div>
    </div>
</div>
