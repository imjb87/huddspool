<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class FixturesRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static ?string $title = 'Fixtures & Results';

    protected static string $relationship = 'fixtures';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fixture_date')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fixture_date')
            ->columns([
                Tables\Columns\TextColumn::make('homeTeam.name')->label('Home team')->alignRight()->searchable(),
                Tables\Columns\TextColumn::make('fixture_date')->label(false)->state(function (Model $record) {
                    return $record->result ? $record->result->home_score . ' - ' . $record->result->away_score : $record->fixture_date->format('d/m');
                })->alignCenter(),
                Tables\Columns\TextColumn::make('awayTeam.name')->label('Away team')->alignLeft()->searchable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('DeleteAllFixtures')
                    ->action(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->fixtures()->delete())
                    ->label('Delete all fixtures')
                    ->modalIcon('heroicon-o-trash')
                    ->modalHeading('Delete all fixtures')
                    ->modalDescription('Are you sure you want to delete all fixtures for this section? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete them')
                    ->visible(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->fixtures()->exists())
                    ->color('danger'),
            ])            
            ->paginated(5)
            ->defaultPaginationPageOption(5)
            ->recordUrl(
                fn (Model $record): string => route('filament.admin.resources.fixtures.edit', $record),
            )
            ->emptyStateActions([
                Tables\Actions\Action::make('GenerateFixtures')
                    ->action(function (RelationManager $livewire) {
                        $livewire->getOwnerRecord()->generateFixtures();
                    })
                    ->label('Generate fixtures')
                    ->icon('heroicon-o-arrow-path')
            ]);
    }
}
