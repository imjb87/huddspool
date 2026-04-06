<?php

declare(strict_types=1);

namespace App\Support\Copilot\Tools\Resources;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

abstract class ListResourceRecordsTool extends ResourceTool
{
    public function description(): Stringable|string
    {
        return "List {$this->getPluralResourceLabel()} with pagination.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()->description('Page number (default: 1)'),
            'per_page' => $schema->integer()->description('Items per page (default: 15, max: 50)'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $page = max(1, (int) ($request['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($request['per_page'] ?? 15)));

        $records = $this->getResourceQuery()->paginate($perPage, ['*'], 'page', $page);

        if ($records->isEmpty()) {
            return "No {$this->getPluralResourceLabel()} found.";
        }

        $lines = [
            "{$this->getPluralResourceLabel()} — Page {$records->currentPage()} of {$records->lastPage()} ({$records->total()} total)",
            '',
        ];

        foreach ($records as $record) {
            $this->authorizeViewRecord($record);
            $lines[] = $this->formatRecordSummary($record);
        }

        return implode("\n", $lines);
    }
}
