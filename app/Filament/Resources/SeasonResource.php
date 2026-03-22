<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpulsionResource\Pages\ListExpulsions;
use App\Filament\Resources\KnockoutResource\Pages\ListKnockouts;
use App\Filament\Resources\SeasonEntryResource\Pages\ListSeasonEntries;
use App\Filament\Resources\SeasonResource\Pages;
use App\Filament\Resources\SectionResource\Pages\ListSections;
use App\Models\Season;
use App\Support\CompetitionCacheInvalidator;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Main section spanning 2 columns
                Section::make('Season information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Season name'),
                        Forms\Components\TextInput::make('team_entry_fee')
                            ->label('Team entry fee')
                            ->numeric()
                            ->prefix('£')
                            ->default(0)
                            ->required(),
                        Forms\Components\DateTimePicker::make('signup_opens_at')
                            ->label('Sign-up opens at')
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('signup_closes_at')
                            ->label('Sign-up closes at')
                            ->seconds(false),
                    ]),

                // Dates on the right in a smaller section spanning 1 column
                Section::make('Schedule')
                    ->columnSpanFull()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\DatePicker::make('first_week_date')
                            ->label('First week date')
                            ->dehydrated(false)
                            ->helperText('Select the first week date, then generate the 18 weekly dates.')
                            ->formatStateUsing(fn (Get $get): ?string => static::firstScheduledDate($get('dates')))
                            ->hintAction(
                                Actions\Action::make('generateWeeks')
                                    ->label('Generate weeks')
                                    ->action(function (Get $get, Set $set): void {
                                        $set('dates', static::generateWeeklyDates($get('first_week_date')));
                                    })
                            ),
                        Forms\Components\Repeater::make('dates')
                            ->label(false)
                            ->simple(
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                            )
                            ->reorderable(false)
                            ->grid(6)
                            ->minItems(18)
                            ->maxItems(18)
                            ->defaultItems(18)
                            ->deletable(false),
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
                Tables\Columns\TextColumn::make('team_entry_fee')
                    ->label('Team fee')
                    ->money('GBP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_closes_at')
                    ->label('Sign-up closes')
                    ->dateTime('j M Y H:i')
                    ->sortable()
                    ->placeholder('Not set'),
                Tables\Columns\ToggleColumn::make('is_open')
                    ->label('Is Open?')
                    ->alignCenter()
                    ->beforeStateUpdated(function ($record, $state) {
                        Season::all()->each(function ($season) use ($record) {
                            if ($season->is_open && $season->id !== $record->id) {
                                $season->update(['is_open' => 0]);
                            }
                        });
                    })
                    ->afterStateUpdated(function (Season $record) {
                        (new CompetitionCacheInvalidator)->forgetForSeason($record);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditSeason::class,
            ListSections::class,
            ListKnockouts::class,
            ListSeasonEntries::class,
            ListExpulsions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int, string|array<string, string>|null>|null  $dates
     * @return list<array{date: string|null}>
     */
    public static function generateWeeklyDates(?string $startDate): array
    {
        if (blank($startDate)) {
            return collect(range(1, 18))
                ->map(fn (): array => ['date' => null])
                ->all();
        }

        $start = Carbon::parse($startDate)->startOfDay();

        return collect(range(0, 17))
            ->map(fn (int $week): array => ['date' => $start->copy()->addWeeks($week)->toDateString()])
            ->all();
    }

    /**
     * @param  array<int, string|array<string, string>|null>|null  $dates
     */
    public static function firstScheduledDate(?array $dates): ?string
    {
        if (! is_array($dates)) {
            return null;
        }

        return collect($dates)
            ->map(fn (mixed $item): ?string => is_array($item) ? ($item['date'] ?? null) : $item)
            ->filter(fn (?string $date): bool => filled($date))
            ->sort()
            ->first();
    }
}
