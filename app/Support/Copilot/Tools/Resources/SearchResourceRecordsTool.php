<?php

declare(strict_types=1);

namespace App\Support\Copilot\Tools\Resources;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

abstract class SearchResourceRecordsTool extends ResourceTool
{
    public function description(): Stringable|string
    {
        return "Search {$this->getPluralResourceLabel()} by keyword.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('The search term to look for')->required(),
            'limit' => $schema->integer()->description('Maximum results to return (default: 10, max: 50)'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $query = trim((string) $request['query']);
        $limit = min(50, max(1, (int) ($request['limit'] ?? 10)));

        $records = $this->applySearchConstraint($this->getResourceQuery(), $query)
            ->limit($limit)
            ->get();

        if ($records->isEmpty()) {
            return "No {$this->getPluralResourceLabel()} found matching '{$query}'.";
        }

        $lines = [
            "Search results for '{$query}' in {$this->getPluralResourceLabel()} ({$records->count()} found):",
            '',
        ];

        foreach ($records as $record) {
            $this->authorizeViewRecord($record);
            $lines[] = $this->formatRecordSummary($record);
        }

        return implode("\n", $lines);
    }
}
