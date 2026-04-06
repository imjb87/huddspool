<?php

declare(strict_types=1);

namespace App\Support\Copilot\Tools\Resources;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

abstract class ViewResourceRecordTool extends ResourceTool
{
    public function description(): Stringable|string
    {
        return "View a single {$this->getResourceLabel()} by its ID.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description("The ID of the {$this->getResourceLabel()} to view")->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $record = $this->getResourceQuery()->find($request['id']);

        if (! $record) {
            return "{$this->getResourceLabel()} #{$request['id']} not found.";
        }

        $this->authorizeViewRecord($record);

        return $this->formatRecordDetail($record);
    }
}
