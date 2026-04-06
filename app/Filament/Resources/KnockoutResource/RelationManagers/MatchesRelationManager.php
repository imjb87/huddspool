<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Venue;
use App\Support\KnockoutMatchVenueOptions;
use Closure;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    public function form(Schema $schema): Schema
    {
        return $schema->schema(function (RelationManager $livewire) {
            $knockout = $livewire->getOwnerRecord();
            $venueOptionsBuilder = app(KnockoutMatchVenueOptions::class);

            $participantSelect = function (string $column, string $relationship, string $label) use ($knockout) {
                return Forms\Components\Select::make($column)
                    ->label($label)
                    ->relationship(
                        name: $relationship,
                        titleAttribute: 'label',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $knockout->id)
                    )
                    ->getOptionLabelFromRecordUsing(fn (KnockoutParticipant $record) => $record->display_name)
                    ->getOptionLabelUsing(function ($value) use ($knockout) {
                        if (! $value) {
                            return null;
                        }

                        $participant = KnockoutParticipant::query()->find($value);

                        if (! $participant) {
                            return null;
                        }

                        $participant->setRelation('knockout', $knockout);

                        return $participant->display_name;
                    })
                    ->getSearchResultsUsing(function (string $search) use ($knockout) {
                        return KnockoutParticipant::query()
                            ->searchForKnockout($knockout, $search)
                            ->orderBy('knockout_participants.seed')
                            ->orderBy('knockout_participants.label')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function (KnockoutParticipant $participant) use ($knockout) {
                                $participant->setRelation('knockout', $knockout);

                                return [$participant->id => $participant->display_name];
                            })
                            ->toArray();
                    })
                    ->searchable();
            };

            $venueOptions = function (callable $get) use ($venueOptionsBuilder, $livewire, $knockout): array {
                $record = $livewire->getMountedTableActionRecord();
                $homeParticipantId = $get('home_participant_id') ?: $record?->home_participant_id;
                $awayParticipantId = $get('away_participant_id') ?: $record?->away_participant_id;
                $currentVenueId = $get('venue_id') ?: $record?->venue_id;

                return $venueOptionsBuilder->venueOptions(
                    $knockout,
                    $homeParticipantId ? (int) $homeParticipantId : null,
                    $awayParticipantId ? (int) $awayParticipantId : null,
                    $currentVenueId ? (int) $currentVenueId : null,
                );
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
                    $match = $record ? $record->replicate() : new KnockoutMatch;
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
                            ->where('name', 'like', '%'.$search.'%')
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
                        if ($knockout->type === KnockoutType::Team && $round && ! str_contains(strtolower($round->name), 'semi') && ! str_contains(strtolower($round->name), 'final')) {
                            $homeParticipantId = $get('home_participant_id');
                            $homeParticipant = $homeParticipantId ? KnockoutParticipant::find($homeParticipantId) : null;
                            $team = $homeParticipant?->team;

                            return $team?->venue_id;
                        }

                        return null;
                    })
                    ->rule(function (callable $get) use ($livewire, $knockout) {
                        return function (string $attribute, $value, Closure $fail) use ($get, $livewire, $knockout) {
                            if (! $value) {
                                return;
                            }

                            if ($get('override_home_venue')) {
                                return;
                            }

                            $record = $livewire->getMountedTableActionRecord();
                            $homeParticipantId = $get('home_participant_id') ?: $record?->home_participant_id;
                            $awayParticipantId = $get('away_participant_id') ?: $record?->away_participant_id;
                            $participantIds = collect([$homeParticipantId, $awayParticipantId])->filter();

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
                                $roundId = $get('knockout_round_id') ?: $record?->knockout_round_id;
                                $round = $roundId ? KnockoutRound::find($roundId) : null;
                                $homeVenueId = $participants
                                    ->firstWhere('id', $homeParticipantId)
                                    ?->team?->venue_id;
                                $roundName = strtolower((string) $round?->name);
                                $homeVenueAllowed = $knockout->type === KnockoutType::Team
                                    && $homeVenueId
                                    && (int) $homeVenueId === (int) $value
                                    && ! str_contains($roundName, 'semi')
                                    && ! str_contains($roundName, 'final');

                                if (! $homeVenueAllowed) {
                                    $fail('A match cannot be assigned to a participant\'s venue.');
                                }
                            }
                        };
                    }),
                Forms\Components\Toggle::make('override_home_venue')
                    ->label('Allow participant venue')
                    ->helperText('Enable to allow assigning a participant venue for this match.')
                    ->default(false),
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
                Forms\Components\Textarea::make('report_reason')
                    ->label('Why was this result submitted or changed?')
                    ->rows(2)
                    ->maxLength(1000)
                    ->helperText('Required in admin when entering or editing a result.')
                    ->visible(fn (callable $get) => filled($get('home_score')) || filled($get('away_score')) || filled($get('forfeit_participant_id')))
                    ->required(fn (callable $get) => filled($get('home_score')) || filled($get('away_score')) || filled($get('forfeit_participant_id')))
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
                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('Reported by')
                    ->placeholder('Not recorded'),
                Tables\Columns\TextColumn::make('reported_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->placeholder('Not submitted'),
                Tables\Columns\TextColumn::make('report_reason')
                    ->label('Why')
                    ->wrap()
                    ->placeholder('No reason recorded'),
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
                Actions\CreateAction::make()
                    ->slideOver()
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->slideOver()
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
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
