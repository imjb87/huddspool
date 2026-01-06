<?php

namespace App\Observers;

use App\Models\Venue;
use App\Support\Geocoding\NominatimGeocoder;

class VenueObserver
{
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
