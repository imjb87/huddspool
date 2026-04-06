<?php

namespace App\Support\Copilot\Tools;

use EslamRedaDiv\FilamentCopilot\Tools\ListPagesTool as BaseListPagesTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class ListPagesTool extends BaseListPagesTool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'include_descriptions' => $schema
                ->boolean()
                ->description('Whether to include page descriptions in the response.')
                ->required(),
        ];
    }
}
