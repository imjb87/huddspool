<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateKnockout extends CreateRecord
{
    use NestedPage;

    protected static string $resource = KnockoutResource::class;
}
