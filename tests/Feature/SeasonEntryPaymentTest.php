<?php

namespace Tests\Feature;

use App\Models\SeasonEntry;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeasonEntryPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmation_page_shows_stripe_checkout_button_when_enabled(): void
    {
        config()->set('services.stripe.secret_key', 'sk_test_123');
        config()->set('services.stripe.webhook_secret', 'whsec_123');

        Setting::current()->update([
            'stripe_enabled' => true,
        ]);

        $entry = SeasonEntry::factory()->create([
            'payment_status' => SeasonEntry::PAYMENT_STATUS_PENDING,
            'payment_currency' => 'GBP',
            'payment_amount' => 25,
        ]);

        $this->get(route('season.entry.confirmation', [
            'season' => $entry->season,
            'entry' => $entry->reference,
        ]))
            ->assertOk()
            ->assertSeeText('Pay with Stripe')
            ->assertSee(route('season.entry.payment.checkout', ['entry' => $entry->reference]), false);
    }

    public function test_checkout_route_creates_stripe_session_and_redirects(): void
    {
        config()->set('services.stripe.secret_key', 'sk_test_123');
        config()->set('services.stripe.webhook_secret', 'whsec_123');

        Setting::current()->update([
            'stripe_enabled' => true,
        ]);

        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_123',
                'url' => 'https://checkout.stripe.test/session/cs_test_123',
                'payment_intent' => 'pi_test_123',
                'currency' => 'gbp',
                'amount_total' => 2500,
                'metadata' => [
                    'season_entry_id' => '1',
                ],
            ]),
        ]);

        $entry = SeasonEntry::factory()->create([
            'payment_status' => SeasonEntry::PAYMENT_STATUS_PENDING,
            'payment_currency' => 'GBP',
            'payment_amount' => 25,
            'total_amount' => 25,
        ]);

        $this->get(route('season.entry.payment.checkout', ['entry' => $entry->reference]))
            ->assertRedirect('https://checkout.stripe.test/session/cs_test_123');

        $this->assertDatabaseHas('season_entries', [
            'id' => $entry->id,
            'payment_provider' => 'stripe',
            'payment_status' => SeasonEntry::PAYMENT_STATUS_CHECKOUT_CREATED,
            'stripe_checkout_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
    }

    public function test_completed_webhook_marks_entry_paid_idempotently(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test_123');

        $entry = SeasonEntry::factory()->create([
            'payment_status' => SeasonEntry::PAYMENT_STATUS_CHECKOUT_CREATED,
            'stripe_checkout_session_id' => 'cs_test_123',
            'total_amount' => 25,
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'payment_intent' => 'pi_test_123',
                    'currency' => 'gbp',
                    'amount_total' => 2500,
                    'metadata' => [
                        'season_entry_id' => (string) $entry->id,
                        'reference' => $entry->reference,
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = $this->stripeSignature($payload, 'whsec_test_123');

        $this->call('POST', route('stripe.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $signature,
        ], $payload)->assertOk();

        $firstPaidAt = $entry->fresh()->paid_at;

        $this->call('POST', route('stripe.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $signature,
        ], $payload)->assertOk();

        $entry->refresh();

        $this->assertNotNull($entry->paid_at);
        $this->assertEquals($firstPaidAt, $entry->paid_at);
        $this->assertSame(SeasonEntry::PAYMENT_STATUS_PAID, $entry->payment_status);
        $this->assertSame('stripe', $entry->payment_provider);
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $timestamp = now()->timestamp;

        return 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
    }
}
