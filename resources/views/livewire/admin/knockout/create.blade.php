<div class="flex-1 xl:overflow-y-auto">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Add Knockout</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">
                <!-- Name Input -->
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Name</label>
                    <input type="text" name="name" id="name" autocomplete="name"
                        wire:model.defer="knockout.name"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <!-- Knockout Type Selection -->
                <div class="sm:col-span-6">
                    <label for="type" class="block text-sm font-medium leading-6 text-slate-900">Knockout Type</label>
                    <select id="type" name="type" wire:model.defer="knockout.type"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6">
                        <option value="">Select Type</option>
                        <option value="singles">Singles</option>
                        <option value="doubles">Doubles</option>
                        <option value="team">Team</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.seasons.show', $season) }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>
