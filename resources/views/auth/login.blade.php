<x-guest-layout>
    <section class="py-1" data-login-page>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Log in</h1>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Access your account to manage your profile, follow knockouts, and submit team results when they are due.
                </p>
            </div>

            <div class="lg:col-span-2">
                <x-auth-session-status class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/80 dark:bg-green-950/60 dark:text-green-200" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/80 dark:bg-red-950/60 dark:text-red-200">
                            <x-errors />
                        </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Email address') }}</label>
                        <x-text-input id="email"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Password') }}</label>
                        <x-text-input id="password"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <input id="remember_me"
                                type="checkbox"
                                class="rounded-sm border-gray-300 text-green-700 shadow-xs focus:ring-green-700"
                                name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-gray-600 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-400 dark:decoration-gray-600 dark:hover:text-gray-100 dark:hover:decoration-gray-500"
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

                    <div class="flex items-center gap-3 pt-2">
                        <div class="h-px flex-1 bg-gray-200 dark:bg-zinc-700"></div>
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Or</span>
                        <div class="h-px flex-1 bg-gray-200 dark:bg-zinc-700"></div>
                    </div>

                    <div>
                        <a href="{{ route('auth.google') }}"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm transition hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:hover:bg-zinc-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.7 3.9-5.5 3.9-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.2.8 4 1.5l2.7-2.6C17 3.3 14.8 2.4 12 2.4 6.9 2.4 2.8 6.5 2.8 11.6s4.1 9.2 9.2 9.2c5.3 0 8.8-3.7 8.8-8.9 0-.6-.1-1.1-.1-1.7H12z"/>
                                <path fill="#34A853" d="M2.8 11.6c0 1.7.6 3.2 1.6 4.4l3.7-2.9c-.3-.8-.5-1.5-.5-2.4s.2-1.7.5-2.4L4.4 5.3C3.4 6.5 2.8 9.9 2.8 11.6z"/>
                                <path fill="#FBBC05" d="M12 20.8c2.8 0 5.1-.9 6.8-2.5l-3.3-2.6c-.9.6-2 1-3.5 1-2.6 0-4.8-1.7-5.6-4.1l-3.8 2.9c1.6 3.1 4.9 5.3 9.4 5.3z"/>
                                <path fill="#4285F4" d="M18.8 18.3c1.9-1.8 3.2-4.4 3.2-7.7 0-.6-.1-1.1-.2-1.7H12v3.9h5.5c-.3 1.3-1 2.5-2.2 3.3l3.5 2.2z"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-guest-layout>
