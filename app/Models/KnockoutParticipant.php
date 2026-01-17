<?php

namespace App\Models;

use App\KnockoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeSearchForKnockout(Builder $query, Knockout $knockout, string $search): Builder
    {
        return $query
            ->where('knockout_id', $knockout->id)
            ->leftJoin('teams', 'knockout_participants.team_id', '=', 'teams.id')
            ->leftJoin('users as player_one', 'knockout_participants.player_one_id', '=', 'player_one.id')
            ->leftJoin('users as player_two', 'knockout_participants.player_two_id', '=', 'player_two.id')
            ->where(function (Builder $query) use ($search) {
                $query->where('knockout_participants.label', 'like', '%' . $search . '%')
                    ->orWhere('teams.name', 'like', '%' . $search . '%')
                    ->orWhere('player_one.name', 'like', '%' . $search . '%')
                    ->orWhere('player_two.name', 'like', '%' . $search . '%');
            })
            ->select('knockout_participants.*');
    }

    public function getDisplayNameAttribute(): string
    {
        $type = $this->knockout?->type ?? KnockoutType::Singles;

        if ($type === KnockoutType::Doubles) {
            $playerOne = $this->playerOne?->name;
            $playerTwo = $this->playerTwo?->name;

            if (($playerOne && ! $playerTwo) || (! $playerOne && $playerTwo)) {
                return $this->formatDoublesName();
            }

            if ($this->label) {
                return $this->label;
            }

            return $this->formatDoublesName();
        }

        if ($this->label) {
            return $this->label;
        }

        return match ($type) {
            KnockoutType::Singles => $this->playerOne?->name ?? 'TBC',
            KnockoutType::Team => $this->team?->name ?? 'TBC',
        };
    }

    public function includesPlayer(User $user): bool
    {
        return $this->player_one_id === $user->id
            || $this->player_two_id === $user->id;
    }

    private function formatDoublesName(): string
    {
        $playerOne = $this->playerOne?->name;
        $playerTwo = $this->playerTwo?->name;

        if (! $playerOne && ! $playerTwo) {
            return 'TBC';
        }

        if ($playerOne && ! $playerTwo) {
            return "{$playerOne} & TBC";
        }

        if (! $playerOne && $playerTwo) {
            return "TBC & {$playerTwo}";
        }

        return "{$playerOne} & {$playerTwo}";
    }
}
