<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class Fixture extends Model
{
    use HasFactory, ClearsResponseCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'week',
        'fixture_date',
        'home_team_id',
        'away_team_id',
        'season_id',
        'section_id',
        'venue_id',
        'ruleset_id'
    ];

    protected $casts = [
        'fixture_date' => 'datetime',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id')->withTrashed();
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id')->withTrashed();
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function ruleset()
    {
        return $this->belongsTo(Ruleset::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

}
