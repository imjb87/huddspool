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
            padding: 52px 56px 44px;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.12), transparent 32%),
                linear-gradient(180deg, #18181b 0%, #111827 58%, #052e16 100%);
        }

        .frame {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 38px;
        }

        .brand-mark {
            margin: 0 0 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(228, 228, 231, 0.74);
        }

        .title {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: rgba(244, 244, 245, 0.9);
        }

        .meta {
            margin: 8px 0 0;
            font-size: 16px;
            color: rgba(212, 212, 216, 0.8);
        }

        .ruleset {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: rgba(228, 228, 231, 0.84);
            text-align: right;
        }

        .scoreboard {
            width: 100%;
            margin-top: 8px;
        }

        .team-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .team-row:first-child {
            border-top: 0;
            padding-top: 0;
        }

        .team-copy {
            min-width: 0;
            flex: 1;
        }

        .team-score {
            flex-shrink: 0;
        }

        .team-name {
            margin: 0;
            font-size: 34px;
            font-weight: 600;
            line-height: 1.08;
            letter-spacing: -0.03em;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            height: 56px;
            padding: 0 22px;
            border-radius: 999px;
            font-size: 30px;
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
            display: flex;
            align-items: center;
            gap: 28px;
            margin-top: auto;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 16px;
            color: rgba(244, 244, 245, 0.9);
        }

        .details-line {
            margin: 0;
        }

        .detail-label {
            display: inline-block;
            margin-right: 8px;
            color: rgba(161, 161, 170, 0.92);
        }
    </style>
</head>

<body>
    <div class="canvas">
        <div class="frame">
            <header class="brand">
                <div>
                    <p class="brand-mark">Huddspool</p>
                    <h1 class="title">Result</h1>
                    <p class="meta">{{ $section?->name ?? 'Archived section' }}</p>
                </div>

                <p class="ruleset">{{ $ruleset?->name ?? 'League result' }}</p>
            </header>

            <div class="scoreboard">
                <div class="team-row">
                    <div class="team-copy">
                        <p class="team-name">{{ $result->home_team_name }}</p>
                    </div>
                    <div class="team-score">
                        <span class="{{ $homePillClasses }}">{{ $homeScore ?? '–' }}</span>
                    </div>
                </div>

                <div class="team-row">
                    <div class="team-copy">
                        <p class="team-name">{{ $result->away_team_name }}</p>
                    </div>
                    <div class="team-score">
                        <span class="{{ $awayPillClasses }}">{{ $awayScore ?? '–' }}</span>
                    </div>
                </div>
            </div>

            <div class="details">
                <p class="details-line">
                    <span class="detail-label">Date</span> {{ $fixture?->fixture_date?->format('l j F Y') ?? 'Date TBC' }}
                </p>
                <p class="details-line">
                    <span class="detail-label">Venue</span> {{ $venue?->name ?? 'Venue TBC' }}
                </p>
            </div>
        </div>
    </div>
</body>

</html>
