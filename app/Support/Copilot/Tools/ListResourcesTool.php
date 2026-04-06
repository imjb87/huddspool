<?php

namespace App\Support\Copilot\Tools;

use EslamRedaDiv\FilamentCopilot\Tools\ListResourcesTool as BaseListResourcesTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class ListResourcesTool extends BaseListResourcesTool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'include_descriptions' => $schema
                ->boolean()
                ->description('Whether to include resource descriptions in the response.')
                ->required(),
        ];
    }
}
