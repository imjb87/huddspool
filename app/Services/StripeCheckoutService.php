<?php

namespace App\Services;

use App\Models\SeasonEntry;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeCheckoutService
{
    /**
     * @return array<string, mixed>
     */
    public function createCheckoutSession(SeasonEntry $entry): array
    {
        $secretKey = (string) config('services.stripe.secret_key');

        if (blank($secretKey)) {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        $entry->loadMissing('season');

        $amount = $entry->totalAmountInMinorUnits();

        if ($amount < 1) {
            throw new RuntimeException('Stripe checkout requires a positive amount.');
        }

        $currency = strtolower((string) ($entry->payment_currency ?: config('services.stripe.currency', 'gbp')));

        $response = Http::asForm()
            ->withToken($secretKey)
            ->withHeaders([
                'Idempotency-Key' => $this->idempotencyKey($entry),
            ])
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => route('season.entry.payment.success', ['entry' => $entry->reference]).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('season.entry.payment.cancel', ['entry' => $entry->reference]),
                'client_reference_id' => $entry->reference,
                'metadata[season_entry_id]' => (string) $entry->id,
                'metadata[reference]' => (string) $entry->reference,
                'payment_intent_data[metadata][season_entry_id]' => (string) $entry->id,
                'payment_intent_data[metadata][reference]' => (string) $entry->reference,
                'line_items[0][quantity]' => 1,
                'line_items[0][price_data][currency]' => $currency,
                'line_items[0][price_data][unit_amount]' => $amount,
                'line_items[0][price_data][product_data][name]' => $entry->season->name.' season registration',
                'line_items[0][price_data][product_data][description]' => 'Reference '.$entry->reference,
            ])
            ->throw()
            ->json();

        if (! is_array($response) || blank($response['id'] ?? null) || blank($response['url'] ?? null)) {
            throw new RuntimeException('Stripe checkout session response was invalid.');
        }

        return $response;
    }

    public function verifyWebhookSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = (string) config('services.stripe.webhook_secret');

        if (blank($secret) || blank($signatureHeader)) {
            return false;
        }

        $parts = collect(explode(',', $signatureHeader))
            ->map(function (string $part): array {
                [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

                return [
                    'key' => trim((string) $key),
                    'value' => trim((string) $value),
                ];
            });

        $timestamp = (int) (($parts->firstWhere('key', 't')['value'] ?? 0));
        $signatures = $parts
            ->where('key', 'v1')
            ->pluck('value')
            ->filter()
            ->values();

        if ($timestamp < 1 || $signatures->isEmpty()) {
            return false;
        }

        $tolerance = (int) config('services.stripe.webhook_tolerance', 300);

        if (abs(now()->timestamp - $timestamp) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return $signatures->contains(fn (string $signature): bool => hash_equals($expected, $signature));
    }

    private function idempotencyKey(SeasonEntry $entry): string
    {
        return hash('sha256', implode(':', [
            'season-entry',
            $entry->id,
            $entry->payment_status ?: SeasonEntry::PAYMENT_STATUS_PENDING,
            $entry->updated_at?->timestamp ?? 0,
        ]));
    }
}
