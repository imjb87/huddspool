<div class="flex-1 xl:overflow-y-auto">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Add Season</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Name</label>
                    <input type="text" name="name" id="name" autocomplete="name" wire:model="season.name"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <div class="sm:col-span-6">
                    <label for="is_open" class="block text-sm font-medium leading-6 text-slate-900">Status</label>
                    <select id="is_open" name="is_open" wire:model="season.is_open"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6">
                        <option value="1">Open</option>
                        <option value="0">Closed</option>
                    </select>
                </div>

                <div class="flex items-center justify-between sm:col-span-6">
                    <span class="flex flex-grow flex-col">
                        <span class="text-sm font-medium leading-6 text-gray-900" id="status-label">Status</span>
                        <span class="text-sm text-gray-500" id="availability-description">This season is currently
                            {{ $season->is_active ? 'active' : 'inactive' }}.</span>
                    </span>
                    <button type="button"
                        class="bg-gray-200 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                        role="switch"
                        wire:model="season.is_active"
                        wire:class="{ 'bg-indigo-600': {{ $season->is_active }}, 'bg-gray-200': !{{ $season->is_active }} }"
                        >
                        <span aria-hidden="true"
                            wire:class="{ 'translate-x-5': {{ $season->is_active }}, 'translate-x-0': !{{ $season->is_active }} }"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>


                @for ($i = 1; $i <= 18; $i++)
                    <div class="sm:col-span-3">
                        <label for="dates{{ $i }}"
                            class="block text-sm font-medium leading-6 text-slate-900">Week {{ $i }}</label>
                        <input type="date" name="dates[]" id="dates{{ $i }}"
                            wire:model="season.dates.{{ $i - 1 }}"
                            class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                    </div>
                @endfor

            </div>

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.seasons.index') }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>
