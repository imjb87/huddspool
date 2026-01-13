<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Filament\Resources\RulesetResource\Pages;
use App\Models\Ruleset;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RulesetResource extends Resource
{
    protected static ?string $model = Ruleset::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Information')
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->placeholder('Ruleset name'),
                    Forms\Components\RichEditor::make('content')
                        ->label('Content')
                        ->required()
                        ->placeholder('Ruleset content'),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListRulesets::route('/'),
            'create' => Pages\CreateRuleset::route('/create'),
            'edit' => Pages\EditRuleset::route('/{record}/edit'),
        ];
    }
}
