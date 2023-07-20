<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $ruleset->name }}
                </h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl text-base leading-7 text-gray-700 px-6 lg:px-8">
            <section class="prose mx-auto">{!! $ruleset->content !!}</section>
        </div>        
    </div>
    <x-logo-clouds />
</div>
