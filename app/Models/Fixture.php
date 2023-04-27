<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

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
        'section_id',
        'venue_id',
    ];

    protected $casts = [
        'fixture_date' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

}
