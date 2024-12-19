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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\ActionSize;
use App\Http\Controllers\Auth\InviteController;
use Filament\Notifications\Notification;

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
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('John Doe'),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->placeholder('john.doe@example.com'),
                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        1 => 'Player',
                        2 => 'Team admin',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('telephone')
                    ->label('Telephone')
                    ->placeholder('0123456789')
                    ->tel(),
                Forms\Components\Select::make('is_admin')
                    ->label('Is admin')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
                    ->required()
                    ->default(0)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('is_admin')
                    ->badge()
                    ->color('success')
                    ->label(false)
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Admin' : ''),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_admin')
                    ->label('Is admin')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Action::make('invite')
                    ->label('Send Invite')
                    ->action(function (User $record) {
                        $inviteController = new \App\Http\Controllers\Auth\InviteController();
                        $inviteController->send($record);
                        Notification::make('Invitation sent to ' . $record->email)->success();
                    }),
                ])
                ->iconButton()
                ->size(ActionSize::Small),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
        ];
    }
}
