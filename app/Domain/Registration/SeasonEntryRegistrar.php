<?php

namespace App\Domain\Registration;

use App\Domain\Registration\Actions\RegisterSeasonEntry;
use App\Models\Knockout;
use App\Models\Season;
use App\Models\SeasonEntry;
use Illuminate\Support\Collection;

class SeasonEntryRegistrar
{
    /**
     * @param  array{name: string, email: string, telephone: string, notes: string}  $contact
     * @param  array{venue_name: ?string, venue_address: ?string, venue_telephone: ?string}  $venue
     * @param  array<int, array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}>  $teamRegistrations
     * @param  array<int, array{knockout_id: ?int, entrant_name: string}>  $knockoutRegistrations
     * @param  Collection<int, Knockout>  $knockouts
     */
    public function register(
        Season $season,
        array $contact,
        array $venue,
        array $teamRegistrations,
        array $knockoutRegistrations,
        Collection $knockouts,
    ): SeasonEntry {
        return app(RegisterSeasonEntry::class)->execute(
            season: $season,
            contact: $contact,
            venue: $venue,
            teamRegistrations: $teamRegistrations,
            knockoutRegistrations: $knockoutRegistrations,
            knockouts: $knockouts,
        );
    }
}
