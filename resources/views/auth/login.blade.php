<x-guest-layout>
    <section class="ui-section" data-login-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">Log in</h1>
                <p class="mt-3 max-w-sm text-sm leading-6 text-muted-foreground">
                    Access your account to manage your profile, follow knockouts, and submit team results when they are due.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <x-auth-session-status :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <x-errors />
                            @endif

                            <div class="grid gap-5">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-foreground">{{ __('Email address') }}</label>
                                    <x-text-input id="email"
                                        class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                                        type="email"
                                        name="email"
                                        :value="old('email')"
                                        required
                                        autofocus
                                        autocomplete="username" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-foreground">{{ __('Password') }}</label>
                                    <x-text-input id="password"
                                        class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-muted-foreground">
                                    <input id="remember_me"
                                        type="checkbox"
                                        class="size-4 rounded border-border text-foreground shadow-xs focus-visible:ring-2 focus-visible:ring-ring/50"
                                        name="remember">
                                    <span>{{ __('Remember me') }}</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="ui-link text-sm font-medium"
                                        href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ui-result-button ui-result-button-primary min-w-24">
                                    {{ __('Log in') }}
                                </button>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <div class="h-px flex-1 bg-border"></div>
                                <span class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Or</span>
                                <div class="h-px flex-1 bg-border"></div>
                            </div>

                            <div>
                                <a href="{{ route('auth.google') }}"
                                    class="ui-result-button ui-result-button-secondary w-full">
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
            </div>
        </div>
    </section>
</x-guest-layout>
