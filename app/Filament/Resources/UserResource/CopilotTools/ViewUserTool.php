<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\CopilotTools;

use App\Filament\Resources\UserResource;
use App\Support\Copilot\Tools\Resources\ViewResourceRecordTool;

class ViewUserTool extends ViewResourceRecordTool
{
    protected static function resourceClass(): string
    {
        return UserResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['name', 'email', 'telephone', 'team.name', 'role'];
    }

    protected function displayColumns(): array
    {
        return ['name', 'email', 'telephone', 'team.name', 'role', 'is_admin', 'email_verified_at'];
    }
}
