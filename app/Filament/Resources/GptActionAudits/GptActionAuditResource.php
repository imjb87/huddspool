<?php

namespace App\Filament\Resources\GptActionAudits;

use App\Filament\Resources\GptActionAudits\Pages\ListGptActionAudits;
use App\Filament\Resources\GptActionAudits\Pages\ViewGptActionAudit;
use App\Models\GptActionAudit;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GptActionAuditResource extends Resource
{
    protected static ?string $model = GptActionAudit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Audit trail';

    protected static ?string $modelLabel = 'Audit entry';

    protected static ?string $pluralModelLabel = 'Audit trail';

    protected static ?string $recordTitleAttribute = 'action';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Action')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('action')->badge(),
                        TextEntry::make('created_at')->label('Performed')->dateTime('j M Y H:i:s'),
                        TextEntry::make('administrator.name')->label('Administrator'),
                        TextEntry::make('subject_type')
                            ->label('Subject type')
                            ->formatStateUsing(fn (?string $state): string => $state === null ? '—' : class_basename($state)),
                        TextEntry::make('subject_id')->label('Subject ID')->placeholder('—'),
                        TextEntry::make('ip_address')->label('IP address')->placeholder('—'),
                        TextEntry::make('user_agent')->label('User agent')->placeholder('—')->columnSpanFull(),
                    ]),
                Section::make('Changes')
                    ->schema([
                        KeyValueEntry::make('before')->placeholder('No previous values')->columnSpanFull(),
                        KeyValueEntry::make('after')->placeholder('No resulting values')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Performed')
                    ->dateTime('j M Y H:i')
                    ->sortable(),
                TextColumn::make('administrator.name')
                    ->label('Administrator')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state, GptActionAudit $record): string => sprintf(
                        '%s #%s',
                        $state === null ? 'Record' : class_basename($state),
                        $record->subject_id ?? '—',
                    )),
                TextColumn::make('ip_address')
                    ->label('IP address')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options(fn (): array => GptActionAudit::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->all()),
                SelectFilter::make('administrator_id')
                    ->label('Administrator')
                    ->relationship('administrator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGptActionAudits::route('/'),
            'view' => ViewGptActionAudit::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
