<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'telephone',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function scopeWithActiveTeams(Builder $query): Builder
    {
        return $query->whereHas('teams', fn (Builder $teamQuery): Builder => $teamQuery
            ->inOpenSeason()
            ->whereNull('folded_at')
            ->notBye());
    }
}
