<x-filament-panels::page>
    <form wire:submit="save" class="max-w-2xl space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="space-y-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Stripe payments</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Toggle whether unpaid season registrations can be paid online with Stripe Checkout.
                    </p>
                </div>

                <label class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        wire:model.live="stripe_enabled"
                        class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                    />
                    <span class="text-sm text-gray-700 dark:text-gray-200">
                        Enable Stripe payments for season sign-up
                    </span>
                </label>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Stripe keys remain in <code>.env</code>. This setting only controls whether public checkout links and webhook-driven payment updates are active.
                </p>
            </div>
        </section>

        @if (! $this->stripeConfigured)
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-200">
                Stripe is not fully configured. Add <code>STRIPE_SECRET_KEY</code> and <code>STRIPE_WEBHOOK_SECRET</code> in the environment before enabling live checkout.
            </div>
        @endif

        <div>
            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-500"
            >
                Save settings
            </button>
        </div>
    </form>
</x-filament-panels::page>
