<?php

namespace App\Filament\Resources\SeasonEntryResource\Pages;

use App\Filament\Resources\SeasonEntryResource;
use App\Models\SeasonEntry;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeasonEntry extends EditRecord
{
    protected static string $resource = SeasonEntryResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Entries';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markPaid')
                ->label('Mark paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => ! $this->getRecord()->isPaid())
                ->action(function (): void {
                    /** @var SeasonEntry $entry */
                    $entry = $this->getRecord();

                    $entry->markPaid();
                    $this->refreshFormData(['paid_at']);
                }),
        ];
    }
}
