<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\NotificationSettingsTable;
use Filament\Pages\Page;

class NotificationSettings extends Page
{
    protected static ?string $title = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $slug = 'notifications';

    /**
     * @return array<class-string<NotificationSettingsTable>>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            NotificationSettingsTable::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
