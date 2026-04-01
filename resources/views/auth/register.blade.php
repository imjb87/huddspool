<x-guest-layout>
    <section class="ui-section" data-invite-register-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">Set password</h1>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Finish setting up your invited account by choosing a password for {{ $user->email }}.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <form method="POST" action="{{ route('invite.store', $user->invitation_token) }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/80 dark:bg-red-950/60 dark:text-red-200">
                                    <x-errors />
                                </div>
                            @endif

                            <div class="grid gap-5">
                                <div>
                                    <label for="password" class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Password') }}</label>
                                    <x-text-input id="password"
                                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20"
                                        type="password"
                                        name="password"
                                        required
                                        autofocus
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Confirm Password') }}</label>
                                    <x-text-input id="password_confirmation"
                                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ui-button-primary min-w-24">
                                    {{ __('Set password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
