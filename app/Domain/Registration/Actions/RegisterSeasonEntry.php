<?php

namespace App\Domain\Registration\Actions;

use App\Models\Knockout;
use App\Models\Season;
use App\Models\SeasonEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class RegisterSeasonEntry
{
    /**
     * @param  array{name: string, email: string, telephone: string, notes: string}  $contact
     * @param  array{venue_name: ?string, venue_address: ?string, venue_telephone: ?string}  $venue
     * @param  array<int, array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}>  $teamRegistrations
     * @param  array<int, array{knockout_id: ?int, entrant_name: string}>  $knockoutRegistrations
     * @param  Collection<int, Knockout>  $knockouts
     */
    public function execute(
        Season $season,
        array $contact,
        array $venue,
        array $teamRegistrations,
        array $knockoutRegistrations,
        Collection $knockouts,
    ): SeasonEntry {
        return DB::transaction(function () use ($season, $contact, $venue, $teamRegistrations, $knockoutRegistrations, $knockouts): SeasonEntry {
            $entry = SeasonEntry::query()->create([
                'season_id' => $season->id,
                'contact_name' => trim($contact['name']),
                'contact_email' => trim($contact['email']),
                'contact_telephone' => trim((string) $contact['telephone']) ?: null,
                'existing_venue_id' => null,
                'venue_name' => $venue['venue_name'],
                'venue_address' => $venue['venue_address'],
                'venue_telephone' => $venue['venue_telephone'],
                'notes' => trim((string) $contact['notes']) ?: null,
                'total_amount' => 0,
            ]);

            $totalAmount = 0.0;

            foreach ($teamRegistrations as $registration) {
                $entry->teams()->create([
                    'existing_team_id' => null,
                    'ruleset_id' => $registration['ruleset_id'],
                    'second_ruleset_id' => $registration['second_ruleset_id'],
                    'existing_venue_id' => null,
                    'team_name' => $registration['team_name'],
                    'contact_name' => $registration['contact_name'] ?: null,
                    'contact_telephone' => $registration['contact_telephone'] ?: null,
                    'venue_name' => $venue['venue_name'],
                    'venue_address' => $venue['venue_address'],
                    'venue_telephone' => $venue['venue_telephone'],
                    'price' => (float) $season->team_entry_fee,
                ]);

                $totalAmount += (float) $season->team_entry_fee;
            }

            $knockoutsById = $knockouts->keyBy('id');

            foreach ($knockoutRegistrations as $registration) {
                $knockout = $knockoutsById->get((int) ($registration['knockout_id'] ?? 0));

                if (! $knockout) {
                    continue;
                }

                $entry->knockoutRegistrations()->create([
                    'knockout_id' => $knockout->id,
                    'season_team_entry_id' => null,
                    'existing_team_id' => null,
                    'entrant_name' => $registration['entrant_name'],
                    'player_one_name' => null,
                    'player_two_name' => null,
                    'price' => (float) $knockout->entry_fee,
                ]);

                $totalAmount += (float) $knockout->entry_fee;
            }

            $entry->update([
                'total_amount' => $totalAmount,
            ]);

            return $entry->load([
                'season',
                'existingVenue',
                'teams.ruleset',
                'teams.secondRuleset',
                'knockoutRegistrations.knockout',
            ]);
        });
    }
}
