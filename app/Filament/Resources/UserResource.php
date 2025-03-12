<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('John Doe'),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('john.doe@example.com'),
                        Forms\Components\Select::make('team_id')
                            ->label('Team')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('telephone')
                            ->label('Telephone')
                            ->placeholder('0123456789')
                            ->tel(),
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                '1' => 'Player',
                                '2' => 'Team Admin',
                            ])
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No email'),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No team'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        '1' => 'Player',
                        '2' => 'Team Admin',
                    })->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make(),
                Tables\Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
