<?php

namespace App\Models;

use App\KnockoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnockoutParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'knockout_id',
        'label',
        'seed',
        'team_id',
        'player_one_id',
        'player_two_id',
    ];

    protected $appends = [
        'display_name',
    ];

    public function knockout(): BelongsTo
    {
        return $this->belongsTo(Knockout::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function playerOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_one_id');
    }

    public function playerTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_two_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw('COALESCE(seed, 9999)')
            ->orderBy('label');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $type = $this->knockout?->type ?? KnockoutType::Singles;

        return match ($type) {
            KnockoutType::Singles => $this->playerOne?->name ?? 'TBC',
            KnockoutType::Doubles => trim(collect([$this->playerOne?->name, $this->playerTwo?->name])->filter()->implode(' & ')) ?: 'TBC',
            KnockoutType::Team => $this->team?->name ?? 'TBC',
        };
    }

    public function includesPlayer(User $user): bool
    {
        return $this->player_one_id === $user->id
            || $this->player_two_id === $user->id;
    }
}
