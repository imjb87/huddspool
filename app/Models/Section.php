<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'season_id',
        'ruleset_id',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'section_team', 'section_id', 'team_id')
            ->using(SectionTeam::class) // Reference the custom pivot model
            ->withTimestamps()
            ->withPivot(['sort', 'section_id', 'team_id', 'deducted']);
    }
        
    /**
     * Get the season that owns the section.
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the ruleset that owns the section.
     */
    public function ruleset()
    {
        return $this->belongsTo(Ruleset::class);
    }

    /**
     * Get the fixtures for the section.
     */
    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    public function generateFixtures()
    {
        $fixture_service = new \App\Services\FixtureService($this);
        $schedule = $fixture_service->generate($this);

        foreach ($schedule as $fixtures) {
            foreach ($fixtures as $fixture) {
                $this->fixtures()->create($fixture);
            }
        }
    }

    /**
     * Get the results for the section through fixtures.
     */
    public function results()
    {
        return $this->hasManyThrough(Result::class, Fixture::class);
    }
}
