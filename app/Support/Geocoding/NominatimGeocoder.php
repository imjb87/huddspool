<?php

namespace App\Support\Geocoding;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NominatimGeocoder
{
    public static function geocode(?string $address): ?array
    {
        if (! $address) {
            return null;
        }

        $coordinates = self::query([
            'q' => $address,
        ]);

        if ($coordinates) {
            return $coordinates;
        }

        if ($postcode = self::extractUkPostcode($address)) {
            return self::query([
                'postalcode' => $postcode,
                'countrycodes' => 'gb',
            ]);
        }

        return null;
    }

    protected static function query(array $params): ?array
    {
        $query = array_merge([
            'format' => 'json',
            'limit' => 1,
        ], $params);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'HuddsPool') . ' geocoder',
                ])
                ->get('https://nominatim.openstreetmap.org/search', $query);

            if (! $response->successful()) {
                Log::warning('Geocoding request failed', [
                    'params' => $query,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            if (empty($data[0]['lat']) || empty($data[0]['lon'])) {
                return null;
            }

            return [
                'lat' => (float) $data[0]['lat'],
                'lng' => (float) $data[0]['lon'],
            ];
        } catch (\Throwable $exception) {
            Log::warning('Failed to geocode address', [
                'params' => $query,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    protected static function extractUkPostcode(?string $address): ?string
    {
        if (! $address) {
            return null;
        }

        if (preg_match('/([A-Z]{1,2}\\d[A-Z\\d]?\\s?\\d[A-Z]{2})/i', $address, $matches)) {
            return strtoupper(trim($matches[1]));
        }

        return null;
    }
}
