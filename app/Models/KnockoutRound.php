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
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'best_of' => 'integer',
    ];

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
