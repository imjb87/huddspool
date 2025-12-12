<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Season extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Season $season) {
            if ($season->isDirty('name') || blank($season->slug)) {
                $season->slug = $season->generateSlug($season->name, $season->id);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'dates',
        'is_open',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
        'is_open' => 'boolean',
    ];

    public function isOpen(): bool
    {
        return $this->is_open;
    }

    /**
     * Get the sections for the season.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the knockouts for the season.
     */
    public function knockouts()
    {
        return $this->hasMany(Knockout::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
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

    /**
     * Determine if the season should appear in history (either closed or all scheduled weeks completed).
     */
    public function hasConcluded(): bool
    {
        if (! $this->is_open) {
            return true;
        }

        $lastWeek = $this->lastScheduledDate();

        if (! $lastWeek) {
            return false;
        }

        return now()->greaterThanOrEqualTo($lastWeek->copy()->endOfDay());
    }

    /**
     * Fetch the final scheduled date for the season.
     */
    public function lastScheduledDate(): ?Carbon
    {
        return collect($this->dates ?? [])
            ->flatten()
            ->filter()
            ->map(function ($value) {
                if ($value instanceof Carbon) {
                    return $value;
                }

                if (is_string($value)) {
                    try {
                        return Carbon::parse($value);
                    } catch (\Throwable) {
                        return null;
                    }
                }

                return null;
            })
            ->filter()
            ->sort()
            ->last();
    }

    /**
     * Get the expulsions for the season.
     */
    public function expulsions()
    {
        return $this->hasMany(Expulsion::class);
    }
}
