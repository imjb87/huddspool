<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                  <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Fixtures &amp; Results</h3>
                  <p class="ml-2 mt-1 truncate text-sm text-gray-500">{{ $ruleset->name }}</p>
                </div>
            </div>                    
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-6">
                @foreach ($sections as $section)
                    <livewire:fixture.section-show :section="$section" />
                @endforeach
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>