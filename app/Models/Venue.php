<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Venue extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public function searchableAs(): string
    {
        return config('scout.prefix').'venues';
    }

    public function toSearchableArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'address' => $this->address,
            'created_at' => $this->created_at?->getTimestamp() ?? 0,
        ], static fn (mixed $value): bool => $value !== null && $value !== []);
    }

    public function shouldBeSearchable(): bool
    {
        return ! $this->trashed();
    }

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
