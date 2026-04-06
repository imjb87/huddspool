<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\CopilotTools\ListUsersTool;
use App\Filament\Resources\UserResource\CopilotTools\SearchUsersTool;
use App\Filament\Resources\UserResource\CopilotTools\ViewUserTool;
use App\Enums\RoleName;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Support\SiteAuthorization;
use EslamRedaDiv\FilamentCopilot\Contracts\CopilotResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Actions\Impersonate;

class UserResource extends Resource implements CopilotResource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->label('Avatar')
                            ->collection('avatars')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->columnSpanFull(),
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
                        Forms\Components\Select::make('site_role')
                            ->label('Role')
                            ->options(SiteAuthorization::roleOptions())
                            ->default(RoleName::Player->value)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->label('Avatar')
                    ->collection('avatars')
                    ->circular()
                    ->defaultImageUrl(asset('/images/user.jpg')),
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
                Tables\Columns\TextColumn::make('site_role')
                    ->label('Role')
                    ->getStateUsing(fn (User $record): string => $record->roleLabel())
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make(),
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FramesRelationManager::class,
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

    public static function copilotResourceDescription(): ?string
    {
        return 'Manage user accounts, roles, teams, avatars, and account details.';
    }

    public static function copilotTools(): array
    {
        return [
            new ListUsersTool,
            new ViewUserTool,
            new SearchUsersTool,
        ];
    }
}
