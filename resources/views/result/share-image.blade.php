<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    @php
        $homeScore = $result->home_score;
        $awayScore = $result->away_score;
        $isDraw = $homeScore === $awayScore;
        $homeWon = $homeScore > $awayScore;

        $homePillClasses = 'pill pill-draw';
        $awayPillClasses = 'pill pill-draw';

        if (! $isDraw) {
            $homePillClasses = $homeWon ? 'pill pill-win' : 'pill pill-loss';
            $awayPillClasses = $homeWon ? 'pill pill-loss' : 'pill pill-win';
        }
    @endphp
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }

        .canvas {
            width: 1200px;
            height: 630px;
            padding: 32px 42px;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.18), transparent 34%),
                radial-gradient(circle at top right, rgba(20, 83, 45, 0.22), transparent 28%),
                linear-gradient(180deg, #18181b 0%, #0f172a 54%, #052e16 100%);
        }

        .frame {
            width: 100%;
            height: 100%;
            padding: 10px 8px 20px;
        }

        .brand {
            margin-bottom: 24px;
        }

        .brand-logo {
            width: 72px;
            margin-bottom: 14px;
        }

        .brand-mark {
            margin: 0 0 6px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: rgba(228, 228, 231, 0.74);
        }

        .eyebrow {
            margin: 0;
            font-size: 21px;
            font-weight: 600;
            color: rgba(244, 244, 245, 0.9);
        }

        .title {
            margin: 10px 0 0;
            max-width: 760px;
            font-size: 42px;
            font-weight: 600;
            line-height: 1.02;
            letter-spacing: -0.03em;
        }

        .meta {
            margin-top: 14px;
            font-size: 18px;
            color: rgba(212, 212, 216, 0.8);
        }

        .scoreboard {
            margin-top: 28px;
        }

        .team-row {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }

        .team-row:last-child {
            margin-bottom: 0;
        }

        .team-copy,
        .team-score {
            display: table-cell;
            vertical-align: middle;
        }

        .team-score {
            width: 128px;
            text-align: right;
        }

        .team-label {
            margin: 0 0 6px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(161, 161, 170, 0.9);
        }

        .team-name {
            margin: 0;
            font-size: 35px;
            font-weight: 600;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 104px;
            height: 72px;
            padding: 0 24px;
            border-radius: 999px;
            font-size: 38px;
            font-weight: 800;
            line-height: 1;
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .pill-win {
            background: linear-gradient(135deg, #14532d 0%, #166534 52%, #15803d 100%);
        }

        .pill-loss {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 52%, #b91c1c 100%);
        }

        .pill-draw {
            background: linear-gradient(135deg, #3f3f46 0%, #52525b 52%, #71717a 100%);
        }

        .details {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 19px;
            color: rgba(244, 244, 245, 0.9);
        }

        .details-line {
            margin: 0 0 10px;
        }

        .details-line:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            display: inline-block;
            min-width: 78px;
            color: rgba(161, 161, 170, 0.92);
        }

        .footer {
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 17px;
            color: rgba(212, 212, 216, 0.74);
        }
    </style>
</head>

<body>
    <div class="canvas">
        <div class="frame">
            <div class="brand">
                @if ($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="" class="brand-logo">
                @endif
                <p class="brand-mark">Huddspool</p>
                <p class="eyebrow">{{ $ruleset?->name ?? 'League result' }}</p>
                <h1 class="title">{{ $section?->name ?? 'Archived section' }}</h1>
                <p class="meta">Result • {{ $fixture?->fixture_date?->format('l j F Y') ?? 'Date TBC' }}</p>
            </div>

            <div class="scoreboard">
                <div class="team-row">
                    <div class="team-copy">
                        <p class="team-label">Home</p>
                        <p class="team-name">{{ $result->home_team_name }}</p>
                    </div>
                    <div class="team-score">
                        <span class="{{ $homePillClasses }}">{{ $homeScore ?? '–' }}</span>
                    </div>
                </div>

                <div class="team-row">
                    <div class="team-copy">
                        <p class="team-label">Away</p>
                        <p class="team-name">{{ $result->away_team_name }}</p>
                    </div>
                    <div class="team-score">
                        <span class="{{ $awayPillClasses }}">{{ $awayScore ?? '–' }}</span>
                    </div>
                </div>
            </div>

            <div class="details">
                <p class="details-line">
                    <span class="detail-label">Venue</span>
                    {{ $venue?->name ?? 'Venue TBC' }}
                </p>
                <p class="details-line">
                    <span class="detail-label">Fixture</span>
                    {{ $result->home_team_name }} v {{ $result->away_team_name }}
                </p>
            </div>

            <div class="footer">
                View the full frame-by-frame result on {{ parse_url(config('app.frontend_url'), PHP_URL_HOST) ?: config('app.frontend_url') }}.
            </div>
        </div>
    </div>
</body>

</html>
