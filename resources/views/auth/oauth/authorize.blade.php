<x-guest-layout>
    <section class="ui-section" data-oauth-authorize-page>
        <div class="ui-shell-grid">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">Connect Huddspool</h1>
                <p class="mt-3 max-w-sm text-sm leading-6 text-muted-foreground">
                    {{ $client->name }} is requesting access to your administrator account.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body space-y-5">
                        <div>
                            <p class="text-sm font-medium text-foreground">Signed in as {{ $user->name }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">Only continue if you recognise this connection.</p>
                        </div>

                        @if (count($scopes) > 0)
                            <div>
                                <h2 class="text-sm font-medium text-foreground">This connection can:</h2>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm leading-6 text-muted-foreground">
                                    @foreach ($scopes as $scope)
                                        <li>{{ $scope->description }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex flex-wrap justify-end gap-3">
                            <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                <button type="submit" class="ui-result-button ui-result-button-secondary">Cancel</button>
                            </form>

                            <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                                @csrf
                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                <button type="submit" class="ui-result-button ui-result-button-primary">Allow access</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
