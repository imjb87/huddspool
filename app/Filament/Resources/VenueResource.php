<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Filament\Resources\VenueResource\Pages;
use App\Filament\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Information')
                ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Venue name'),
                        Forms\Components\TextInput::make('telephone')
                            ->label('Telephone')
                            ->placeholder('0123456789')
                            ->tel(),
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->required()
                            ->placeholder('Venue address')
                            ->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Location')
                ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically populated from the address.')
                            ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 7) : null),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically populated from the address.')
                            ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 7) : null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TeamsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
