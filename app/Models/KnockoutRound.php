<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnockoutRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'knockout_id',
        'name',
        'position',
        'scheduled_for',
        'best_of',
        'is_visible',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'best_of' => 'integer',
        'is_visible' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (KnockoutRound $round) {
            if (! $round->wasChanged('scheduled_for') || ! $round->scheduled_for) {
                return;
            }

            $date = $round->scheduled_for->copy();

            $round->matches()->get()->each(function (KnockoutMatch $match) use ($date) {
                $startsAt = $match->starts_at;

                if ($startsAt) {
                    $updated = $date->copy()->setTime(
                        (int) $startsAt->format('H'),
                        (int) $startsAt->format('i'),
                        (int) $startsAt->format('s')
                    );
                } else {
                    $updated = $date->copy();
                }

                $match->forceFill(['starts_at' => $updated])->saveQuietly();
            });
        });
    }

    public function knockout(): BelongsTo
    {
        return $this->belongsTo(Knockout::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(KnockoutMatch::class)->orderBy('position');
    }

    public function bestOfValue(): ?int
    {
        return $this->best_of ?? $this->knockout?->bestOfValue();
    }
}
