<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use App\Models\Ruleset;
use App\Models\Team;
use App\Models\Venue;
use App\Support\SectionSheetParser;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSectionImportAction(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSectionImportAction(): Actions\Action
    {
        return Actions\Action::make('importSection')
            ->label('Import section')
            ->icon('heroicon-o-arrow-down-on-square-stack')
            ->color('primary')
            ->modalHeading('Import section & teams')
            ->modalSubmitActionLabel('Import')
            ->form($this->getSectionImportFormSchema())
            ->action(fn (array $data) => $this->handleImportSection($data));
    }

    protected function getSectionImportFormSchema(): array
    {
        return [
            \Filament\Schemas\Components\Section::make('Source data')
                ->columnSpanFull()
                ->description('Paste the CSV export for the section you want to import.')
                ->schema([
                    Forms\Components\TextInput::make('section_name')
                        ->label('Section name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('ruleset_id')
                        ->label('Ruleset')
                        ->options(fn () => Ruleset::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Textarea::make('raw_csv')
                        ->label('CSV data')
                        ->rows(10)
                        ->helperText('Include the heading rows – the importer will ignore anything that is not a team.')
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $this->hydrateTeamsFromCsv($set, $state)),
                ]),
            \Filament\Schemas\Components\Section::make('Teams')
                ->columnSpanFull()
                ->description('Match imported names to existing teams or create new ones.')
                ->visible(fn (Get $get) => filled($get('teams')))
                ->schema([
                    Forms\Components\Repeater::make('teams')
                        ->label(false)
                        ->columns(2)
                        ->defaultItems(0)
                        ->schema([
                            Forms\Components\TextInput::make('position_label')
                                ->label('#')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('raw_name')
                                ->label('Imported name')
                                ->disabled()
                                ->columnSpan(1)
                                ->extraAttributes(fn (Get $get) => [
                                    'class' => filled($get('team_id')) ? 'text-success-600 font-medium' : 'text-danger-600 font-medium',
                                ]),
                            Forms\Components\Select::make('team_id')
                                ->label('Existing team')
                                ->options(fn () => Team::query()->withTrashed()->orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->placeholder('Select a team')
                                ->reactive()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('new_team_name')
                                ->label('Create as new team')
                                ->placeholder('Enter a team name to create one automatically')
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => blank($get('team_id')))
                                ->required(fn (Get $get) => blank($get('team_id'))),
                            Forms\Components\Select::make('new_team_venue_id')
                                ->label('Venue')
                                ->placeholder('Select a venue for the new team')
                                ->options(fn () => Venue::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->preload()
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => blank($get('team_id')))
                                ->required(fn (Get $get) => blank($get('team_id'))),
                            Forms\Components\Hidden::make('sort_order'),
                        ])
                        ->disableItemCreation()
                        ->disableItemDeletion()
                        ->disableItemMovement()
                        ->itemLabel(fn (array $state): ?string => $state['raw_name'] ?? null),
                ]),
        ];
    }

    protected function hydrateTeamsFromCsv(Set $set, ?string $state): void
    {
        if (blank($state)) {
            $set('teams', []);
            return;
        }

        $parsed = SectionSheetParser::parse($state);

        if ($parsed->sectionName) {
            $set('section_name', $parsed->sectionName);
        }

        $set('teams', collect($parsed->teams)
            ->map(function (array $team, int $index) {
                $existingTeam = $this->findTeamByName($team['name']);
                $sortOrder = $this->determineSortOrder($team, $index);

                return [
                    'position_label' => $team['label'],
                    'raw_name' => $team['name'],
                    'team_id' => $existingTeam?->id,
                    'new_team_name' => $existingTeam ? null : $team['name'],
                    'new_team_venue_id' => null,
                    'sort_order' => $sortOrder,
                ];
            })
            ->values()
            ->all());
    }

    protected function handleImportSection(array $data): void
    {
        $ruleset = Ruleset::query()->find($data['ruleset_id']);

        if (! $ruleset) {
            throw ValidationException::withMessages([
                'ruleset_id' => 'Select a valid ruleset.',
            ]);
        }

        $sectionName = trim((string) ($data['section_name'] ?? ''));

        if ($sectionName === '') {
            throw ValidationException::withMessages([
                'section_name' => 'Section name is required.',
            ]);
        }

        $teams = $data['teams'] ?? [];

        if (blank($teams)) {
            throw ValidationException::withMessages([
                'raw_csv' => 'No teams were detected in the CSV data.',
            ]);
        }

        $season = $this->getRecord();
        $section = null;

        DB::transaction(function () use ($teams, $ruleset, $season, $sectionName, &$section) {
            $section = $season->sections()->create([
                'name' => $sectionName,
                'ruleset_id' => $ruleset->id,
            ]);

            foreach ($teams as $index => $teamData) {
                $team = $this->resolveTeamFromMapping($teamData, $index);

                $section->teams()->attach($team->id, [
                    'sort' => $this->determineSortOrder($teamData, $index),
                ]);
            }
        });

        Notification::make()
            ->title('Section imported')
            ->body(sprintf(
                '%s and %d teams were imported to %s.',
                $section->name,
                count($teams),
                $season->name
            ))
            ->success()
            ->send();
    }

    protected function resolveTeamFromMapping(array $teamData, int $index): Team
    {
        $teamId = $teamData['team_id'] ?? null;

        if ($teamId) {
            $team = Team::query()->withTrashed()->find($teamId);

            if ($team) {
                return $team;
            }
        }

        $newTeamName = trim((string) ($teamData['new_team_name'] ?? ''));
        $newTeamVenueId = $teamData['new_team_venue_id'] ?? null;

        if ($newTeamName !== '') {
            if (! $newTeamVenueId) {
                throw ValidationException::withMessages([
                    "teams.{$index}.new_team_venue_id" => sprintf(
                        'Select a venue for "%s".',
                        $teamData['raw_name'] ?? 'team'
                    ),
                ]);
            }

            $venue = Venue::query()->find($newTeamVenueId);

            if (! $venue) {
                throw ValidationException::withMessages([
                    "teams.{$index}.new_team_venue_id" => 'Select a valid venue.',
                ]);
            }

            return Team::create([
                'name' => $newTeamName,
                'shortname' => $this->makeShortName($newTeamName),
                'venue_id' => $venue->id,
            ]);
        }

        throw ValidationException::withMessages([
            "teams.{$index}.team_id" => sprintf(
                'Select an existing team or enter a new team name for "%s".',
                $teamData['raw_name'] ?? 'team'
            ),
        ]);
    }

    protected function findTeamByName(string $name): ?Team
    {
        $normalized = mb_strtolower(trim($name));

        if ($normalized === '') {
            return null;
        }

        return Team::query()
            ->withTrashed()
            ->where(function ($query) use ($normalized) {
                $query->whereRaw('LOWER(name) = ?', [$normalized])
                    ->orWhereRaw('LOWER(shortname) = ?', [$normalized]);
            })
            ->first();
    }

    protected function makeShortName(string $name): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($name)) ?: 'Team';

        return Str::of($clean)->limit(20)->toString();
    }

    /**
     * Normalise the imported position into a valid sort order for fixtures.
     */
    protected function determineSortOrder(array $team, int $index): int
    {
        $label = trim((string) ($team['label'] ?? $team['position_label'] ?? ''));

        if ($label !== '' && ctype_digit($label)) {
            $numericLabel = (int) $label;

            if ($numericLabel === 0) {
                return 10;
            }

            return $numericLabel;
        }

        $sort = (int) ($team['sort'] ?? $team['sort_order'] ?? 0);

        if ($sort > 0) {
            return $sort;
        }

        return $index + 1;
    }
}
