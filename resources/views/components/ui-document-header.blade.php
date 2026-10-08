@props([
    'title',
    'description' => null,
    'breadcrumbs' => [],
])

<header {{ $attributes->class('ui-document-header') }} data-ui-document-header data-section-shared-header>
    <div class="ui-document-header-inner">
        @if (filled($breadcrumbs))
            <x-ui-breadcrumb class="mb-5" :items="$breadcrumbs" />
        @endif

        <div class="ui-document-heading">
            <div class="min-w-0">
                <h1 class="ui-document-title text-gray-900 dark:text-gray-100">{{ $title }}</h1>

                @if (filled($description))
                    <p class="ui-document-description">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>
</header>
