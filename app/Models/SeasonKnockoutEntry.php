<?php

namespace App\Models;

use Database\Factories\SeasonKnockoutEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonKnockoutEntry extends Model
{
    /** @use HasFactory<SeasonKnockoutEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'season_entry_id',
        'knockout_id',
        'season_team_entry_id',
        'existing_team_id',
        'entrant_name',
        'player_one_name',
        'player_two_name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(SeasonEntry::class, 'season_entry_id');
    }

    public function knockout(): BelongsTo
    {
        return $this->belongsTo(Knockout::class);
    }

    public function teamEntry(): BelongsTo
    {
        return $this->belongsTo(SeasonTeamEntry::class, 'season_team_entry_id');
    }

    public function existingTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'existing_team_id');
    }
}
