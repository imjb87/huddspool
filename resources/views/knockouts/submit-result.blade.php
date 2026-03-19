@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 lg:pb-14" data-knockout-submit-page>
        <section class="bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl" data-knockout-submit-header>
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
                <div class="mx-auto flex w-full max-w-4xl flex-col gap-3">
                    <div class="inline-flex w-fit items-center rounded-full bg-white/12 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-green-50 ring-1 ring-white/15">
                        Result submission
                    </div>
                    <div class="space-y-2">
                        <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Submit knockout result</h1>
                        <p class="max-w-2xl text-sm text-green-50/85 sm:text-base">
                            Record the final score for this match. Existing access rules and result validation still apply.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-7">
            <div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
                <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-knockout-submit-context>
                    <div class="bg-linear-to-b from-gray-50 to-gray-100">
                        <div class="mx-auto flex w-full max-w-4xl flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900 sm:text-base">
                                    {{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $match->round?->name ?? 'Unscheduled round' }}
                                </p>
                            </div>
                            <div class="inline-flex w-fit items-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm ring-1 ring-black/10">
                                Best of {{ $match->bestOfValue() }} frames
                            </div>
                        </div>
                    </div>
                </div>

                <div data-knockout-submit-shell>
                    <livewire:knockout.submit-result :match="$match" />
                </div>
            </div>
        </div>
    </div>
@endsection
