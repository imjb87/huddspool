<?php

namespace App\Models;

use Database\Factories\SeasonEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeasonEntry extends Model
{
    /** @use HasFactory<SeasonEntryFactory> */
    use HasFactory;

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_CHECKOUT_CREATED = 'checkout_created';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_EXPIRED = 'expired';

    public const PAYMENT_STATUS_FAILED = 'failed';

    protected $fillable = [
        'season_id',
        'reference',
        'contact_name',
        'contact_email',
        'contact_telephone',
        'existing_venue_id',
        'venue_name',
        'venue_address',
        'venue_telephone',
        'notes',
        'payment_provider',
        'payment_status',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'payment_completed_at',
        'payment_currency',
        'payment_amount',
        'payment_metadata',
        'total_amount',
        'paid_at',
    ];

    protected $casts = [
        'payment_completed_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'payment_metadata' => 'array',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (self $entry): void {
            if (filled($entry->reference)) {
                return;
            }

            $entry->forceFill([
                'reference' => $entry->generateReference(),
            ])->saveQuietly();
        });
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function existingVenue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'existing_venue_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(SeasonTeamEntry::class);
    }

    public function knockoutRegistrations(): HasMany
    {
        return $this->hasMany(SeasonKnockoutEntry::class);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function requiresPayment(): bool
    {
        return ! $this->isPaid() && $this->totalAmountInMinorUnits() > 0;
    }

    public function totalAmountInMinorUnits(): int
    {
        return (int) round((float) $this->total_amount * 100);
    }

    /**
     * @return array<string, string>
     */
    public static function paymentStatusOptions(): array
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'Pending',
            self::PAYMENT_STATUS_CHECKOUT_CREATED => 'Checkout created',
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_EXPIRED => 'Expired',
            self::PAYMENT_STATUS_FAILED => 'Failed',
        ];
    }

    public function paymentStatusLabel(): string
    {
        return self::paymentStatusOptions()[$this->payment_status] ?? 'Pending';
    }

    public function markPaid(string $provider = 'manual', array $attributes = []): void
    {
        $this->forceFill([
            'payment_provider' => $attributes['payment_provider'] ?? $provider,
            'payment_status' => $attributes['payment_status'] ?? self::PAYMENT_STATUS_PAID,
            'payment_completed_at' => $attributes['payment_completed_at'] ?? $this->payment_completed_at ?? now(),
            'paid_at' => $attributes['paid_at'] ?? $this->paid_at ?? now(),
            'stripe_checkout_session_id' => $attributes['stripe_checkout_session_id'] ?? $this->stripe_checkout_session_id,
            'stripe_payment_intent_id' => $attributes['stripe_payment_intent_id'] ?? $this->stripe_payment_intent_id,
            'payment_currency' => $attributes['payment_currency'] ?? $this->payment_currency,
            'payment_amount' => $attributes['payment_amount'] ?? $this->payment_amount ?? $this->total_amount,
            'payment_metadata' => $attributes['payment_metadata'] ?? $this->payment_metadata,
        ])->save();
    }

    private function generateReference(): string
    {
        return sprintf('HS%s-%05d', now()->format('y'), $this->id);
    }
}
