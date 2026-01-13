<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Frame;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HuddspoolSeeder extends Seeder
{
    /**
     * Seed the application's database with Huddspool-based data.
     */
    public function run(): void
    {
        $dataPath = database_path('seeders/data/huddspool.json');

        if (! is_file($dataPath)) {
            $this->command?->warn("Huddspool seed data not found at {$dataPath}.");
            return;
        }

        $payload = json_decode(file_get_contents($dataPath), true);

        if (! is_array($payload)) {
            $this->command?->warn('Huddspool seed data is not valid JSON.');
            return;
        }

        DB::transaction(function () use ($payload): void {
            $seasonData = Arr::get($payload, 'season', []);
            $seasonName = Arr::get($seasonData, 'name', 'Current Season');
            $teamsData = Arr::get($payload, 'teams', []);
            $teamCache = [];
            $teamNameCache = [];
            $rulesetCache = [];
            $sectionCache = [];

            $season = Season::query()->firstOrNew(['name' => $seasonName]);
            $season->is_open = (bool) Arr::get($seasonData, 'is_open', true);
            $season->dates = Arr::get($seasonData, 'dates', []);
            $season->save();

            foreach (Arr::get($payload, 'rulesets', []) as $rulesetData) {
                $rulesetName = Arr::get($rulesetData, 'name', 'Ruleset');
                $rulesetSlug = Arr::get($rulesetData, 'slug', Str::slug($rulesetName));

                $ruleset = Ruleset::query()->firstOrNew(['slug' => $rulesetSlug]);
                $ruleset->name = $rulesetName;
                $ruleset->slug = $rulesetSlug;
                $ruleset->save();
                $rulesetCache[$rulesetSlug] = $ruleset;

                foreach (Arr::get($rulesetData, 'sections', []) as $sectionData) {
                    $sectionName = Arr::get($sectionData, 'name', 'Section');

                    $section = Section::query()->firstOrNew([
                        'name' => $sectionName,
                        'season_id' => $season->id,
                        'ruleset_id' => $ruleset->id,
                    ]);
                    $section->save();
                    $sectionCache[$rulesetSlug][$sectionName] = $section;

                    $teamSync = [];

                    foreach (Arr::get($sectionData, 'teams', []) as $index => $teamKey) {
                        $teamKey = (string) $teamKey;
                        $teamData = Arr::get($teamsData, $teamKey, []);
                        $teamName = trim((string) Arr::get($teamData, 'name', ''));

                        if ($teamName === '') {
                            continue;
                        }

                        $isNewTeam = ! array_key_exists($teamKey, $teamCache);
                        $team = $teamCache[$teamKey] ?? $this->getOrCreateTeam($teamName);
                        $teamCache[$teamKey] = $team;
                        $teamNameCache[$teamName] = $team;

                        if ($isNewTeam) {
                            $this->seedPlayers($team, $teamData);
                        }

                        $teamSync[$team->id] = ['sort' => $index + 1];
                    }

                    if ($teamSync) {
                        $section->teams()->syncWithoutDetaching($teamSync);
                    }
                }
            }

            foreach (Arr::get($payload, 'fixtures', []) as $fixtureData) {
                $rulesetSlug = Arr::get($fixtureData, 'ruleset');
                $sectionName = Arr::get($fixtureData, 'section');
                $week = (int) Arr::get($fixtureData, 'week', 0);
                $fixtureDate = Arr::get($fixtureData, 'date');
                $homeTeamName = trim((string) Arr::get($fixtureData, 'home_team', ''));
                $awayTeamName = trim((string) Arr::get($fixtureData, 'away_team', ''));

                if (! $rulesetSlug || ! $sectionName || $week <= 0 || ! $fixtureDate || $homeTeamName === '' || $awayTeamName === '') {
                    continue;
                }

                $ruleset = $rulesetCache[$rulesetSlug] ?? Ruleset::query()->where('slug', $rulesetSlug)->first();
                $section = $sectionCache[$rulesetSlug][$sectionName] ?? Section::query()
                    ->where('name', $sectionName)
                    ->where('season_id', $season->id)
                    ->where('ruleset_id', $ruleset?->id)
                    ->first();

                if (! $ruleset || ! $section) {
                    continue;
                }

                $homeTeam = $teamNameCache[$homeTeamName] ?? $this->getOrCreateTeam($homeTeamName);
                $awayTeam = $teamNameCache[$awayTeamName] ?? $this->getOrCreateTeam($awayTeamName);
                $teamNameCache[$homeTeamName] = $homeTeam;
                $teamNameCache[$awayTeamName] = $awayTeam;

                $venueName = trim((string) Arr::get($fixtureData, 'venue', ''));
                $venue = $venueName !== '' ? $this->getOrCreateVenue($venueName) : $homeTeam->venue;

                $fixture = Fixture::query()->firstOrNew([
                    'season_id' => $season->id,
                    'section_id' => $section->id,
                    'week' => $week,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                ]);
                $fixture->fixture_date = $fixtureDate;
                $fixture->venue_id = $venue?->id;
                $fixture->ruleset_id = $ruleset->id;
                $fixture->save();

                $resultData = Arr::get($fixtureData, 'result');
                if (! is_array($resultData)) {
                    continue;
                }

                $homeScore = Arr::get($resultData, 'home_score');
                $awayScore = Arr::get($resultData, 'away_score');
                if ($homeScore === null || $awayScore === null) {
                    continue;
                }

                $result = Result::query()->firstOrNew(['fixture_id' => $fixture->id]);
                $result->home_team_id = $homeTeam->id;
                $result->home_team_name = $homeTeam->name;
                $result->home_score = (int) $homeScore;
                $result->away_team_id = $awayTeam->id;
                $result->away_team_name = $awayTeam->name;
                $result->away_score = (int) $awayScore;
                $result->is_confirmed = true;
                $result->is_overridden = false;
                $result->submitted_by = 0;
                $result->section_id = $section->id;
                $result->ruleset_id = $ruleset->id;
                $result->save();

                $frames = Arr::get($resultData, 'frames', []);
                if (! is_array($frames) || $frames === []) {
                    continue;
                }

                Frame::query()->where('result_id', $result->id)->delete();

                foreach ($frames as $frameData) {
                    $homePlayerName = trim((string) Arr::get($frameData, 'home_player', ''));
                    $awayPlayerName = trim((string) Arr::get($frameData, 'away_player', ''));
                    $homeFrameScore = Arr::get($frameData, 'home_score');
                    $awayFrameScore = Arr::get($frameData, 'away_score');

                    if ($homePlayerName === '' || $awayPlayerName === '' || $homeFrameScore === null || $awayFrameScore === null) {
                        continue;
                    }

                    $homePlayer = $this->getOrCreatePlayer($homePlayerName, $homeTeam);
                    $awayPlayer = $this->getOrCreatePlayer($awayPlayerName, $awayTeam);

                    Frame::create([
                        'result_id' => $result->id,
                        'home_player_id' => $homePlayer->id,
                        'home_score' => (int) $homeFrameScore,
                        'away_player_id' => $awayPlayer->id,
                        'away_score' => (int) $awayFrameScore,
                    ]);
                }
            }
        });
    }

    private function getOrCreateTeam(string $teamName): Team
    {
        $venue = Venue::query()->firstOrCreate(
            ['name' => $teamName],
            ['address' => 'Address unavailable', 'telephone' => null],
        );

        $team = Team::query()->firstOrNew(['name' => $teamName]);

        if (! $team->shortname) {
            $team->shortname = $this->makeShortname($teamName);
        }

        if (! $team->venue_id) {
            $team->venue_id = $venue->id;
        }

        $team->save();

        return $team;
    }

    private function seedPlayers(Team $team, array $teamData): void
    {
        $players = Arr::get($teamData, 'players', []);
        $captainName = Arr::get($teamData, 'captain');
        $captainUser = null;

        foreach ($players as $playerName) {
            $playerName = trim((string) $playerName);

            if ($playerName === '') {
                continue;
            }

            $user = User::query()->firstOrCreate(
                ['name' => $playerName, 'team_id' => $team->id],
                ['role' => '1'],
            );

            if ($captainName && $playerName === $captainName) {
                $user->role = '2';
                $user->save();
                $captainUser = $user;
            }
        }

        if ($captainName && ! $captainUser) {
            $captainUser = User::query()->firstOrCreate(
                ['name' => $captainName, 'team_id' => $team->id],
                ['role' => '2'],
            );
        }

        if ($captainUser && ! $team->captain_id) {
            $team->captain_id = $captainUser->id;
            $team->save();
        }
    }

    private function makeShortname(string $name): string
    {
        $normalized = preg_replace('/[^A-Za-z0-9 ]+/', ' ', $name);
        $words = array_values(array_filter(preg_split('/\s+/', trim((string) $normalized))));

        if (count($words) > 1) {
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper($word[0]);
            }
            return strtoupper(substr($initials, 0, 6));
        }

        $compact = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) ($words[0] ?? '')));

        return substr($compact !== '' ? $compact : 'TEAM', 0, 6);
    }

    private function getOrCreateVenue(string $venueName): Venue
    {
        return Venue::query()->firstOrCreate(
            ['name' => $venueName],
            ['address' => 'Address unavailable', 'telephone' => null],
        );
    }

    private function getOrCreatePlayer(string $playerName, Team $team): User
    {
        return User::query()->firstOrCreate(
            ['name' => $playerName, 'team_id' => $team->id],
            ['role' => '1'],
        );
    }
}
