<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'shortname',
        'venue_id',
        'folded_at',
    ];

    /**
     * Get the venue associated with the team.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the players associated with the team.
     */
    public function players()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the sections the team belongs to.
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_team', 'team_id', 'section_id')
            ->withTimestamps();
    }

    /**
     * Get the active section for the team.
     */
    public function activeSection()
    {
        return $this->sections()->whereHas('season', function ($query) {
            $query->where('is_open', true);
        })->first();
    }

    /**
     * Get the captain of the team from roles.
     */
    public function captain()
    {
        return $this->hasOne(User::class)->role('captain');
    }

    /**
     * Get all fixtures (home and away) for the team.
     */
    public function fixtures()
    {
        return Fixture::where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->orWhere('away_team_id', $this->id);
        })->whereHas('season', function ($query) {
            $query->where('is_open', true);
        })->get();
    }

    /**
     * Get the home fixtures for the team.
     */
    public function homeFixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the results for the team.
     */
    public function results()
    {
        return Result::where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->orWhere('away_team_id', $this->id);
        })->whereHas('fixture', function ($query) {
            $query->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
        })->get();
    }

    /**
     * Get the expulsions for the team.
     */
    public function expulsions()
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }

    public function sectionTeams()
    {
        return $this->hasMany(SectionTeam::class);
    }
}
