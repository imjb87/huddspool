<?php

declare(strict_types=1);

namespace App\Support\Copilot\Tools\Resources;

use EslamRedaDiv\FilamentCopilot\Tools\BaseTool;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class ResourceTool extends BaseTool
{
    /**
     * @return class-string<Resource>
     */
    abstract protected static function resourceClass(): string;

    protected function getResourceClass(): string
    {
        return static::resourceClass();
    }

    protected function getResourceQuery(): Builder
    {
        $resource = $this->getResourceClass();

        if (config('filament-copilot.respect_authorization', true)) {
            $resource::authorizeViewAny();
        }

        return $resource::getEloquentQuery()->with($this->eagerLoadRelations());
    }

    protected function authorizeViewRecord(Model $record): void
    {
        if (! config('filament-copilot.respect_authorization', true)) {
            return;
        }

        $resource = $this->getResourceClass();

        $resource::authorizeView($record);
    }

    protected function getResourceLabel(): string
    {
        return $this->getResourceClass()::getTitleCaseModelLabel();
    }

    protected function getPluralResourceLabel(): string
    {
        return $this->getResourceClass()::getTitleCasePluralModelLabel();
    }

    protected function searchableColumns(): array
    {
        return [];
    }

    protected function displayColumns(): array
    {
        return $this->searchableColumns();
    }

    protected function eagerLoadRelations(): array
    {
        return collect(array_merge($this->searchableColumns(), $this->displayColumns()))
            ->filter(fn (string $column): bool => str_contains($column, '.'))
            ->map(fn (string $column): string => (string) str($column)->beforeLast('.'))
            ->unique()
            ->values()
            ->all();
    }

    protected function applySearchConstraint(Builder $query, string $search): Builder
    {
        $columns = $this->searchableColumns();

        if ($columns === []) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($columns, $search): void {
            foreach ($columns as $column) {
                if (! str_contains($column, '.')) {
                    $builder->orWhere($column, 'like', "%{$search}%");

                    continue;
                }

                $relationship = (string) str($column)->beforeLast('.');
                $attribute = (string) str($column)->afterLast('.');

                $builder->orWhereHas($relationship, function (Builder $relationshipQuery) use ($attribute, $search): void {
                    $relationshipQuery->where($attribute, 'like', "%{$search}%");
                });
            }
        });
    }

    protected function formatRecordSummary(Model $record): string
    {
        $title = $this->normalizeValue($this->getResourceClass()::getRecordTitle($record));

        $summary = collect($this->displayColumns())
            ->mapWithKeys(function (string $column) use ($record): array {
                $value = $this->normalizeValue(data_get($record, $column));

                if ($value === null || $value === '') {
                    return [];
                }

                return [$column => $value];
            })
            ->map(fn (string $value, string $column): string => "{$column}: {$value}")
            ->implode(', ');

        $prefix = "- #{$record->getKey()}";

        if ($title !== null && $title !== '' && $title !== $this->getResourceLabel()) {
            $prefix .= " ({$title})";
        }

        return $summary === '' ? $prefix : "{$prefix}: {$summary}";
    }

    protected function formatRecordDetail(Model $record): string
    {
        $title = $this->normalizeValue($this->getResourceClass()::getRecordTitle($record));
        $heading = "{$this->getResourceLabel()} #{$record->getKey()}";

        if ($title !== null && $title !== '' && $title !== $this->getResourceLabel()) {
            $heading .= " ({$title})";
        }

        $lines = [$heading . ':', ''];

        foreach (collect($this->displayColumns())->prepend($record->getKeyName())->unique() as $column) {
            $value = $this->normalizeValue(data_get($record, $column));

            if ($value === null || $value === '') {
                continue;
            }

            $lines[] = "  {$column}: {$value}";
        }

        return implode("\n", $lines);
    }

    protected function normalizeValue(mixed $value): ?string
    {
        if ($value instanceof Htmlable) {
            $value = $value->toHtml();
        }

        if ($value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        if ($value instanceof Collection) {
            $value = $value->all();
        }

        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        if (is_scalar($value)) {
            return mb_substr((string) $value, 0, 200);
        }

        if (is_array($value)) {
            return mb_substr((string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, 200);
        }

        return null;
    }
}
