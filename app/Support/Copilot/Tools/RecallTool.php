<?php

namespace App\Support\Copilot\Tools;

use EslamRedaDiv\FilamentCopilot\Tools\RecallTool as BaseRecallTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class RecallTool extends BaseRecallTool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'key' => $schema
                ->string()
                ->description('The memory key to recall. Use "*" to list all stored memories.')
                ->required(),
        ];
    }
}
