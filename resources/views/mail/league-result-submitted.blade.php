<x-mail::message>
# League result submitted

{{ $result->submittedBy?->name ?? 'A team admin' }} submitted the result for **{{ $result->home_team_name }} {{ (int) $result->home_score }}-{{ (int) $result->away_score }} {{ $result->away_team_name }}**.

**Section:** {{ $result->fixture?->section?->name ?? 'Unknown section' }}

**Ruleset:** {{ $result->fixture?->section?->ruleset?->name ?? 'Unknown ruleset' }}

**Fixture date:** {{ $result->fixture?->fixture_date?->format('j F Y') ?? 'Unknown date' }}

**Submitted:** {{ $result->submitted_at?->format('j F Y, H:i') ?? now()->format('j F Y, H:i') }}

<x-mail::button :url="route('result.show', $result)">
View submitted result
</x-mail::button>
</x-mail::message>
