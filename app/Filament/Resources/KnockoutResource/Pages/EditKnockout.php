<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use App\Services\KnockoutBracketBuilder;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditKnockout extends EditRecord
{
    protected static string $resource = KnockoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateBracket')
                ->label('Generate Bracket')
                ->icon('heroicon-o-cube')
                ->color('primary')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Toggle::make('shuffle')
                        ->label('Shuffle participants')
                        ->helperText('Randomly shuffle seeds before pairing participants.'),
                ])
                ->action(function (array $data) {
                    $builder = new KnockoutBracketBuilder($this->record);

                    try {
                        $builder->generate((bool) ($data['shuffle'] ?? false));
                        $this->record->refresh();

                        Notification::make()
                            ->title('Bracket generated successfully.')
                            ->success()
                            ->send();
                    } catch (ValidationException $exception) {
                        $this->addError('generateBracket', $exception->getMessage());

                        Notification::make()
                            ->title($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
