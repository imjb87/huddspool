<x-guest-layout>
    <section class="py-1" data-login-page>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h1 class="text-lg font-semibold text-gray-900">Log in</h1>
                <p class="max-w-sm text-sm leading-6 text-gray-500">
                    Access your account to manage your profile, follow knockouts, and submit team results when they are due.
                </p>
            </div>

            <div class="lg:col-span-2">
                <x-auth-session-status class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <x-errors />
                        </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900">{{ __('Email address') }}</label>
                        <x-text-input id="email"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900">{{ __('Password') }}</label>
                        <x-text-input id="password"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input id="remember_me"
                                type="checkbox"
                                class="rounded-sm border-gray-300 text-green-700 shadow-xs focus:ring-green-700"
                                name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-gray-600 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                            class="inline-flex min-w-24 items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                            {{ __('Log in') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-guest-layout>
