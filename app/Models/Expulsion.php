<?php

namespace App\Models;

use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Expulsion extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $cacheInvalidator = new CompetitionCacheInvalidator;
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
    public function expellable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the season associated with the expulsion.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
