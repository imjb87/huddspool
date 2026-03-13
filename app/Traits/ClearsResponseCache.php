<?php

namespace App\Traits;

use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait ClearsResponseCache
{
    public static function bootClearsResponseCache(): void
    {
        $flush = function (Model $model): void {
            $model->flushRulesetCaches();
        };

        static::created($flush);
        static::updated($flush);
        static::deleted($flush);

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored($flush);
            static::forceDeleted($flush);
        }
    }

    protected function flushRulesetCaches(): void
    {
        app(CompetitionCacheInvalidator::class)->forgetForRulesetContent(
            $this->resolveRulesetSlug(),
            $this->resolveSectionId(),
            $this->resolveSeasonId(),
            $this->resolveRulesetId(),
        );
    }

    protected function resolveRulesetSlug(): ?string
    {
        if (method_exists($this, 'ruleset') && method_exists($this, 'relationLoaded') && ! $this->relationLoaded('ruleset')) {
            $this->loadMissing('ruleset');
        }

        $slug = optional($this->ruleset)->slug;

        if (! $slug && method_exists($this, 'section')) {
            if (method_exists($this, 'relationLoaded') && ! $this->relationLoaded('section')) {
                $this->loadMissing('section.ruleset');
            }

            $slug = optional(optional($this->section)->ruleset)->slug;
        }

        return $slug;
    }

    protected function resolveSectionId(): ?int
    {
        if (property_exists($this, 'section_id') && $this->section_id) {
            return (int) $this->section_id;
        }

        if (isset($this->section_id)) {
            return $this->section_id ? (int) $this->section_id : null;
        }

        if (method_exists($this, 'section')) {
            if (method_exists($this, 'relationLoaded') && ! $this->relationLoaded('section')) {
                $this->loadMissing('section');
            }

            if ($this->section) {
                return (int) $this->section->id;
            }
        }

        if (method_exists($this, 'fixture')) {
            $this->loadMissing('fixture.section');

            $fixture = $this->fixture ?? null;

            if ($fixture) {
                return $fixture->section_id ?? $fixture->section?->id;
            }
        }

        return null;
    }

    protected function resolveSeasonId(): ?int
    {
        if (property_exists($this, 'season_id') && $this->season_id) {
            return (int) $this->season_id;
        }

        if (isset($this->season_id)) {
            return $this->season_id ? (int) $this->season_id : null;
        }

        if (method_exists($this, 'section')) {
            if (method_exists($this, 'relationLoaded') && ! $this->relationLoaded('section')) {
                $this->loadMissing('section');
            }

            if ($this->section) {
                if (method_exists($this->section, 'relationLoaded') && ! $this->section->relationLoaded('season')) {
                    $this->section->loadMissing('season');
                }

                return $this->section->season_id ?? $this->section->season?->id;
            }
        }

        if (method_exists($this, 'fixture')) {
            $this->loadMissing('fixture');

            if ($this->fixture) {
                return $this->fixture->season_id;
            }
        }

        return null;
    }

    protected function resolveRulesetId(): ?int
    {
        if (property_exists($this, 'ruleset_id') && $this->ruleset_id) {
            return (int) $this->ruleset_id;
        }

        if (isset($this->ruleset_id)) {
            return $this->ruleset_id ? (int) $this->ruleset_id : null;
        }

        if (method_exists($this, 'ruleset')) {
            if (method_exists($this, 'relationLoaded') && ! $this->relationLoaded('ruleset')) {
                $this->loadMissing('ruleset');
            }

            if ($this->ruleset) {
                return (int) $this->ruleset->id;
            }
        }

        if (method_exists($this, 'section')) {
            if (method_exists($this, 'relationLoaded') && ! $this->relationLoaded('section')) {
                $this->loadMissing('section');
            }

            if ($this->section) {
                if (method_exists($this->section, 'relationLoaded') && ! $this->section->relationLoaded('ruleset')) {
                    $this->section->loadMissing('ruleset');
                }

                return $this->section->ruleset_id ?? $this->section->ruleset?->id;
            }
        }

        if (method_exists($this, 'fixture')) {
            $this->loadMissing('fixture');

            if ($this->fixture) {
                return $this->fixture->ruleset_id;
            }
        }

        return null;
    }
}
