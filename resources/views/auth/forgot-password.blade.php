<x-guest-layout>
    <section class="ui-section" data-forgot-password-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">Forgot password</h1>
                <p class="mt-3 max-w-sm text-sm leading-6 text-muted-foreground">
                    {{ __('Enter your email address and we will send you a reset link so you can choose a new password.') }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <x-auth-session-status :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <x-errors />
                            @endif

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

                            <div class="flex items-center justify-between gap-4">
                                <a href="{{ route('login') }}" class="ui-link text-sm font-medium">
                                    {{ __('Back to login') }}
                                </a>

                                <button type="submit" class="ui-result-button ui-result-button-primary min-w-24">
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
