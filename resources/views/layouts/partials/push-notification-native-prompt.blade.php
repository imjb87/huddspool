<div
    x-data="nativePushPermissionPrompt({
        publicKey: @js(config('services.web_push.public_key')),
        subscribeUrl: @js(route('account.push-subscriptions.store')),
        acknowledgeUrl: @js(route('account.push-permission.acknowledge')),
    })"
    x-init="init()"
    data-auto-push-permission-prompt
    class="hidden"
    aria-hidden="true"></div>
