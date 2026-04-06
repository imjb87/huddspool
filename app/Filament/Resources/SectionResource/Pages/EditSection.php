<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Section';
    }

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return null;
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
            'season' => $this->getRecord()->season,
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
                        ->title('Section cannot be deleted')
                        ->body('Sections with recorded results or frames cannot be deleted.')
                        ->send();

                    $action->halt();
                }),
        ];
    }
}
