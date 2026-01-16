<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Frame extends Model
{
    use HasFactory, \Awobaz\Compoships\Compoships;

    protected static function booted()
    {
        $flush = fn (Frame $frame) => $frame->flushAnalyticsCaches();

        static::saved($flush);
        static::deleted($flush);
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

    protected function flushAnalyticsCaches(): void
    {
        Cache::forget('stats:open-season');
        Cache::forget('stats:season-series');
        Cache::forget('history:index');
        Cache::forget('nav:past-seasons');

        $this->loadMissing('result.section', 'result.fixture');

        $sectionId = $this->result?->section_id ?? $this->result?->section?->id;
        if ($sectionId) {
            Cache::forget(sprintf('section:%d:averages', $sectionId));
            Cache::forget(sprintf('section:%d:standings', $sectionId));
        }

        $seasonId = $this->result?->fixture?->season_id ?? $this->result?->section?->season_id;
        $rulesetId = $this->result?->fixture?->ruleset_id ?? $this->result?->section?->ruleset_id;

        if ($seasonId) {
            Cache::forget(sprintf('history:season:%d', $seasonId));
        }

        if ($seasonId && $rulesetId) {
            Cache::forget(sprintf('history:sections:%d:%d', $seasonId, $rulesetId));
        }

        foreach ([$this->home_player_id, $this->away_player_id] as $playerId) {
            if ($playerId) {
                Cache::forget("player:season-history:{$playerId}");
            }
        }

        foreach ([$this->result?->home_team_id, $this->result?->away_team_id] as $teamId) {
            if ($teamId) {
                Cache::forget("team:season-history:{$teamId}");
            }
        }
    }
}
