<x-guest-layout>
    <section class="ui-section" data-invite-register-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">Set password</h1>
                <p class="mt-3 max-w-sm text-sm leading-6 text-muted-foreground">
                    Finish setting up your invited account by choosing a password for {{ $user->email }}.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <form method="POST" action="{{ route('invite.store', $user->invitation_token) }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <x-errors />
                            @endif

                            <div class="grid gap-5">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-foreground">{{ __('Password') }}</label>
                                    <x-text-input id="password"
                                        class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                                        type="password"
                                        name="password"
                                        required
                                        autofocus
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-foreground">{{ __('Confirm Password') }}</label>
                                    <x-text-input id="password_confirmation"
                                        class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ui-result-button ui-result-button-primary min-w-24">
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
