<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Actions;
use App\Filament\Resources\FixtureResource;
use Filament\Forms;
use App\Models\Fixture;
use App\Services\FixtureService;
use Illuminate\Support\Carbon;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FixturesRelationManager extends RelationManager
{
    protected static ?string $title = 'Fixtures & Results';

    protected static string $relationship = 'fixtures';

    protected static ?string $relatedResource = FixtureResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
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
                Actions\Action::make('DeleteAllFixtures')
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
                fn (Model $record): string => FixtureResource::getUrl(
                    'edit',
                    ['record' => $record],
                    shouldGuessMissingParameters: true
                ),
            )
            ->emptyStateActions([
                Actions\Action::make('GenerateFixtures')
                    ->modalHeading('Preview fixtures')
                    ->modalSubmitActionLabel('Create fixtures')
                    ->form([
                        Forms\Components\Repeater::make('fixtures')
                            ->hiddenLabel()
                            ->defaultItems(0)
                            ->generateUuidUsing(false)
                            ->columns(4)
                            ->dehydrated(false)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                            ->schema([
                                Forms\Components\Hidden::make('has_conflict')
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('date')
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('home_team')
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('away_team')
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('venue')
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\Repeater::make('conflicts')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get): bool => (bool) $get('has_conflict'))
                                    ->defaultItems(0)
                                    ->generateUuidUsing(false)
                                    ->columns(4)
                                    ->disableItemCreation()
                                    ->disableItemDeletion()
                                    ->disableItemMovement()
                                    ->schema([
                                        Forms\Components\TextInput::make('date')
                                            ->hiddenLabel()
                                            ->extraFieldWrapperAttributes(['class' => 'border border-red-300 bg-red-50 rounded-lg'], merge: true)
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('section')
                                            ->hiddenLabel()
                                            ->extraFieldWrapperAttributes(['class' => 'border border-red-300 bg-red-50 rounded-lg'], merge: true)
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('home_team')
                                            ->hiddenLabel()
                                            ->extraFieldWrapperAttributes(['class' => 'border border-red-300 bg-red-50 rounded-lg'], merge: true)
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('away_team')
                                            ->hiddenLabel()
                                            ->extraFieldWrapperAttributes(['class' => 'border border-red-300 bg-red-50 rounded-lg'], merge: true)
                                            ->disabled()
                                            ->dehydrated(false),
                                    ])
                                    ->dehydrated(false),
                            ]),
                    ])
                    ->fillForm(function (): array {
                        $fixtures = $this->buildFixturePreview();

                        return [
                            'fixtures' => $fixtures,
                        ];
                    })
                    ->action(function (RelationManager $livewire) {
                        $livewire->getOwnerRecord()->generateFixtures();
                    })
                    ->label('Generate fixtures')
                    ->icon('heroicon-o-arrow-path')
            ]);
    }

    /**
     * @return array<int, array{date:string,home_team:string,away_team:string,venue:string,conflicts:array<int,array{date:string,section:string,home_team:string,away_team:string}>,has_conflict:bool}>
     */
    private function buildFixturePreview(): array
    {
        $section = $this->getOwnerRecord();
        $fixtureService = new FixtureService($section);
        $schedule = $fixtureService->generate();
        $teams = $section->teams->keyBy('id');
        $venues = $section->teams->loadMissing('venue')->pluck('venue', 'venue_id');

        $fixtures = [];
        foreach ($schedule as $weekNumber => $weekFixtures) {
            foreach ($weekFixtures as $fixture) {
                $home = $teams->get($fixture['home_team_id']);
                $away = $teams->get($fixture['away_team_id']);
                $venue = $venues->get($fixture['venue_id']);
                $date = $fixture['fixture_date']
                    ? Carbon::parse($fixture['fixture_date'])->format('d M Y')
                    : 'TBC';

                $fixtures[] = [
                    'date' => $date,
                    'date_raw' => $fixture['fixture_date'] ?? null,
                    'home_team' => $home?->name ?? 'TBC',
                    'away_team' => $away?->name ?? 'TBC',
                    'venue' => $venue?->name ?? 'TBC',
                    'venue_id' => $fixture['venue_id'] ?? null,
                    'conflicts' => [],
                    'has_conflict' => false,
                ];
            }
        }

        $dateValues = collect($fixtures)
            ->pluck('date_raw')
            ->filter()
            ->unique()
            ->values();
        $venueValues = collect($fixtures)
            ->pluck('venue_id')
            ->filter()
            ->unique()
            ->values();

        $existingFixturesByKey = [];
        if ($dateValues->isNotEmpty() && $venueValues->isNotEmpty()) {
            $existingFixtures = Fixture::query()
                ->whereIn('fixture_date', $dateValues)
                ->whereIn('venue_id', $venueValues)
                ->with([
                    'homeTeam:id,name',
                    'awayTeam:id,name',
                    'venue:id,name',
                    'section:id,name',
                ])
                ->get();

            foreach ($existingFixtures as $existingFixture) {
                if (! $existingFixture->fixture_date || ! $existingFixture->venue_id) {
                    continue;
                }

                $key = $existingFixture->fixture_date->toDateString() . '|' . $existingFixture->venue_id;
                $existingFixturesByKey[$key][] = [
                    'date' => $existingFixture->fixture_date?->format('d M Y') ?? 'TBC',
                    'home_team' => $existingFixture->homeTeam?->name ?? 'TBC',
                    'away_team' => $existingFixture->awayTeam?->name ?? 'TBC',
                    'section' => $existingFixture->section?->name ?? 'Unknown section',
                ];
            }
        }

        foreach ($fixtures as $index => $preview) {
            if (empty($preview['date_raw']) || empty($preview['venue_id'])) {
                continue;
            }

            $key = (string) $preview['date_raw'] . '|' . (string) $preview['venue_id'];
            $existing = $existingFixturesByKey[$key] ?? [];

            if (empty($existing)) {
                continue;
            }

            $conflictList = array_map(static function (array $existingFixture): array {
                return [
                    'date' => $existingFixture['date'] ?? 'TBC',
                    'section' => $existingFixture['section'] ?? 'Unknown section',
                    'home_team' => $existingFixture['home_team'] ?? 'TBC',
                    'away_team' => $existingFixture['away_team'] ?? 'TBC',
                ];
            }, $existing);

            $conflictList = array_values(array_unique(array_map('serialize', $conflictList)));
            $conflictList = array_map('unserialize', $conflictList);
            $conflictList = array_slice($conflictList, 0, 5);

            $fixtures[$index]['conflicts'] = $conflictList;
            $fixtures[$index]['has_conflict'] = true;
        }

        foreach ($fixtures as &$preview) {
            unset($preview['date_raw'], $preview['venue_id']);
        }
        unset($preview);

        return $fixtures;
    }

}
