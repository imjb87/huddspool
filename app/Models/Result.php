<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class Result extends Model
{
    use HasFactory, ClearsResponseCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fixture_id',
        'home_team_id',
        'home_team_name',
        'home_score',
        'away_team_id',
        'away_team_name',
        'away_score',
        'is_confirmed',
        'is_overridden',
        'submitted_by',
        'section_id',
    ];

    /**
     * Get the fixture that owns the result.
     */
    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get the result's frames.
     */
    public function frames()
    {
        return $this->hasMany(Frame::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function ruleset()
    {
        return $this->hasOneThrough(
            Ruleset::class,  // The final model we want to access
            Fixture::class,  // The intermediate model
            'id',            // Foreign key on the Fixture table
            'id',            // Foreign key on the Ruleset table
            'fixture_id',    // Local key on the Result table
            'ruleset_id'     // Local key on the Fixture table
        );
    }
    
}
