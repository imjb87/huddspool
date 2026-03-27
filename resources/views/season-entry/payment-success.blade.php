@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 dark:bg-zinc-900">
        <div class="mx-auto max-w-3xl px-4 pt-6 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-green-700 dark:text-green-400">Online card payment</p>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $entry->season->name }}</h1>
                </div>

                @if ($entry->isPaid())
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-4 dark:border-green-900/60 dark:bg-green-950/40">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Payment confirmed</p>
                        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                            Online payment has been confirmed for registration {{ $entry->reference }}.
                        </p>
                    </div>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 dark:border-amber-900/60 dark:bg-amber-950/40">
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Payment confirmation pending</p>
                        <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                            Your card payment page has redirected you back successfully, but the registration is only marked as paid once the payment confirmation arrives.
                        </p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('season.entry.confirmation', ['season' => $entry->season, 'entry' => $entry->reference]) }}"
                       class="inline-flex items-center rounded-full bg-green-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-500">
                        View registration
                    </a>
                    <a href="{{ route('season.entry.invoice', ['season' => $entry->season, 'entry' => $entry->reference]) }}"
                       class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-200 dark:hover:bg-zinc-800">
                        Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
