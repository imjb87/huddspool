<?php

namespace App\Filament\Resources\RoundResource\Pages;

use App\Filament\Resources\RoundResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateRound extends CreateRecord
{
    use NestedPage;
    
    protected static string $resource = RoundResource::class;
}
