<?php

namespace App\Filament\Widgets;

use App\Enums\PermissionName;
use App\Models\NotificationSetting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NotificationSettingsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Notifications')
            ->description('Switch notification types on or off across email, in-app and browser alerts.')
            ->query(NotificationSetting::query()->orderBy('id'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Notification')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('description')
                    ->label('When it is sent')
                    ->wrap(),
                Tables\Columns\ToggleColumn::make('enabled')
                    ->label('Enabled')
                    ->alignCenter()
                    ->disabled(fn (): bool => ! auth()->user()?->can(PermissionName::AccessAdminPanel->value)),
            ])
            ->paginated(false)
            ->searchable(false);
    }
}
