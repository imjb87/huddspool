<footer class="bg-neutral-800">
    @if (session()->has('previous_user'))
        <div class="fixed bottom-24 right-4 z-50 lg:bottom-4">
            <a href="{{ route('admin.switch-back') }}"
                class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-green-500 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                Switch back to {{ session('previous_user')->name }}
            </a>
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
