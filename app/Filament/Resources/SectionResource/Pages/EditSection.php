<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->disabled(fn (): bool => $this->getRecord()->hasRecordedResults())
                ->before(function (Actions\DeleteAction $action): void {
                    if (! $this->getRecord()->hasRecordedResults()) {
                        return;
                    }

                    Notification::make()
                        ->warning()
                        ->title('Section cannot be deleted')
                        ->body('Sections with recorded results or frames cannot be deleted.')
                        ->send();

                    $action->halt();
                }),
        ];
    }
}
