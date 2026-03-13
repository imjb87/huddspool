<?php

namespace App\Models;

use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frame extends Model
{
    use \Awobaz\Compoships\Compoships, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        $cacheInvalidator = app(CompetitionCacheInvalidator::class);
        $flush = function (Frame $frame) use ($cacheInvalidator): void {
            $cacheInvalidator->forgetForFrame($frame);
        };

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
        static::forceDeleted($flush);
    }

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
        return $this->belongsTo(User::class, 'home_player_id')->withTrashed();
    }

    /**
     * Get the away player that owns the frame.
     */
    public function awayPlayer()
    {
        return $this->belongsTo(User::class, 'away_player_id')->withTrashed();
    }
}
