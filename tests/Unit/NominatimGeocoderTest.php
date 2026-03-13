<?php

namespace Tests\Unit;

use App\Support\Geocoding\NominatimGeocoder;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimGeocoderTest extends TestCase
{
    public function test_geocoder_uses_configured_endpoint_and_user_agent(): void
    {
        config([
            'services.nominatim.search_url' => 'https://example.com/search',
            'services.nominatim.user_agent' => 'Configured Geocoder',
        ]);

        Http::fake([
            'https://example.com/search*' => Http::response([
                ['lat' => '53.6458', 'lon' => '-1.7850'],
            ]),
        ]);

        $coordinates = NominatimGeocoder::geocode('Huddersfield');

        $this->assertSame([
            'lat' => 53.6458,
            'lng' => -1.785,
        ], $coordinates);

        Http::assertSent(function ($request) {
            return str_starts_with($request->url(), 'https://example.com/search')
                && $request->hasHeader('User-Agent', 'Configured Geocoder');
        });
    }
}
