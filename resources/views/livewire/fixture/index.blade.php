<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-6">
                @foreach ($sections as $section)
                    <livewire:fixture.section-show :section="$section" />
                @endforeach
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>