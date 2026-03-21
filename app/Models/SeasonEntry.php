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
        'total_amount',
        'paid_at',
    ];

    protected $casts = [
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

    public function markPaid(): void
    {
        $this->forceFill([
            'paid_at' => now(),
        ])->save();
    }

    private function generateReference(): string
    {
        return sprintf('HS%s-%05d', now()->format('y'), $this->id);
    }
}
