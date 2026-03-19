<section data-section-table-view class="mt-0">
    @php
        $isHistoryView = $history ?? false;
    @endphp
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-section-table-shell>
        <div class="min-w-full overflow-hidden">
            <div class="bg-linear-to-b from-gray-50 to-gray-100">
                <div class="mx-auto flex w-full max-w-4xl" data-section-table-band>
                    <div class="flex w-[44%] pl-4 sm:w-1/2 sm:pl-6">
                        <div scope="col" class="w-2/12 py-2 text-sm font-semibold text-gray-900">#</div>
                        <div scope="col" class="w-10/12 py-2 text-sm font-semibold text-gray-900">Team</div>
                    </div>
                    <div class="flex w-[56%] pr-4 sm:w-1/2 sm:pr-0">
                        <div scope="col" class="w-1/5 py-2 text-right text-sm font-semibold text-gray-900">Pl</div>
                        <div scope="col" class="w-1/5 py-2 text-right text-sm font-semibold text-gray-900">W</div>
                        <div scope="col" class="w-1/5 py-2 text-right text-sm font-semibold text-gray-900">D</div>
                        <div scope="col" class="w-1/5 py-2 text-right text-sm font-semibold text-gray-900">L</div>
                        <div scope="col" class="w-1/5 py-2 text-right text-sm font-semibold text-gray-900">Pts</div>
                    </div>
                </div>
            </div>
            <div class="bg-white">
                @if ($standings->isEmpty())
                    <div class="px-4 py-10 text-center sm:px-6">
                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8">
                            <h3 class="text-sm font-semibold text-gray-900">No standings available for this section yet.</h3>
                            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                                Standings will appear once results are entered for this section.
                            </p>
                        </div>
                    </div>
                @else
                    @foreach ($standings as $index => $team)
                        @php
                            $withdrawn = (bool) ($team->pivot->withdrawn_at ?? false);
                            $textClass = $withdrawn ? 'text-gray-400' : 'text-gray-900';
                            $displayName = $isHistoryView ? ($team->archived_name ?? $team->name) : $team->name;
                        @endphp
                        @php
                            $canLinkTeam = ! ($isHistoryView && ($team->trashed ?? false));
                        @endphp
                        @if ($canLinkTeam)
                            <a class="block w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 {{ $withdrawn ? 'line-through' : '' }}"
                                wire:key="section-standing-{{ $section->id }}-{{ $team->id }}"
                                data-section-table-row-type="link"
                                href="{{ route('team.show', $team->id) }}">
                        @else
                            <div class="block w-full border-t border-gray-300 {{ $withdrawn ? 'line-through' : '' }}"
                                wire:key="section-standing-{{ $section->id }}-{{ $team->id }}"
                                data-section-table-row-type="static">
                        @endif
                            <div class="mx-auto flex w-full max-w-4xl" data-section-table-band>
                                <div class="flex w-[44%] items-center pl-4 sm:w-1/2 sm:pl-6">
                                    <div class="w-2/12 whitespace-nowrap py-2 text-sm font-semibold {{ $textClass }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex w-10/12 flex-col whitespace-nowrap py-2 text-sm {{ $textClass }}">
                                        <span class="{{ $team->shortname ? 'hidden md:inline' : '' }}">
                                            {{ $displayName }}
                                        </span>
                                        @if ($team->shortname)
                                            <span class="md:hidden {{ $textClass }}">
                                                {{ $isHistoryView ? $displayName : $team->shortname }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex w-[56%] items-center pr-4 sm:w-1/2 sm:pr-0">
                                    <div class="w-1/5 py-2 text-right text-sm {{ $textClass }}">
                                        {{ $team->played }}
                                    </div>
                                    <div class="w-1/5 py-2 text-right text-sm {{ $textClass }}">
                                        {{ $team->wins }}
                                    </div>
                                    <div class="w-1/5 py-2 text-right text-sm {{ $textClass }}">
                                        {{ $team->draws }}
                                    </div>
                                    <div class="w-1/5 py-2 text-right text-sm {{ $textClass }}">
                                        {{ $team->losses }}
                                    </div>
                                    <div class="w-1/5 py-2 text-right text-sm font-semibold {{ $withdrawn ? 'text-gray-400' : 'text-green-700' }}">
                                        {{ $team->points }}
                                    </div>
                                </div>
                            </div>
                        @if ($canLinkTeam)
                            </a>
                        @else
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
