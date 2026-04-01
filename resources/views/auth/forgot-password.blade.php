<x-guest-layout>
    <section class="ui-section" data-forgot-password-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">Forgot password</h1>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ __('Enter your email address and we will send you a reset link so you can choose a new password.') }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <x-auth-session-status class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/80 dark:bg-green-950/60 dark:text-green-200" :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/80 dark:bg-red-950/60 dark:text-red-200">
                                    <x-errors />
                                </div>
                            @endif

                            <div>
                                <label for="email" class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Email address') }}</label>
                                <x-text-input id="email"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <a href="{{ route('login') }}" class="ui-link text-sm font-medium">
                                    {{ __('Back to login') }}
                                </a>

                                <button type="submit" class="ui-button-primary min-w-24">
                                    {{ __('Email Reset Link') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
