<?php

namespace App\Filament\Resources\RoundResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Team;
use App\Models\KnockoutMatch;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';
    protected static string $model = KnockoutMatch::class;

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema(fn(RelationManager $livewire) => $this->getFormFields($livewire));
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                $this->matchColumn('1', 'team1', 'pair1', 'player1', 'alignRight'),
                $this->fixtureDateColumn(),
                $this->matchColumn('2', 'team2', 'pair2', 'player2', 'alignLeft'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultGroup('venue.name');
    }

    private function matchColumn(string $key, string $teamKey, string $pairKey, string $playerKey, string $alignment): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make($key)
            ->label(false)
            ->{$alignment}()
            ->state(function (Model $record) use ($teamKey, $pairKey, $playerKey) {
                $type = $record->round->knockout->type->value;
    
                return match ($type) {
                    'singles' => optional($record->{$playerKey})->name ?: 'Winner of Above',
                    'doubles' => !empty(count(array_filter($record->{$pairKey})))
                        ? User::find($record->{$pairKey}[0])?->name . ' & ' . User::find($record->{$pairKey}[1])?->name
                        : 'Winner of Above',
                    'teams' => optional($record->{$teamKey})->name ?: 'Winner of Above',
                    default => 'Winner of Above',
                };
            });
    }    
        
    private function fixtureDateColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('fixture_date')
            ->label(false)
            ->alignCenter()
            ->state(fn(Model $record) => $record->score1 !== null && $record->score2 !== null
                ? $record->score1 . ' - ' . $record->score2
                : $record->round->date->format('d/m'));
    }

    private function getFormFields(RelationManager $livewire): array
    {
        $type = $livewire->getOwnerRecord()->knockout->type->value;
    
        $fields = match ($type) {
            'singles' => [
                $this->playerSelect('player1_id'),
                $this->playerSelect('player2_id'),
            ],
            'doubles' => [
                $this->pairRepeater('pair1'),
                $this->pairRepeater('pair2'),
            ],
            'teams' => [
                $this->teamSelect('team1_id'),
                $this->teamSelect('team2_id'),
            ],
            default => throw new \Exception("Unsupported knockout type: $type"),
        };
    
        $commonFields = $this->getCommonFields($livewire);
    
        return array_merge($fields, $commonFields);
    }

    private function playerSelect(string $label): Forms\Components\Select
    {
        return Forms\Components\Select::make($label)
            ->label(ucwords(str_replace('_', ' ', $label)))
            ->options(User::pluck('name', 'id')->sortBy('name'))
            ->searchable();
    }

    private function pairRepeater(string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($label)
            ->label(ucwords(str_replace('_', ' ', $label)))
            ->simple(
                Forms\Components\Select::make('player_id')
                    ->label('Player')
                    ->options(User::pluck('name', 'id')->sortBy('name'))
                    ->searchable()
            )
            ->defaultItems(2)
            ->deletable(false)
            ->addable(false);
    }

    private function teamSelect(string $label): Forms\Components\Select
    {
        return Forms\Components\Select::make($label)
            ->label(ucwords(str_replace('_', ' ', $label)))
            ->options(Team::pluck('name', 'id')->sortBy('name'))
            ->searchable();
    }

    private function getCommonFields(RelationManager $livewire): array
    {
        return [
            Forms\Components\Select::make('venue_id')
                ->label('Venue')
                ->relationship('venue', 'name')
                ->required(),
            Forms\Components\Repeater::make('dependancies')
                ->label('Dependancies')
                ->relationship('dependancies')
                ->simple(
                    Forms\Components\Select::make('depends_on_id')
                        ->label('Match')
                        ->relationship(
                            name: 'match', 
                            modifyQueryUsing: fn ($query) => $query->where('round_id', $livewire->getOwnerRecord()->id)
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->getTitleAttribute())
                )
                ->defaultItems(2)
                ->deletable(false)
                ->addable(false),
            Forms\Components\TextInput::make('score1')
                ->label('Score 1')
                ->type('number'),
            Forms\Components\TextInput::make('score2')
                ->label('Score 2')
                ->type('number'),
        ];
    }
}
