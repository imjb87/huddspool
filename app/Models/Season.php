<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Season extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (Season $season) {
            if ($season->hasRecordedResults()) {
                return false;
            }
        });

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
        'signup_opens_at',
        'signup_closes_at',
        'team_entry_fee',
        'is_open',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
        'signup_opens_at' => 'datetime',
        'signup_closes_at' => 'datetime',
        'team_entry_fee' => 'decimal:2',
        'is_open' => 'boolean',
    ];

    public function isOpen(): bool
    {
        return $this->is_open;
    }

    /**
     * Get the sections for the season.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Get the knockouts for the season.
     */
    public function knockouts(): HasMany
    {
        return $this->hasMany(Knockout::class);
    }

    public function getRouteKeyName(): string
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

    public function currentOrPreviousScheduledWeek(?Carbon $date = null): int
    {
        $referenceDate = ($date ?? now())->copy()->startOfDay();

        $scheduledWeeks = collect($this->dates ?? [])
            ->flatten()
            ->values()
            ->map(function ($value, int $index) use ($referenceDate): ?array {
                if ($value instanceof Carbon) {
                    $scheduledDate = $value->copy()->startOfDay();
                } elseif (is_string($value)) {
                    try {
                        $scheduledDate = Carbon::parse($value)->startOfDay();
                    } catch (\Throwable) {
                        return null;
                    }
                } else {
                    return null;
                }

                return [
                    'week' => $index + 1,
                    'date' => $scheduledDate,
                    'is_same_week' => $scheduledDate->isoWeek() === $referenceDate->isoWeek()
                        && $scheduledDate->isoWeekYear() === $referenceDate->isoWeekYear(),
                ];
            })
            ->filter();

        $currentWeek = $scheduledWeeks->firstWhere('is_same_week', true);

        if ($currentWeek) {
            return (int) $currentWeek['week'];
        }

        $previousWeek = $scheduledWeeks
            ->filter(fn (array $week): bool => $week['date']->lessThan($referenceDate))
            ->sortByDesc(fn (array $week): int => $week['date']->getTimestamp())
            ->first();

        return (int) ($previousWeek['week'] ?? 1);
    }

    /**
     * Get the expulsions for the season.
     */
    public function expulsions(): HasMany
    {
        return $this->hasMany(Expulsion::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SeasonEntry::class);
    }

    public function hasRecordedResults(): bool
    {
        return $this->fixtures()->whereHas('result')->exists();
    }

    public function acceptingEntries(): bool
    {
        if ($this->signup_opens_at && now()->lt($this->signup_opens_at)) {
            return false;
        }

        if ($this->signup_closes_at === null) {
            return false;
        }

        return now()->lte($this->signup_closes_at);
    }
}
