<?php

namespace App\Models;

use App\KnockoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Knockout extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'name',
        'slug',
        'type',
        'best_of',
        'published_at',
    ];

    protected $casts = [
        'type' => KnockoutType::class,
        'best_of' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Knockout $knockout) {
            if (! $knockout->slug) {
                $knockout->slug = $knockout->generateSlug($knockout->name);
            }
        });

        static::updating(function (Knockout $knockout) {
            if ($knockout->isDirty('name')) {
                $knockout->slug = $knockout->generateSlug($knockout->name, $knockout->id);
            }
        });
    }

    private function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while (self::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(KnockoutParticipant::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(KnockoutRound::class)->orderBy('position');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(KnockoutMatch::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bestOfValue(): ?int
    {
        return $this->best_of ?? $this->type?->defaultBestOf();
    }

    public function requiresBestOf(): bool
    {
        return $this->type?->requiresBestOf() ?? false;
    }
}
