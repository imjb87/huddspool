<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fixture;

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
        'captain_id',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function players()
    {
        return $this->hasMany(User::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class)
            ->withPivot('withdrawn_at')
            ->withTimestamps();
    }

    public function section()
    {
        return $this->sections()->whereHas('season', function ($query) {
            $query->where('is_open', true);
        })->first();
    }

    public function captain()
    {
        return $this->hasOne(User::class, 'id', 'captain_id');
    }

    public function fixtures()
    {
        return Fixture::where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->orWhere('away_team_id', $this->id);
        })->whereHas('season', function ($query) {
            $query->where('is_open', 1);
        })->get();
    }

    public function homeFixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

}
