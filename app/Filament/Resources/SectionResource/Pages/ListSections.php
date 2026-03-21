<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use App\Filament\Resources\SectionResource\Pages\Concerns\InteractsWithSectionImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSections extends ListRecords
{
    use InteractsWithSectionImport;

    protected static string $resource = SectionResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Sections';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getSectionImportAction(),
            Actions\CreateAction::make(),
        ];
    }
}
