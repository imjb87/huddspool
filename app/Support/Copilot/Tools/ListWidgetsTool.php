<?php

namespace App\Support\Copilot\Tools;

use EslamRedaDiv\FilamentCopilot\Tools\ListWidgetsTool as BaseListWidgetsTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;

class ListWidgetsTool extends BaseListWidgetsTool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'include_descriptions' => $schema
                ->boolean()
                ->description('Whether to include widget descriptions in the response.')
                ->required(),
        ];
    }
}
