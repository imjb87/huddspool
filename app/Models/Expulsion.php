<?php

namespace App\Models;

use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expulsion extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $cacheInvalidator = app(CompetitionCacheInvalidator::class);
        $flush = function (Expulsion $expulsion) use ($cacheInvalidator): void {
            $cacheInvalidator->forgetForExpulsion($expulsion);
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'expellable_id',
        'expellable_type',
        'reason',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the owning expellable model (team or player).
     */
    public function expellable()
    {
        return $this->morphTo();
    }

    /**
     * Get the season associated with the expulsion.
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
