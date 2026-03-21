<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpulsionResource\Pages;
use App\Models\Expulsion;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExpulsionResource extends Resource
{
    protected static ?string $model = Expulsion::class;

    protected static ?string $parentResource = SeasonResource::class;

    protected static ?string $modelLabel = 'Expulsion';

    protected static ?string $pluralModelLabel = 'Expulsions';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                MorphToSelect::make('expellable')
                    ->label('Who was expelled?')
                    ->types([
                        MorphToSelect\Type::make(User::class)
                            ->label('Player')
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Team::class)
                            ->titleAttribute('name'),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expellable.name')
                    ->label('Expelled')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->placeholder('-')
                    ->wrap(),
                Tables\Columns\TextColumn::make('date')
                    ->date('j M Y')
                    ->placeholder('-'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpulsions::route('/'),
            'create' => Pages\CreateExpulsion::route('/create'),
            'edit' => Pages\EditExpulsion::route('/{record}/edit'),
        ];
    }
}
