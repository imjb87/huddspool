<?php

namespace App\Models;

use Database\Factories\SeasonTeamEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonTeamEntry extends Model
{
    /** @use HasFactory<SeasonTeamEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'season_entry_id',
        'existing_team_id',
        'ruleset_id',
        'second_ruleset_id',
        'existing_venue_id',
        'team_name',
        'contact_name',
        'contact_telephone',
        'venue_name',
        'venue_address',
        'venue_telephone',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(SeasonEntry::class, 'season_entry_id');
    }

    public function existingTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'existing_team_id');
    }

    public function existingVenue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'existing_venue_id');
    }

    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    public function secondRuleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class, 'second_ruleset_id');
    }
}
