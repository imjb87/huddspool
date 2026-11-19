<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Domain\Authorization\SiteAuthorization;
use App\Enums\RoleName;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class PlayersRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    public function form(Schema $schema): Schema
    {
        return $schema
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
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('site_role')
                    ->label('Role')
                    ->required()
                    ->options(SiteAuthorization::roleOptions(includeAdmin: false))
                    ->default(RoleName::Player->value),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->label('Avatar')
                    ->collection('avatars')
                    ->circular()
                    ->defaultImageUrl(asset('/images/user.jpg')),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('site_role')
                    ->getStateUsing(fn (User $record): string => $record->roleLabel())
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
                        unset($data['site_role']);

                        /** @var User $record */
                        $record = $this->getRelationship()->create($data);
                        SiteAuthorization::assignRole($record, $role);

                        return $record;
                    }),
                Actions\AssociateAction::make(),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make()
                        ->slideOver()
                        ->fillForm(fn (User $record): array => [
                            'name' => $record->name,
                            'email' => $record->email,
                            'site_role' => $record->roleName()->value,
                            'telephone' => $record->telephone,
                        ])
                        ->using(function (User $record, array $data): User {
                            $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
                            unset($data['site_role']);

                            $record->update($data);
                            SiteAuthorization::assignRole($record, $role);

                            if ($record->hasMedia('avatars')) {
                                $record->clearLegacyAvatarPath();
                            }

                            return $record;
                        }),
                    Actions\DissociateAction::make(),
                ]),
            ])
            ->bulkActions([

            ])
            ->defaultSort('name', 'asc');
    }
}
