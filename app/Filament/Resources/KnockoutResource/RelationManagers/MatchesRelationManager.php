<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use Filament\Actions;
use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Venue;
use Closure;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Validation\ValidationException;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    public function form(Schema $schema): Schema
    {
        return $schema->schema(function (RelationManager $livewire) {
            $knockout = $livewire->getOwnerRecord();

            $participantSelect = function (string $column, string $relationship, string $label) use ($knockout) {
                return Forms\Components\Select::make($column)
                    ->label($label)
                    ->relationship(
                        name: $relationship,
                        titleAttribute: 'label',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $knockout->id)
                    )
                    ->getOptionLabelFromRecordUsing(fn (KnockoutParticipant $record) => $record->display_name)
                    ->searchable();
            };

            $participantLocationCache = [];
            $participantLocations = function (?int $participantId) use (&$participantLocationCache) {
                if (! $participantId) {
                    return collect();
                }

                if (array_key_exists($participantId, $participantLocationCache)) {
                    return $participantLocationCache[$participantId];
                }

                $participant = KnockoutParticipant::query()
                    ->with([
                        'team.venue',
                        'playerOne.team.venue',
                        'playerTwo.team.venue',
                    ])
                    ->find($participantId);

                if (! $participant) {
                    return $participantLocationCache[$participantId] = collect();
                }

                $locations = collect();
                $collectVenue = function (?Venue $venue) use (&$locations) {
                    if (! $venue || $venue->latitude === null || $venue->longitude === null) {
                        return;
                    }

                    $locations->push([
                        'lat' => (float) $venue->latitude,
                        'lng' => (float) $venue->longitude,
                    ]);
                };

                $collectVenue($participant->team?->venue);
                $collectVenue($participant->playerOne?->team?->venue);
                $collectVenue($participant->playerTwo?->team?->venue);

                return $participantLocationCache[$participantId] = $locations;
            };

            $participantVenueCache = [];
            $participantVenueIds = function (?int $participantId) use (&$participantVenueCache) {
                if (! $participantId) {
                    return collect();
                }

                if (array_key_exists($participantId, $participantVenueCache)) {
                    return $participantVenueCache[$participantId];
                }

                $participant = KnockoutParticipant::query()
                    ->with([
                        'team',
                        'playerOne.team',
                        'playerTwo.team',
                    ])
                    ->find($participantId);

                if (! $participant) {
                    return $participantVenueCache[$participantId] = collect();
                }

                return $participantVenueCache[$participantId] = collect([
                    $participant->team?->venue_id,
                    $participant->playerOne?->team?->venue_id,
                    $participant->playerTwo?->team?->venue_id,
                ])
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();
            };

            $neutralPoint = function (callable $get) use ($knockout, $participantLocations, $livewire) {
                if (! in_array($knockout->type, [KnockoutType::Singles, KnockoutType::Doubles], true)) {
                    return null;
                }

                $record = $livewire->getMountedTableActionRecord();
                $homeParticipantId = $get('home_participant_id') ?: $record?->home_participant_id;
                $awayParticipantId = $get('away_participant_id') ?: $record?->away_participant_id;

                $points = collect();

                foreach ([$homeParticipantId, $awayParticipantId] as $participantId) {
                    $points = $points->merge($participantLocations($participantId));
                }

                if ($points->isEmpty()) {
                    return null;
                }

                return [
                    'lat' => $points->avg('lat'),
                    'lng' => $points->avg('lng'),
                ];
            };

            $distanceBetween = function (array $from, array $to) {
                $earthRadius = 6371; // kilometers

                $latFrom = deg2rad($from['lat']);
                $lonFrom = deg2rad($from['lng']);
                $latTo = deg2rad($to['lat']);
                $lonTo = deg2rad($to['lng']);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                return $earthRadius * $angle;
            };

            $calculateVenueSuggestions = function (callable $get) use ($neutralPoint, $distanceBetween, $livewire, $knockout, $participantVenueIds) {
                static $cache = [];

                $record = $livewire->getMountedTableActionRecord();
                $homeParticipantId = $get('home_participant_id') ?: $record?->home_participant_id;
                $awayParticipantId = $get('away_participant_id') ?: $record?->away_participant_id;
                $currentVenueId = $get('venue_id') ?: $record?->venue_id;
                $cacheKey = json_encode([$homeParticipantId, $awayParticipantId, $currentVenueId]);

                if (array_key_exists($cacheKey, $cache)) {
                    return $cache[$cacheKey];
                }

                $point = $neutralPoint($get);
                $excludedVenueIds = collect();

                if (in_array($knockout->type, [KnockoutType::Singles, KnockoutType::Doubles], true)) {
                    $excludedVenueIds = collect([$homeParticipantId, $awayParticipantId])
                        ->filter()
                        ->flatMap(fn ($participantId) => $participantVenueIds($participantId))
                        ->unique()
                        ->values();
                }

                $venues = Venue::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'latitude', 'longitude']);

                if ($excludedVenueIds->isNotEmpty()) {
                    $venues = $venues->reject(function (Venue $venue) use ($excludedVenueIds, $currentVenueId) {
                        if ($currentVenueId && (int) $venue->id === (int) $currentVenueId) {
                            return false;
                        }

                        return $excludedVenueIds->contains((int) $venue->id);
                    });
                }

                if ($point) {
                    $venues = $venues
                        ->filter(fn (Venue $venue) => $venue->latitude !== null && $venue->longitude !== null)
                        ->map(function (Venue $venue) use ($point, $distanceBetween) {
                            $distance = $distanceBetween($point, [
                                'lat' => (float) $venue->latitude,
                                'lng' => (float) $venue->longitude,
                            ]);

                            $venue->distance_from_neutral = $distance;

                            return $venue;
                        })
                        ->sortBy('distance_from_neutral')
                        ->values();
                }

                $limit = $point ? 25 : 20;
                $venues = $venues->take($limit);

                if ($currentVenueId && ! $venues->contains('id', $currentVenueId)) {
                    $currentVenue = Venue::find($currentVenueId);

                    if ($currentVenue) {
                        $venues->push($currentVenue);
                    }
                }

                $options = $venues
                    ->mapWithKeys(function (Venue $venue) use ($point) {
                        $label = $venue->name;

                        if ($point && isset($venue->distance_from_neutral)) {
                            $label .= sprintf(' (%.1f km from neutral point)', $venue->distance_from_neutral);
                        }

                        return [$venue->id => $label];
                    })
                    ->toArray();

                return $cache[$cacheKey] = [
                    'options' => $options,
                ];
            };

            $venueOptions = function (callable $get) use ($calculateVenueSuggestions) {
                $data = $calculateVenueSuggestions($get);

                return $data['options'] ?? [];
            };

            $scoreRule = function (callable $get) use ($livewire, $knockout) {
                return function (string $attribute, $value, Closure $fail) use ($get, $livewire, $knockout) {
                    $homeScore = $get('home_score');
                    $awayScore = $get('away_score');

                    if ($homeScore === '' || $homeScore === null || $awayScore === '' || $awayScore === null) {
                        return;
                    }

                    $homeScore = (int) $homeScore;
                    $awayScore = (int) $awayScore;

                    $roundId = $get('knockout_round_id') ?: $livewire->getMountedTableActionRecord()?->knockout_round_id;
                    $round = $roundId ? KnockoutRound::query()->where('knockout_id', $knockout->id)->find($roundId) : null;

                    $record = $livewire->getMountedTableActionRecord();
                    $match = $record ? $record->replicate() : new KnockoutMatch();
                    $match->knockout_id = $knockout->id;
                    $match->best_of = $get('best_of') ?: $record?->best_of;
                    $match->home_score = $homeScore;
                    $match->away_score = $awayScore;
                    $match->setRelation('knockout', $knockout);

                    if ($round) {
                        $match->knockout_round_id = $round->id;
                        $match->setRelation('round', $round);
                    }

                    try {
                        $match->ensureScoresAreValid($homeScore, $awayScore);
                    } catch (ValidationException $exception) {
                        foreach ($exception->errors() as $messages) {
                            foreach ($messages as $message) {
                                $fail($message);
                            }
                        }
                    }
                };
            };

            $forfeitOptions = function (callable $get) use ($knockout) {
                $participantIds = collect([
                    $get('home_participant_id'),
                    $get('away_participant_id'),
                ])->filter()->unique();

                if ($participantIds->isEmpty()) {
                    return [];
                }

                return KnockoutParticipant::query()
                    ->where('knockout_id', $knockout->id)
                    ->whereIn('id', $participantIds)
                    ->get()
                    ->mapWithKeys(function (KnockoutParticipant $participant) use ($knockout) {
                        $participant->setRelation('knockout', $knockout);

                        return [$participant->id => $participant->display_name];
                    })
                    ->toArray();
            };

            return [
                Forms\Components\Hidden::make('knockout_id')
                    ->default($knockout->id),
                Forms\Components\Select::make('knockout_round_id')
                    ->label('Round')
                    ->relationship(
                        name: 'round',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $knockout->id)
                    )
                    ->required(),
                $participantSelect('home_participant_id', 'homeParticipant', 'Home'),
                $participantSelect('away_participant_id', 'awayParticipant', 'Away'),
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                $venueSelect = Forms\Components\Select::make('venue_id')
                    ->label('Venue')
                    ->options(fn (callable $get) => $venueOptions($get))
                    ->searchable()
                    ->preload()
                    ->getSearchResultsUsing(function (string $search) {
                        return Venue::query()
                            ->where('name', 'like', '%' . $search . '%')
                            ->orderBy('name')
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        static $cache = [];

                        if (! $value) {
                            return null;
                        }

                        if (! array_key_exists($value, $cache)) {
                            $cache[$value] = Venue::find($value)?->name;
                        }

                        return $cache[$value];
                    })
                    ->default(function (callable $get) use ($knockout) {
                        // Only for team knockouts, and only for rounds before semi-finals
                        $roundId = $get('knockout_round_id');
                        $round = $roundId ? KnockoutRound::find($roundId) : null;
                        if ($knockout->type === \App\KnockoutType::Team && $round && !str_contains(strtolower($round->name), 'semi') && !str_contains(strtolower($round->name), 'final')) {
                            $homeParticipantId = $get('home_participant_id');
                            $homeParticipant = $homeParticipantId ? KnockoutParticipant::find($homeParticipantId) : null;
                            $team = $homeParticipant?->team;
                            return $team?->venue_id;
                        }
                        return null;
                    })
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            if (! $value) {
                                return;
                            }

                            $participantIds = collect([
                                $get('home_participant_id'),
                                $get('away_participant_id'),
                            ])->filter();

                            if ($participantIds->isEmpty()) {
                                return;
                            }

                            $participants = KnockoutParticipant::query()
                                ->with(['team', 'playerOne.team', 'playerTwo.team'])
                                ->whereIn('id', $participantIds)
                                ->get();

                            $conflict = $participants->contains(function (KnockoutParticipant $participant) use ($value) {
                                return collect([
                                    $participant->team?->venue_id,
                                    $participant->playerOne?->team?->venue_id,
                                    $participant->playerTwo?->team?->venue_id,
                                ])
                                    ->filter()
                                    ->contains(fn ($id) => (int) $id === (int) $value);
                            });

                            if ($conflict) {
                                $fail('A match cannot be assigned to a venue that belongs to one of the participants involved.');
                            }
                        };
                    }),
                Forms\Components\TextInput::make('referee')
                    ->maxLength(255)
                    ->label('Referee')
                    ->helperText('Optional official assigned to the match.'),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->seconds(false)
                    ->default(function (?KnockoutMatch $record) {
                        if ($record?->starts_at) {
                            return $record->starts_at;
                        }

                        return now()->setTime(20, 15);
                    }),
                Forms\Components\TextInput::make('best_of')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Override frames for this match only.'),
                Forms\Components\TextInput::make('home_score')
                    ->numeric()
                    ->minValue(0)
                    ->rule($scoreRule)
                    ->disabled(fn (callable $get) => filled($get('forfeit_participant_id'))),
                Forms\Components\TextInput::make('away_score')
                    ->numeric()
                    ->minValue(0)
                    ->rule($scoreRule)
                    ->disabled(fn (callable $get) => filled($get('forfeit_participant_id'))),
                Forms\Components\Select::make('forfeit_participant_id')
                    ->label('Forfeit by')
                    ->options(fn (callable $get) => $forfeitOptions($get))
                    ->searchable()
                    ->placeholder('Match played')
                    ->reactive()
                    ->helperText('Select the participant who forfeited to award the match automatically.')
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            if (! $value) {
                                return;
                            }

                            $validIds = collect([
                                $get('home_participant_id'),
                                $get('away_participant_id'),
                            ])->filter()->map(fn ($id) => (int) $id);

                            if (! $validIds->contains((int) $value)) {
                                $fail('Forfeit participant must be part of this match.');
                            }
                        };
                    })
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('forfeit_reason')
                    ->label('Forfeit reason')
                    ->rows(2)
                    ->maxLength(1000)
                    ->visible(fn (callable $get) => filled($get('forfeit_participant_id')))
                    ->required(fn (callable $get) => filled($get('forfeit_participant_id')))
                    ->columnSpanFull(),
            ];
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('round.name')->label('Round')->sortable(),
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\TextColumn::make('homeParticipant.display_name')->label('Home'),
                Tables\Columns\TextColumn::make('score')
                    ->state(function (KnockoutMatch $record) {
                        if ($record->forfeitParticipant) {
                            return 'Forfeit';
                        }

                        if ($record->home_score !== null && $record->away_score !== null) {
                            return "{$record->home_score} - {$record->away_score}";
                        }

                        return 'TBC';
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('awayParticipant.display_name')->label('Away'),
                Tables\Columns\TextColumn::make('starts_at')->label('Scheduled')->dateTime(),
                Tables\Columns\TextColumn::make('venue.name')->label('Venue')->wrap(),
            ])
            ->filters([
                SelectFilter::make('round')
                    ->relationship(
                        name: 'round',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $this->getOwnerRecord()->id)
                    )
                    ->label('Round'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->orderBy('knockout_round_id')->orderBy('position'))
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\Action::make('clear_result')
                    ->label('Clear result')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (KnockoutMatch $record) => $record->home_score !== null || $record->away_score !== null || $record->forfeit_participant_id)
                    ->action(fn (KnockoutMatch $record) => $record->clearResult())
                    ->successNotificationTitle('Result cleared'),
                Actions\DeleteAction::make(),
            ]);
    }
}
