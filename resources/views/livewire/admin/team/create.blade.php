<div class="flex-1">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Add Team</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Name</label>
                    <input type="text" name="name" id="name" autocomplete="name" wire:model="team.name"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <div class="sm:col-span-6">
                    <label for="shortname" class="block text-sm font-medium leading-6 text-slate-900">Short name</label>
                    <input type="text" name="shortname" id="shortname" autocomplete="shortname" wire:model="team.shortname"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <div class="sm:col-span-6">
                    <label for="venue_id" class="block text-sm font-medium leading-6 text-slate-900">Venue</label>
                    <x-select wire:model="team.venue_id" id="venue_id" name="venue_id">
                        <option value="">Select a venue...</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </x-select>
                </div>

            </div>

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.teams.index') }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>
