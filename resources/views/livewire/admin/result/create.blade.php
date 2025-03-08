<div class="flex-1 xl:overflow-y-auto">
    <div class="mx-auto max-w-4xl py-10 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Add Result</h1>
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="save">
            <div class="overflow-hidden bg-white rounded-lg">
                <div class="px-4 py-5 sm:p-0">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Home team</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $fixture->homeTeam->name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Away team</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $fixture->awayTeam->name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Venue</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $fixture->venue->name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Section</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $fixture->section->name }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Ruleset</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ $fixture->section->ruleset->name }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Fixture date</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ date('d/m/Y', strtotime($fixture->fixture_date)) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                <div class="bg-gray-50 hidden sm:flex">
                    <div class="flex-1 leading-6 py-2 px-3 text-left text-sm font-semibold text-gray-900">
                        {{ $fixture->homeTeam->name }}
                    </div>
                    <div class="w-12 text-center text-sm leading-6 py-2 font-semibold text-gray-900">
                        vs
                    </div>
                    <div class="flex-1 leading-6 py-2 px-3 text-right text-sm font-semibold text-gray-900">
                        {{ $fixture->awayTeam->name }}
                    </div>
                </div>
                @for ($i = 1; $i <= 10; $i++)
                    <div class="flex flex-wrap">
                        <div
                            class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-gray-200 sm:border-0">
                            <select wire:model.live="frames.{{ $i }}.home_player_id"
                                class="border-0 py-1.5 px-3 leading-6 text-sm flex-1 focus:outline-0 focus:ring-0">
                                <option value="">Select player</option>
                                <option value="0">Awarded</option>
                                @foreach ($fixture->homeTeam->players as $player)
                                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                                @endforeach
                            </select>
                            <div class="w-10 sm:w-12 border-x border-gray-200">
                                <select wire:model.live="frames.{{ $i }}.home_score"
                                    name="frames.{{ $i }}.home_score"
                                    class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                    placeholder="0">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div
                            class="w-full sm:w-12 sm:text-center bg-gray-50 py-1.5 px-3 text-left text-sm font-semibold text-gray-900 order-first sm:order-2 leading-6">
                            <span class="sm:hidden">Frame </span>
                            {{ $i }}
                        </div>
                        <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                            <div class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
                                <select wire:model.live="frames.{{ $i }}.away_score"
                                    name="frames.{{ $i }}.away_score"
                                    class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                    placeholder="0">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                            <select wire:model.live="frames.{{ $i }}.away_player_id"
                                class="border-0 py-1.5 px-3 leading-6 text-sm flex-1 order-first sm:order-last focus:outline-0 focus:ring-0">
                                <option value="">Select player</option>
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
                        <div class="flex-1 leading-6 py-1.5 px-3 sm:text-right">
                            Home total
                        </div>
                        <div class="w-10 sm:w-12 leading-6 text-center border-x border-gray-200">
                            <input type="text" wire:model="homeScore" name="homeScore"
                                class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-1.5 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="w-10 sm:w-12 bg-gray-50"></div>
                    <div class="w-full sm:w-auto flex sm:flex-1">
                        <div
                            class="w-10 sm:w-12 leading-6 text-center border-x border-gray-200 order-last sm:order-first">
                            <input type="text" wire:model="awayScore" name="awayScore"
                                class="appearance-none bg-none block w-full border-0 pr-0 pl-0 py-1.5 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0"
                                placeholder="0">
                        </div>
                        <div class="flex-1 leading-6 py-1.5 px-3 order-first sm:order-last">
                            Away total
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <x-errors />
            @endif

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.fixtures.show', $fixture) }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>            
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>

            </div>
        </form>
    </div>
</div>
