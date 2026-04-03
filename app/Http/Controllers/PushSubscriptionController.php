<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPushSubscriptionRequest;
use App\Http\Requests\StorePushSubscriptionRequest;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(StorePushSubscriptionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $endpoint = $request->string('endpoint')->toString();

        $subscription = PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => hash('sha256', $endpoint)],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => $request->string('public_key')->toString(),
                'auth_token' => $request->string('auth_token')->toString(),
                'content_encoding' => $request->string('content_encoding')->toString() ?: 'aes128gcm',
            ]
        );

        if ($user->push_prompted_at === null) {
            $user->forceFill([
                'push_prompted_at' => now(),
            ])->saveQuietly();
        }

        return response()->json([
            'enabled' => true,
            'subscription_id' => $subscription->id,
        ]);
    }

    public function acknowledge(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->push_prompted_at === null) {
            $user->forceFill([
                'push_prompted_at' => now(),
            ])->saveQuietly();
        }

        return response()->json([
            'acknowledged' => true,
        ]);
    }

    public function destroy(DestroyPushSubscriptionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $endpoint = $request->string('endpoint')->toString();

        $user->pushSubscriptions()
            ->where('endpoint_hash', hash('sha256', $endpoint))
            ->delete();

        return response()->json([
            'enabled' => false,
        ]);
    }
}
