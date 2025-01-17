<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

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
    ];

    protected static function booted()
    {
        static::saved(function ($result) {
            // $result->fixture->update([
            //     'submitted_by' => auth()->id(),
            //     'home_team_name' => \App\Models\Team::find($result->home_team_id)->name,
            //     'away_team_name' => \App\Models\Team::find($result->away_team_id)->name,
            // ]);
        });
    }

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
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
