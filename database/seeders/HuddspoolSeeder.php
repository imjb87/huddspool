<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
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

                foreach (Arr::get($rulesetData, 'sections', []) as $sectionData) {
                    $sectionName = Arr::get($sectionData, 'name', 'Section');

                    $section = Section::query()->firstOrNew([
                        'name' => $sectionName,
                        'season_id' => $season->id,
                        'ruleset_id' => $ruleset->id,
                    ]);
                    $section->save();

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
}
