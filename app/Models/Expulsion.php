<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Section;

class Expulsion extends Model
{
    use HasFactory;

    protected static function booted()
    {
        $flush = fn (Expulsion $expulsion) => $expulsion->flushCaches();

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

    protected function flushCaches(): void
    {
        Cache::forget('stats:open-season');
        Cache::forget('history:index');
        Cache::forget('nav:past-seasons');

        if (! $this->season_id) {
            return;
        }

        $sections = Section::query()
            ->where('season_id', $this->season_id)
            ->get(['id', 'ruleset_id']);

        foreach ($sections as $section) {
            Cache::forget(sprintf('section:%d:averages', $section->id));
            Cache::forget(sprintf('section:%d:standings', $section->id));

            if ($section->ruleset_id) {
                Cache::forget(sprintf('history:sections:%d:%d', $this->season_id, $section->ruleset_id));
            }
        }
    }
}
