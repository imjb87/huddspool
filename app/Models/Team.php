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
        return $this->belongsToMany(Section::class);
    }

    public function section()
    {
        return $this->sections()->whereHas('season', function ($query) {
            $query->where('is_open', true);
        })->first();
    }

    public function captain()
    {
        return $this->hasOne(User::class);
    }

    public function fixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id')
            ->orWhere('away_team_id', $this->id)
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

}
