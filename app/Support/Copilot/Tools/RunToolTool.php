<?php

namespace App\Support\Copilot\Tools;

use EslamRedaDiv\FilamentCopilot\Tools\RunToolTool as BaseRunToolTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class RunToolTool extends BaseRunToolTool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'source_class' => $schema->string()->description('The fully qualified class name of the resource, page, or widget that owns the tool')->required(),
            'tool_class' => $schema->string()->description('The fully qualified class name of the tool to execute')->required(),
            'arguments' => $schema->string()->description('JSON object of arguments to pass to the tool. Use "{}" when the tool takes no arguments.')->required(),
        ];
    }
}
