<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RulesetResource\Pages;
use App\Models\Ruleset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RulesetResource extends Resource
{
    protected static ?string $model = Ruleset::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')
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
                Tables\Actions\EditAction::make()->color('warning'),
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
