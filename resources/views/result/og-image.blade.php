<svg width="1200" height="630" viewBox="0 0 1200 630" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="result-card-bg" x1="70" y1="40" x2="1130" y2="590" gradientUnits="userSpaceOnUse">
            <stop stop-color="#052e16" />
            <stop offset="0.52" stop-color="#14532d" />
            <stop offset="1" stop-color="#166534" />
        </linearGradient>
        <linearGradient id="result-card-header" x1="120" y1="70" x2="1080" y2="230" gradientUnits="userSpaceOnUse">
            <stop stop-color="rgba(255,255,255,0.06)" />
            <stop offset="1" stop-color="rgba(255,255,255,0.02)" />
        </linearGradient>
        <clipPath id="score-pill-clip">
            <rect x="860" y="286" width="216" height="96" rx="48" />
        </clipPath>
    </defs>

    <rect width="1200" height="630" fill="#031b0f" />
    <rect x="40" y="40" width="1120" height="550" rx="38" fill="url(#result-card-bg)" />
    <rect x="40" y="40" width="1120" height="550" rx="38" stroke="rgba(255,255,255,0.1)" />
    <rect x="88" y="76" width="1024" height="164" rx="30" fill="url(#result-card-header)" />

    <text x="120" y="132" fill="#bbf7d0" font-size="20" font-weight="700" letter-spacing="0.18em" font-family="Inter, Arial, sans-serif">
        {{ strtoupper(config('app.name', 'Huddersfield & District Tuesday Night Pool League')) }}
    </text>
    <text x="120" y="174" fill="white" font-size="40" font-weight="700" font-family="Inter, Arial, sans-serif">
        Match result
    </text>
    <text x="120" y="204" fill="#d1fae5" font-size="23" font-weight="500" font-family="Inter, Arial, sans-serif">
        {{ $section?->name ?? 'Archived section' }}
    </text>

    <rect x="88" y="248" width="1024" height="172" rx="34" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.1)" />

    <text x="120" y="324" fill="white" font-size="34" font-weight="700" font-family="Inter, Arial, sans-serif">
        {{ $homeTeamDisplay }}
    </text>
    <text x="120" y="374" fill="white" font-size="34" font-weight="700" font-family="Inter, Arial, sans-serif">
        {{ $awayTeamDisplay }}
    </text>

    <rect x="856" y="282" width="224" height="104" rx="52" fill="rgba(3,27,15,0.18)" />
    <g clip-path="url(#score-pill-clip)">
        <rect x="860" y="286" width="108" height="96" fill="{{ $homeFill }}" />
        <rect x="968" y="286" width="108" height="96" fill="{{ $awayFill }}" />
        <rect x="967" y="286" width="2" height="96" fill="rgba(3,27,15,0.18)" />
    </g>

    <text x="914" y="348" text-anchor="middle" fill="{{ $homeText }}" font-size="48" font-weight="800" font-family="Inter, Arial, sans-serif">
        {{ $result->home_score }}
    </text>
    <text x="1022" y="348" text-anchor="middle" fill="{{ $awayText }}" font-size="48" font-weight="800" font-family="Inter, Arial, sans-serif">
        {{ $result->away_score }}
    </text>

    <rect x="88" y="428" width="508" height="94" rx="24" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.1)" />
    <rect x="604" y="428" width="508" height="94" rx="24" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.1)" />

    <text x="120" y="462" fill="#bbf7d0" font-size="18" font-weight="700" letter-spacing="0.08em" font-family="Inter, Arial, sans-serif">
        DATE
    </text>
    <text x="120" y="502" fill="white" font-size="23" font-weight="600" font-family="Inter, Arial, sans-serif">
        {{ $fixture->fixture_date->format('D j M Y') }}
    </text>

    <text x="636" y="462" fill="#bbf7d0" font-size="18" font-weight="700" letter-spacing="0.08em" font-family="Inter, Arial, sans-serif">
        RULESET
    </text>
    <text x="636" y="502" fill="white" font-size="23" font-weight="600" font-family="Inter, Arial, sans-serif">
        {{ $rulesetDisplay }}
    </text>

    <text x="88" y="560" fill="#d1fae5" font-size="18" font-weight="500" font-family="Inter, Arial, sans-serif">
        {{ $seasonDisplay }}
    </text>

    @if ($submittedByDisplay)
        <text x="1112" y="560" text-anchor="end" fill="#bbf7d0" font-size="15" font-weight="500" font-family="Inter, Arial, sans-serif">
            {{ $submittedByDisplay }}
        </text>
    @endif
</svg>
