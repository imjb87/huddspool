<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruleset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'content',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function openSections(): HasMany
    {
        return $this->sections()
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->orderBy('sections.id');
    }

    public function defaultOpenSection(?User $user = null): ?Section
    {
        $teamSectionId = $user?->team?->openSections()
            ->where('ruleset_id', $this->id)
            ->orderBy('sections.id')
            ->value('sections.id');

        if ($teamSectionId) {
            return $this->openSections()
                ->with('season')
                ->whereKey($teamSectionId)
                ->first();
        }

        if ($this->relationLoaded('openSections')) {
            return $this->getRelation('openSections')->first();
        }

        return $this->openSections()
            ->with('season')
            ->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
