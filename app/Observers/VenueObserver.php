<?php

namespace App\Observers;

use App\Models\Venue;
use App\Support\CompetitionCacheInvalidator;
use App\Support\Geocoding\NominatimGeocoder;
use App\Support\SiteSearch\SearchableRelationRefresher;

class VenueObserver
{
    public function saved(Venue $venue): void
    {
        (new CompetitionCacheInvalidator)->forgetForVenue($venue);
        app(SearchableRelationRefresher::class)->refreshVenueTeams($venue);
    }

    public function deleted(Venue $venue): void
    {
        (new CompetitionCacheInvalidator)->forgetForVenue($venue);
        app(SearchableRelationRefresher::class)->refreshVenueTeams($venue);
    }

    public function restored(Venue $venue): void
    {
        (new CompetitionCacheInvalidator)->forgetForVenue($venue);
        app(SearchableRelationRefresher::class)->refreshVenueTeams($venue);
    }

    public function saving(Venue $venue): void
    {
        if (! $this->shouldGeocode($venue)) {
            return;
        }

        $coordinates = NominatimGeocoder::geocode($venue->address);

        if (! $coordinates) {
            return;
        }

        $venue->latitude = $coordinates['lat'];
        $venue->longitude = $coordinates['lng'];
    }

    private function shouldGeocode(Venue $venue): bool
    {
        if (! $venue->address) {
            return false;
        }

        if ($venue->isDirty('address')) {
            return true;
        }

        return blank($venue->latitude) || blank($venue->longitude);
    }
}
