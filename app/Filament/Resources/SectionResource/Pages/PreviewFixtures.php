<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use App\Models\Section;
use App\Support\SectionFixturePreviewBuilder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class PreviewFixtures extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SectionResource::class;

    protected string $view = 'filament.resources.section-resource.pages.preview-fixtures';

    /**
     * @var array<int, array{week:int,date:string,home_team:string,away_team:string,venue:string,conflicts:array<int,array{date:string,section:string,home_team:string,away_team:string}>,has_conflict:bool}>
     */
    public array $fixtures = [];

    public static function getNavigationLabel(): string
    {
        return 'Fixture preview';
    }

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-eye';
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        $section = static::resolveSection($parameters);

        if (! $section) {
            return false;
        }

        return ! $section->fixtures()->exists();
    }

    public static function canAccess(array $parameters = []): bool
    {
        $section = static::resolveSection($parameters);

        if (! $section) {
            return false;
        }

        return ! $section->fixtures()->exists() && parent::canAccess($parameters);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->parentRecord = $this->getRecord()->season;
        $this->fixtures = app(SectionFixturePreviewBuilder::class)->build($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return 'Fixture preview';
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
            'season' => $this->getRecord()->season,
        ];
    }

    /**
     * @return array<int, array{week:int,date:string,fixture_count:int,conflict_count:int,fixtures:array<int, array{week:int,date:string,home_team:string,away_team:string,venue:string,conflicts:array<int,array{date:string,section:string,home_team:string,away_team:string}>,has_conflict:bool}>}>
     */
    public function getWeekGroups(): array
    {
        return collect($this->fixtures)
            ->groupBy('week')
            ->map(function (Collection $fixtures, int|string $week): array {
                $weekFixtures = $fixtures->values()->all();

                return [
                    'week' => (int) $week,
                    'date' => (string) ($fixtures->first()['date'] ?? 'TBC'),
                    'fixture_count' => count($weekFixtures),
                    'conflict_count' => $fixtures->where('has_conflict', true)->count(),
                    'fixtures' => $weekFixtures,
                ];
            })
            ->values()
            ->all();
    }

    public function getTotalFixtureCount(): int
    {
        return count($this->fixtures);
    }

    public function getTotalConflictCount(): int
    {
        return collect($this->fixtures)
            ->where('has_conflict', true)
            ->count();
    }

    /**
     * @return array<int, array{week:int,date:string,fixture_count:int,conflict_count:int,fixtures:array<int, array{home_team:string,away_team:string,venue:string,conflict_lines:array<int, string>}>}>
     */
    public function getSchemaWeekGroups(): array
    {
        return collect($this->getWeekGroups())
            ->map(function (array $week): array {
                return [
                    'week' => $week['week'],
                    'date' => $week['date'],
                    'fixture_count' => $week['fixture_count'],
                    'conflict_count' => $week['conflict_count'],
                    'fixtures' => collect($week['fixtures'])
                        ->map(function (array $fixture): array {
                            return [
                                'home_team' => $fixture['home_team'],
                                'away_team' => $fixture['away_team'],
                                'venue' => $fixture['venue'],
                                'conflict_lines' => collect($fixture['conflicts'])
                                    ->map(fn (array $conflict): string => "{$conflict['section']} · {$conflict['home_team']} v {$conflict['away_team']}")
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    }

    public function canCreateFixtures(): bool
    {
        return ! $this->getRecord()->fixtures()->exists();
    }

    public function previewSchema(Schema $schema): Schema
    {
        $components = [
            SectionComponent::make('Preview summary')
                ->description($this->getTotalConflictCount() > 0
                    ? $this->getTotalFixtureCount().' fixtures across '.count($this->getWeekGroups()).' weeks. '.$this->getTotalConflictCount().' venue conflicts need review before creating.'
                    : $this->getTotalFixtureCount().' fixtures across '.count($this->getWeekGroups()).' weeks. No venue conflicts found in the generated schedule.')
                ->afterHeader([
                    Text::make(count($this->getWeekGroups()).' weeks')
                        ->badge()
                        ->color('gray'),
                    Text::make($this->getTotalFixtureCount().' fixtures')
                        ->badge()
                        ->color('gray'),
                    Text::make($this->getTotalConflictCount().' conflicts')
                        ->badge()
                        ->color($this->getTotalConflictCount() > 0 ? 'danger' : 'success'),
                ]),
        ];

        if (! $this->canCreateFixtures()) {
            $components[] = Callout::make('Fixtures already exist')
                ->warning()
                ->description('Delete the current fixtures before generating a new schedule from this preview.');
        }

        foreach ($this->getSchemaWeekGroups() as $week) {
            $components[] = SectionComponent::make('Week '.$week['week'])
                ->description($week['date'])
                ->compact()
                ->afterHeader([
                    Text::make($week['fixture_count'].' fixtures')
                        ->badge()
                        ->color('gray'),
                    Text::make($week['conflict_count'].' conflicts')
                        ->badge()
                        ->color($week['conflict_count'] > 0 ? 'danger' : 'gray'),
                ])
                ->schema([
                    Grid::make(1)
                        ->schema($this->makeFixtureComponents($week['fixtures'])),
                ]);
        }

        return $schema->components($components);
    }

    /**
     * @param  array<int, array{home_team:string,away_team:string,venue:string,conflict_lines:array<int, string>}>  $fixtures
     * @return array<int, SectionComponent>
     */
    protected function makeFixtureComponents(array $fixtures): array
    {
        return collect($fixtures)
            ->map(function (array $fixture): SectionComponent {
                $content = e($fixture['home_team'].' v '.$fixture['away_team']);

                if ($fixture['conflict_lines'] !== []) {
                    $content .= '<br><span class="text-xs text-danger-600 dark:text-danger-400">'.
                        collect($fixture['conflict_lines'])
                            ->map(fn (string $conflictLine): string => e($conflictLine))
                            ->implode('<br>')
                        .'</span>';
                }

                return SectionComponent::make([
                    Text::make(new HtmlString($content))
                        ->weight('medium'),
                ])
                    ->compact()
                    ->contained(false);
            })
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToSection')
                ->label('Back to section')
                ->url(SectionResource::getUrl('edit', [
                    'record' => $this->getRecord(),
                    'season' => $this->getRecord()->season,
                ], shouldGuessMissingParameters: true))
                ->color('gray'),
            Action::make('createFixtures')
                ->label('Create fixtures')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Create fixtures')
                ->modalDescription('This will create the fixtures shown in the preview for this section.')
                ->visible(fn (): bool => $this->getTotalFixtureCount() > 0)
                ->disabled(fn (): bool => ! $this->canCreateFixtures())
                ->action(function (): void {
                    /** @var Section $section */
                    $section = $this->getRecord();

                    if ($section->fixtures()->exists()) {
                        Notification::make()
                            ->warning()
                            ->title('Fixtures already exist')
                            ->body('Delete the current fixtures before generating a new schedule.')
                            ->send();

                        return;
                    }

                    $section->generateFixtures();

                    Notification::make()
                        ->success()
                        ->title('Fixtures created')
                        ->body('The section fixtures have been created.')
                        ->send();

                    $this->redirect(SectionResource::getUrl('edit', [
                        'record' => $section,
                        'season' => $section->season,
                    ], shouldGuessMissingParameters: true));
                }),
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected static function resolveSection(array $parameters): ?Section
    {
        $record = $parameters['record'] ?? null;

        if ($record instanceof Section) {
            return $record;
        }

        if (blank($record)) {
            return null;
        }

        return SectionResource::resolveRecordRouteBinding($record);
    }
}
