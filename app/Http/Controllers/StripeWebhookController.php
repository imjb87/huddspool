<?php

namespace App\Http\Controllers;

use App\Models\SeasonEntry;
use App\Services\StripeCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeCheckoutService $stripeCheckoutService): JsonResponse
    {
        $payload = $request->getContent();

        if (! $stripeCheckoutService->verifyWebhookSignature($payload, $request->header('Stripe-Signature'))) {
            return response()->json([
                'message' => 'Invalid Stripe signature.',
            ], 400);
        }

        /** @var array<string, mixed> $event */
        $event = $request->json()->all();
        /** @var array<string, mixed> $session */
        $session = $event['data']['object'] ?? [];

        $entry = $this->resolveEntry($session);

        if (! $entry) {
            return response()->json([
                'message' => 'No matching season entry found.',
            ]);
        }

        $entry->loadMissing('season');

        match ($event['type'] ?? null) {
            'checkout.session.completed' => $entry->markPaid('stripe', [
                'payment_provider' => 'stripe',
                'payment_status' => SeasonEntry::PAYMENT_STATUS_PAID,
                'stripe_checkout_session_id' => $session['id'] ?? $entry->stripe_checkout_session_id,
                'stripe_payment_intent_id' => $session['payment_intent'] ?? $entry->stripe_payment_intent_id,
                'payment_currency' => strtoupper((string) ($session['currency'] ?? $entry->payment_currency ?? config('services.stripe.currency', 'gbp'))),
                'payment_amount' => isset($session['amount_total']) ? number_format(((int) $session['amount_total']) / 100, 2, '.', '') : $entry->payment_amount ?? $entry->total_amount,
                'payment_metadata' => $session['metadata'] ?? $entry->payment_metadata,
            ]),
            'checkout.session.expired' => $this->markStatus($entry, SeasonEntry::PAYMENT_STATUS_EXPIRED, $session),
            'checkout.session.async_payment_failed' => $this->markStatus($entry, SeasonEntry::PAYMENT_STATUS_FAILED, $session),
            default => null,
        };

        return response()->json([
            'message' => 'Webhook processed.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function resolveEntry(array $session): ?SeasonEntry
    {
        $seasonEntryId = $session['metadata']['season_entry_id'] ?? null;

        if ($seasonEntryId) {
            return SeasonEntry::query()->find($seasonEntryId);
        }

        if (filled($session['id'] ?? null)) {
            return SeasonEntry::query()
                ->where('stripe_checkout_session_id', $session['id'])
                ->first();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function markStatus(SeasonEntry $entry, string $status, array $session): void
    {
        if ($entry->isPaid()) {
            return;
        }

        $entry->forceFill([
            'payment_provider' => 'stripe',
            'payment_status' => $status,
            'stripe_checkout_session_id' => $session['id'] ?? $entry->stripe_checkout_session_id,
            'stripe_payment_intent_id' => $session['payment_intent'] ?? $entry->stripe_payment_intent_id,
            'payment_currency' => strtoupper((string) ($session['currency'] ?? $entry->payment_currency ?? config('services.stripe.currency', 'gbp'))),
            'payment_amount' => isset($session['amount_total']) ? number_format(((int) $session['amount_total']) / 100, 2, '.', '') : $entry->payment_amount ?? $entry->total_amount,
            'payment_metadata' => $session['metadata'] ?? $entry->payment_metadata,
        ])->save();
    }
}
