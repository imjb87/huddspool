<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KnockoutResource\Pages;
use App\Filament\Resources\KnockoutResource\RelationManagers;
use App\KnockoutType;
use App\Models\Knockout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class KnockoutResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Knockout::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'League Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Knockout details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('season_id')
                            ->relationship('season', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn (?Knockout $record) => filled($record?->season_id)),
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
                Tables\Actions\EditAction::make(),
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

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make(
            'knockouts',
            'season',
        );
    }
}
