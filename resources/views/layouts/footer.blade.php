<footer class="bg-neutral-800">
    @if (app('impersonate')->isImpersonating())
        <div class="border-b border-white/10">
            <div class="mx-auto max-w-7xl px-6 py-3 text-right lg:px-8">
                <a href="{{ route('impersonation.leave') }}"
                    class="text-xs font-semibold text-gray-300 underline decoration-gray-500 underline-offset-3 transition hover:text-white hover:decoration-gray-300">
                    Stop impersonating
                </a>
            </div>
        </div>
    @endif
    <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
            <p class="text-xs leading-5 text-gray-400">
                &copy; 2023 Huddersfield & District Tuesday Night Pool League.
            </p>
            <p class="mt-2 text-xs leading-5 text-gray-400">
                Website built by John Bell.
            </p>
        </div>
    </div>
</footer>
