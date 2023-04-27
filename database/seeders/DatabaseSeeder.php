<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'John Bell',
            'email' => 'john@thebiggerboat.co.uk',
            'is_admin' => true,
            'role' => 'captain',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        \App\Models\Team::factory()->create([
            'name' => 'Bye',
            'venue_id' => 0,
        ]);

        \App\Models\Season::factory()->create([
            'name' => '2021/22',
            'is_open' => true,
            'dates' => [
                '2021-09-01',
                '2021-09-08',
                '2021-09-15',
                '2021-09-22',
                '2021-09-29',
                '2021-10-06',
                '2021-10-13',
                '2021-10-20',
                '2021-10-27',
                '2021-11-03',
                '2021-11-10',
                '2021-11-17',
                '2021-11-24',
                '2021-12-01',
                '2021-12-08',
                '2021-12-15',
                '2021-12-22',
                '2021-12-29',
            ],
        ]);

        // create 5 venues
        $venues = \App\Models\Venue::factory()->count(5)
            ->create()
            ->each(function ($venue) {
                // create 2 teams for each venue
                \App\Models\Team::factory()->count(2)
                    ->create([
                        'venue_id' => $venue->id,
                    ])->each(function ($team) use ($venue) {
                        $team->name = $venue->name . ' ' . $team->id;
                        $team->save();
                        // create 5 players for each team, 4 with the role of player and 1 with the role of captain
                        \App\Models\User::factory()->count(5)->create()->each(function ($player) use ($team) {
                            $player->team_id = $team->id;
                            if ($player->id % 5 == 0) {
                                $player->role = 'captain';
                                $player->save();
                            } else {
                                $player->role = 'player';
                                $player->save();
                            }
                        });
                    });
            });

        // create 5 venues
        $venues = \App\Models\Venue::factory()->count(5)
            ->create()
            ->each(function ($venue) {
                // create 2 teams for each venue
                \App\Models\Team::factory()->count(2)
                    ->create([
                        'venue_id' => $venue->id,
                    ])->each(function ($team) use ($venue) {
                        $team->name = $venue->name . ' ' . $team->id;
                        $team->save();
                        // create 5 players for each team, 4 with the role of player and 1 with the role of captain
                        \App\Models\User::factory()->count(5)->create()->each(function ($player) use ($team) {
                            $player->team_id = $team->id;
                            if ($player->id % 5 == 0) {
                                $player->role = 'captain';
                                $player->save();
                            } else {
                                $player->role = 'player';
                                $player->save();
                            }
                        });
                    });
            });            


        // create 2 rulesets
        \App\Models\Ruleset::factory()->count(2)->create();

        $section = \App\Models\Section::create([
            'name' => 'Blackball Premier',
            'season_id' => 1,
            'ruleset_id' => 1,
        ]);
        
        $section->teams()->attach([0, 2, 4, 6, 10, 12, 14, 16, 18, 20]);
    }
}
