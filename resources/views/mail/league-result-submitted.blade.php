<div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 12px;">League result submitted</h1>

    <p style="margin: 0 0 12px;">
        {{ $result->submittedBy?->name ?? 'A team admin' }} submitted the result for
        <strong>{{ $result->home_team_name }} {{ (int) $result->home_score }}-{{ (int) $result->away_score }} {{ $result->away_team_name }}</strong>.
    </p>

    <p style="margin: 0 0 8px;"><strong>Section:</strong> {{ $result->fixture?->section?->name ?? 'Unknown section' }}</p>
    <p style="margin: 0 0 8px;"><strong>Ruleset:</strong> {{ $result->fixture?->section?->ruleset?->name ?? 'Unknown ruleset' }}</p>
    <p style="margin: 0 0 8px;"><strong>Fixture date:</strong> {{ $result->fixture?->fixture_date?->format('j F Y') ?? 'Unknown date' }}</p>
    <p style="margin: 0 0 16px;"><strong>Submitted:</strong> {{ $result->submitted_at?->format('j F Y, H:i') ?? now()->format('j F Y, H:i') }}</p>

    <p style="margin: 0;">
        <a href="{{ route('result.show', $result) }}" style="color: #166534; font-weight: 600;">
            View submitted result
        </a>
    </p>
</div>
