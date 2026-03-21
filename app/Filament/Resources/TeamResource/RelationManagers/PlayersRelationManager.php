<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Enums\RoleName;
use App\Models\User;
use App\Support\SiteAuthorization;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PlayersRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
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
                    ->mutateDataUsing(function (array $data): array {
                        $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
                        unset($data['site_role']);

                        return SiteAuthorization::applyLegacyColumnsForRole($data, $role);
                    })
                    ->using(function (array $data) {
                        /** @var User $record */
                        $record = $this->getRelationship()->create($data);
                        SiteAuthorization::syncSpatieRoleFromLegacyColumns($record);

                        return $record;
                    }),
                Actions\AssociateAction::make(),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make()
                        ->fillForm(fn (User $record): array => [
                            'name' => $record->name,
                            'email' => $record->email,
                            'site_role' => SiteAuthorization::inferRoleNameFromLegacy(
                                $record->role !== null ? (string) $record->role : null,
                                (bool) $record->is_admin,
                            )->value,
                            'telephone' => $record->telephone,
                        ])
                        ->mutateDataUsing(function (array $data): array {
                            $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
                            unset($data['site_role']);

                            return SiteAuthorization::applyLegacyColumnsForRole($data, $role);
                        })
                        ->using(function (User $record, array $data): User {
                            $record->update($data);
                            SiteAuthorization::syncSpatieRoleFromLegacyColumns($record);

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
