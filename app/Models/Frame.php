<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory, \Awobaz\Compoships\Compoships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'result_id',
        'home_player_id',
        'home_score',
        'away_player_id',
        'away_score',
    ];

    /**
     * Get the result that owns the frame.
     */
    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    /**
     * Get the home player that owns the frame.
     */
    public function homePlayer()
    {
        return $this->belongsTo(User::class, 'home_player_id');
    }

    /**
     * Get the away player that owns the frame.
     */
    public function awayPlayer()
    {
        return $this->belongsTo(User::class, 'away_player_id');
    }
}
