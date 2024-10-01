<div class="flex-1 xl:overflow-y-auto h-screen">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Edit Match</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">
                @if ( $match->round->knockout->type == 'singles' )
                    <div class="sm:col-span-3 relative">
                        <label for="participant1_id" class="block text-sm font-medium leading-6 text-slate-900">Player 1</label>
                        <x-select wire:model="participant1_id" id="participant1_id" name="participant1_id">
                            <option value="">Select a player</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-select>
                    </div>                    
                    <div class="sm:col-span-3 relative">
                        <label for="participant2_id" class="block text-sm font-medium leading-6 text-slate-900">Player 2</label>
                        <x-select wire:model="participant2_id" id="participant2_id" name="participant2_id">
                            <option value="">Select a player</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-select>
                    </div>         
                @endif

                <div class="sm:col-span-6 relative">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Venue</label>
                    <x-select wire:model="match.venue_id" id="venue_id" name="venue_id">
                        <option value="">Select a venue</option>
                        @foreach ($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                @if ( $match->round->matches->count() > 0 )
                    <div class="sm:col-span-6">
                        <label for="depends_on1_id" class="block text-sm font-medium leading-6 text-slate-900">Does this match depend on the result of other matches?</label>
                        <x-select wire:model="depends_on1_id" id="depends_on1_id" name="depends_on1_id">
                            <option value="">Select a match</option>
                            @foreach ($match->round->matches as $ko_match)
                                <option value="{{ $ko_match->id }}">
                                    {{ $ko_match->title }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-select wire:model="depends_on2_id" id="depends_on2_id" name="depends_on2_id">
                            <option value="">Select a match</option>
                            @foreach ($match->round->matches as $ko_match)
                                <option value="{{ $ko_match->id }}">
                                    {{ $ko_match->title }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                @endif
            </div>

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.matches.show', $match) }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>
