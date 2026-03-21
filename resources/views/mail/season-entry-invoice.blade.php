<x-mail::message>
# Registration received

Your registration for **{{ $season->name }}** has been recorded.

Reference: **{{ $entry->reference }}**

Use this reference when making your bank transfer.

## Contact

- {{ $entry->contact_name }}
- {{ $entry->contact_email }}
@if (filled($entry->contact_telephone))
- {{ $entry->contact_telephone }}
@endif

## Venue

- {{ $entry->venue_name }}
@if (filled($entry->venue_address))
- {{ $entry->venue_address }}
@endif
@if (filled($entry->venue_telephone))
- {{ $entry->venue_telephone }}
@endif

## Line items

@foreach ($entry->teams as $team)
- {{ $team->team_name }} ({{ $team->contact_name }}{{ filled($team->contact_telephone) ? ', '.$team->contact_telephone : '' }}): Team registration (£{{ number_format((float) $team->price, 2) }})
@endforeach

@foreach ($entry->knockoutRegistrations as $knockoutEntry)
- {{ $knockoutEntry->knockout->name }}: {{ $knockoutEntry->entrant_name }} (£{{ number_format((float) $knockoutEntry->price, 2) }})
@endforeach

Total: **£{{ number_format((float) $entry->total_amount, 2) }}**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
