<?php

namespace App\Http\Controllers;

use App\Models\SeasonEntry;
use App\Models\Setting;
use App\Services\StripeCheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class SeasonEntryPaymentController extends Controller
{
    public function checkout(SeasonEntry $entry, StripeCheckoutService $stripeCheckoutService): RedirectResponse
    {
        $entry->loadMissing('season');

        if ($entry->isPaid()) {
            return $this->redirectToConfirmation($entry)
                ->with('payment_notice', 'This registration is already marked as paid.');
        }

        if (! $entry->requiresPayment()) {
            return $this->redirectToConfirmation($entry)
                ->with('payment_notice', 'This registration does not currently require an online payment.');
        }

        if (! Setting::stripePaymentsAvailable()) {
            return $this->redirectToConfirmation($entry)
                ->with('payment_error', 'Stripe payments are currently unavailable. Please use the invoice reference for manual payment.');
        }

        try {
            $session = $stripeCheckoutService->createCheckoutSession($entry);
        } catch (Throwable $throwable) {
            report($throwable);

            return $this->redirectToConfirmation($entry)
                ->with('payment_error', 'Unable to start Stripe checkout right now. Please try again in a moment.');
        }

        $entry->forceFill([
            'payment_provider' => 'stripe',
            'payment_status' => SeasonEntry::PAYMENT_STATUS_CHECKOUT_CREATED,
            'stripe_checkout_session_id' => $session['id'],
            'stripe_payment_intent_id' => $session['payment_intent'] ?? $entry->stripe_payment_intent_id,
            'payment_currency' => strtoupper((string) ($session['currency'] ?? $entry->payment_currency ?? config('services.stripe.currency', 'gbp'))),
            'payment_amount' => isset($session['amount_total']) ? SeasonEntry::amountFromMinorUnits((int) $session['amount_total']) : $entry->payment_amount ?? $entry->total_amount,
            'payment_metadata' => $session['metadata'] ?? $entry->payment_metadata,
        ])->save();

        return redirect()->away($session['url']);
    }

    public function success(SeasonEntry $entry): View
    {
        return view('season-entry.payment-success', [
            'entry' => $entry->loadMissing('season'),
        ]);
    }

    public function cancel(SeasonEntry $entry): View
    {
        return view('season-entry.payment-cancel', [
            'entry' => $entry->loadMissing('season'),
        ]);
    }

    private function redirectToConfirmation(SeasonEntry $entry): RedirectResponse
    {
        return redirect()->route('season.entry.confirmation', [
            'season' => $entry->season,
            'entry' => $entry->reference,
        ]);
    }
}
