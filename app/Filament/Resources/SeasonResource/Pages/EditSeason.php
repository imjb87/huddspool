<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Season';
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
            'season' => $this->getRecord(),
        ];
    }

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
                        ->title('Season cannot be deleted')
                        ->body('Seasons with recorded results or frames cannot be deleted.')
                        ->send();

                    $action->halt();
                }),
        ];
    }
}
