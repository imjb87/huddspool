<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Section;
use App\Services\FixtureService;
use Illuminate\Support\Carbon;

class SectionFixturePreviewBuilder
{
    /**
     * @return array<int, array{week:int,date:string,home_team:string,away_team:string,venue:string,conflicts:array<int,array{date:string,section:string,home_team:string,away_team:string}>,has_conflict:bool}>
     */
    public function build(Section $section): array
    {
        $fixtureService = new FixtureService($section);
        $schedule = $fixtureService->generate();
        $teams = $section->teams->keyBy('id');
        $venues = $section->teams->loadMissing('venue')->pluck('venue', 'venue_id');

        $fixtures = [];
        foreach ($schedule as $weekFixtures) {
            foreach ($weekFixtures as $fixture) {
                $home = $teams->get($fixture['home_team_id']);
                $away = $teams->get($fixture['away_team_id']);
                $venue = $venues->get($fixture['venue_id']);
                $date = $fixture['fixture_date']
                    ? Carbon::parse($fixture['fixture_date'])->format('d M Y')
                    : 'TBC';

                $fixtures[] = [
                    'week' => (int) ($fixture['week'] ?? 0),
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

        $this->addVenueConflicts($fixtures);

        foreach ($fixtures as &$preview) {
            unset($preview['date_raw'], $preview['venue_id']);
        }
        unset($preview);

        return $fixtures;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fixtures
     */
    private function addVenueConflicts(array &$fixtures): void
    {
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

                $key = $existingFixture->fixture_date->toDateString().'|'.$existingFixture->venue_id;
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

            $key = (string) $preview['date_raw'].'|'.(string) $preview['venue_id'];
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
    }
}
