<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Team;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('foldTeam')
                ->label('Fold Team')
                ->icon('heroicon-o-archive-box')
                ->color('info')
                ->requiresConfirmation()
                ->action(fn (Team $team) => $team->update(['folded_at' => now()])),            
        ];
    }
}
