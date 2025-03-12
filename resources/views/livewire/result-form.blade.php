<form class="divide-y-slate-200 space-y-8 divide-y" wire:submit="save">
            
            <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                <div class="bg-green-700 hidden sm:flex">
                    <div class="flex-1 leading-6 py-2 px-4 text-left font-semibold text-white text-sm">
                        {{ $fixture->homeTeam->name }}
                    </div>
                    <div class="w-12 text-center leading-6 py-2 font-semibold text-white text-sm">
                        vs
                    </div>
                    <div class="flex-1 leading-6 py-2 px-4 text-right font-semibold text-white text-sm">
                        {{ $fixture->awayTeam->name }}
                    </div>
                </div>
                @for ($i = 1; $i <= 10; $i++)
                    <div class="flex flex-wrap">
                        <div
                            class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-gray-200 sm:border-0">
                            <select wire:model.live="frames.{{ $i }}.home_player_id"
                                class="border-0 py-2 px-4 leading-6 text-sm flex-1 focus:outline-0 focus:ring-0">
                                <option value="">Select player...</option>
                                <option value="0">Awarded</option>
                                @foreach ($fixture->homeTeam->players as $player)
                                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                                @endforeach
                            </select>
                            <div class="w-10 sm:w-12 border-x border-gray-200">
                                <select wire:model.live="frames.{{ $i }}.home_score" name="frames.{{ $i }}.home_score"
                                    class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                    placeholder="0">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div
                            class="w-full sm:w-12 sm:text-center bg-green-700 sm:bg-gray-50 py-2 px-4 text-left text-sm font-semibold text-white sm:text-gray-900 order-first sm:order-2 leading-6">
                            <span class="sm:hidden">Frame </span>
                            {{ $i }}
                        </div>
                        <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                            <div class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
                                <select wire:model.live="frames.{{ $i }}.away_score" name="frames.{{ $i }}.away_score"
                                    class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                    placeholder="0">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                            <select wire:model.live="frames.{{ $i }}.away_player_id"
                                class="border-0 py-2 px-4 leading-6 text-sm flex-1 order-first sm:order-last focus:outline-0 focus:ring-0">
                                <option value="">Select player...</option>
                                <option value="0">Awarded</option>
                                @foreach ($fixture->awayTeam->players as $player)
                                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endfor
                <div class="flex flex-wrap bg-gray-50 font-semibold text-gray-900 text-sm">
                    <div class="w-full sm:w-auto flex sm:flex-1 border-b border-gray-200">
                        <div class="flex-1 leading-6 py-2 px-4 sm:text-right">
                            Home total
                        </div>
                        <div class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200">
                            {{ $this->homeScore }}
                        </div>
                    </div>
                    <div class="w-10 sm:w-12 bg-gray-50"></div>
                    <div class="w-full sm:w-auto flex sm:flex-1">
                        <div class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200 order-last sm:order-first">
                            {{ $this->awayScore }}
                        </div>
                        <div class="flex-1 leading-6 py-2 px-4 order-first sm:order-last">
                            Away total
                        </div>        
                    </div>
                </div>            
            </div>

            @if ($errors->any())
                <x-errors />
            @endif

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('fixture.show', $fixture->id) }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-green-700 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-green-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700" wire:loading.attr="disabled" wire:target="save">Submit</button>
                    
            </div>
        </form>