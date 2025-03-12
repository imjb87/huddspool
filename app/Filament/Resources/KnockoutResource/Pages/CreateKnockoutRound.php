<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateKnockoutRound extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = KnockoutResource::class;
    protected static string $relationship = 'rounds';
}