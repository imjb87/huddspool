<?php

namespace App\Support;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutParticipant;
use App\Models\Venue;
use Illuminate\Support\Collection;

class KnockoutMatchVenueOptions
{
    /** @var array<int, Collection<int, array{lat:float,lng:float}>> */
    private array $participantLocationCache = [];

    /** @var array<int, Collection<int, int>> */
    private array $participantVenueCache = [];

    /** @var array<string, array<int, string>> */
    private array $venueOptionsCache = [];

    /**
     * @return array<int, string>
     */
    public function venueOptions(Knockout $knockout, ?int $homeParticipantId, ?int $awayParticipantId, ?int $currentVenueId): array
    {
        $cacheKey = json_encode([$knockout->id, $knockout->type?->value, $homeParticipantId, $awayParticipantId, $currentVenueId]);

        if (isset($this->venueOptionsCache[$cacheKey])) {
            return $this->venueOptionsCache[$cacheKey];
        }

        $point = $this->neutralPoint($knockout, $homeParticipantId, $awayParticipantId);
        $excludedVenueIds = collect();

        if (in_array($knockout->type, [KnockoutType::Singles, KnockoutType::Doubles], true)) {
            $excludedVenueIds = collect([$homeParticipantId, $awayParticipantId])
                ->filter()
                ->flatMap(fn (int $participantId) => $this->participantVenueIds($participantId))
                ->unique()
                ->values();
        }

        $venues = Venue::query()
            ->orderBy('name')
            ->get(['id', 'name', 'latitude', 'longitude']);

        if ($excludedVenueIds->isNotEmpty()) {
            $venues = $venues->reject(function (Venue $venue) use ($excludedVenueIds, $currentVenueId) {
                if ($currentVenueId && (int) $venue->id === (int) $currentVenueId) {
                    return false;
                }

                return $excludedVenueIds->contains((int) $venue->id);
            });
        }

        if ($point) {
            $venues = $venues
                ->filter(fn (Venue $venue): bool => $venue->latitude !== null && $venue->longitude !== null)
                ->map(function (Venue $venue) use ($point): Venue {
                    $distance = $this->distanceBetween($point, [
                        'lat' => (float) $venue->latitude,
                        'lng' => (float) $venue->longitude,
                    ]);

                    $venue->distance_from_neutral = $distance;

                    return $venue;
                })
                ->sortBy('distance_from_neutral')
                ->values();
        }

        $venues = $venues->take($point ? 25 : 20);

        if ($currentVenueId && ! $venues->contains('id', $currentVenueId)) {
            $currentVenue = Venue::find($currentVenueId);

            if ($currentVenue) {
                $venues->push($currentVenue);
            }
        }

        return $this->venueOptionsCache[$cacheKey] = $venues
            ->mapWithKeys(function (Venue $venue) use ($point): array {
                $label = $venue->name;

                if ($point && isset($venue->distance_from_neutral)) {
                    $label .= sprintf(' (%.1f km from neutral point)', $venue->distance_from_neutral);
                }

                return [$venue->id => $label];
            })
            ->toArray();
    }

    /**
     * @return array{lat:float,lng:float}|null
     */
    private function neutralPoint(Knockout $knockout, ?int $homeParticipantId, ?int $awayParticipantId): ?array
    {
        if (! in_array($knockout->type, [KnockoutType::Singles, KnockoutType::Doubles], true)) {
            return null;
        }

        $points = collect();

        foreach ([$homeParticipantId, $awayParticipantId] as $participantId) {
            $points = $points->merge($this->participantLocations($participantId));
        }

        if ($points->isEmpty()) {
            return null;
        }

        return [
            'lat' => (float) $points->avg('lat'),
            'lng' => (float) $points->avg('lng'),
        ];
    }

    /**
     * @return Collection<int, array{lat:float,lng:float}>
     */
    private function participantLocations(?int $participantId): Collection
    {
        if (! $participantId) {
            return collect();
        }

        if (array_key_exists($participantId, $this->participantLocationCache)) {
            return $this->participantLocationCache[$participantId];
        }

        $participant = KnockoutParticipant::query()
            ->with([
                'team.venue',
                'playerOne.team.venue',
                'playerTwo.team.venue',
            ])
            ->find($participantId);

        if (! $participant) {
            return $this->participantLocationCache[$participantId] = collect();
        }

        $locations = collect();
        $collectVenue = function (?Venue $venue) use (&$locations): void {
            if (! $venue || $venue->latitude === null || $venue->longitude === null) {
                return;
            }

            $locations->push([
                'lat' => (float) $venue->latitude,
                'lng' => (float) $venue->longitude,
            ]);
        };

        $collectVenue($participant->team?->venue);
        $collectVenue($participant->playerOne?->team?->venue);
        $collectVenue($participant->playerTwo?->team?->venue);

        return $this->participantLocationCache[$participantId] = $locations;
    }

    /**
     * @return Collection<int, int>
     */
    private function participantVenueIds(?int $participantId): Collection
    {
        if (! $participantId) {
            return collect();
        }

        if (array_key_exists($participantId, $this->participantVenueCache)) {
            return $this->participantVenueCache[$participantId];
        }

        $participant = KnockoutParticipant::query()
            ->with([
                'team',
                'playerOne.team',
                'playerTwo.team',
            ])
            ->find($participantId);

        if (! $participant) {
            return $this->participantVenueCache[$participantId] = collect();
        }

        return $this->participantVenueCache[$participantId] = collect([
            $participant->team?->venue_id,
            $participant->playerOne?->team?->venue_id,
            $participant->playerTwo?->team?->venue_id,
        ])
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * @param  array{lat:float,lng:float}  $from
     * @param  array{lat:float,lng:float}  $to
     */
    private function distanceBetween(array $from, array $to): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($from['lat']);
        $lonFrom = deg2rad($from['lng']);
        $latTo = deg2rad($to['lat']);
        $lonTo = deg2rad($to['lng']);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $earthRadius * $angle;
    }
}
