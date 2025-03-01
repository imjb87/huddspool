<div class="flex-1 xl:overflow-y-auto">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Add Section</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Name</label>
                    <input type="text" name="name" id="name" autocomplete="name"
                        wire:model="section.name"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Ruleset</label>
                    <select name="ruleset_id" id="ruleset_id" wire:model.live="section.ruleset_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6">
                        <option value="">Select a ruleset</option>
                        @foreach ($rulesets as $ruleset)
                            <option value="{{ $ruleset->id }}">{{ $ruleset->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-6">
                    <label for="teams[]" class="block text-sm font-medium leading-6 text-slate-900">Teams</label>
                    <fieldset
                        class="overflow-hidden rounded-md mt-2 ring-1 ring-inset ring-slate-300 shadow-sm divide-y divide-gray-200 border">
                        @for ($i = 1; $i <= 10; $i++)
                            <div class="flex items-center">
                                <label
                                    class="whitespace-nowrap text-xs font-semibold text-slate-900 bg-white py-1.5 px-4 w-8 sm:leading-6"
                                    for="teams{{ $i }}">{{ $i }}</label>

                                <select name="teams[]" id="teams{{ $i }}"
                                    wire:model.live="section.teams.{{ $i - 1 }}"
                                    class="block w-full border-0 py-1.5 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-sm sm:leading-6">
                                    <option value="">Select a team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor
                    </fieldset>
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
