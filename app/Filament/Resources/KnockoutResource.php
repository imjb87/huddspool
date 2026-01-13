<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Filament\Resources\KnockoutResource\Pages;
use App\Filament\Resources\KnockoutResource\RelationManagers;
use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Season;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnockoutResource extends Resource
{
    protected static ?string $model = Knockout::class;

    protected static ?string $parentResource = SeasonResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Knockout details')
                ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('season_id')
                            ->relationship('season', 'name')
                            ->searchable()
                            ->required()
                            ->default(function (?Knockout $record, $livewire) {
                                if ($record?->season_id) {
                                    return $record->season_id;
                                }

                                if (method_exists($livewire, 'getParentRecord')) {
                                    return $livewire->getParentRecord()?->getKey();
                                }

                                return static::getContextSeasonId();
                            }),
                        Forms\Components\Select::make('type')
                            ->options(KnockoutType::class)
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('best_of')
                            ->label('Best of (frames)')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (Get $get) => in_array($get('type'), [
                                KnockoutType::Singles->value,
                                KnockoutType::Doubles->value,
                            ]))
                            ->helperText('First to half this number (rounded up). Leave blank for default.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('season.name')->label('Season')->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof KnockoutType) {
                            return $state->getLabel();
                        }

                        return $state ? KnockoutType::from($state)->getLabel() : 'Unknown';
                    }),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            'participants' => RelationManagers\ParticipantsRelationManager::class,
            'rounds' => RelationManagers\RoundsRelationManager::class,
            'matches' => RelationManagers\MatchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnockouts::route('/'),
            'create' => Pages\CreateKnockout::route('/create'),
            'edit' => Pages\EditKnockout::route('/{record}/edit'),
        ];
    }

    protected static function getContextSeasonId(): ?int
    {
        $route = request()?->route();

        if (! $route) {
            return null;
        }

        $seasonIdentifier = $route->parameter('record');

        if (! $seasonIdentifier) {
            return null;
        }

        return Season::query()
            ->whereKey($seasonIdentifier)
            ->orWhere('slug', $seasonIdentifier)
            ->value('id');
    }
}
