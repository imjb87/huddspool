<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #06120b;
            color: #f8fafc;
        }

        .canvas {
            width: 1200px;
            height: 630px;
            padding: 44px 54px;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.26), transparent 34%),
                radial-gradient(circle at top right, rgba(21, 128, 61, 0.24), transparent 28%),
                linear-gradient(135deg, #052e16 0%, #14532d 56%, #166534 100%);
        }

        .frame {
            width: 100%;
            height: 100%;
            padding: 34px 38px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 28px;
            background: rgba(3, 7, 18, 0.24);
        }

        .brand {
            margin-bottom: 34px;
        }

        .brand-logo {
            width: 100px;
            margin-bottom: 18px;
        }

        .brand-mark {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(240, 253, 244, 0.84);
        }

        .title {
            margin: 0;
            font-size: 44px;
            font-weight: 700;
            line-height: 1.08;
        }

        .meta {
            margin-top: 10px;
            font-size: 24px;
            color: rgba(220, 252, 231, 0.88);
        }

        .scoreboard {
            margin-top: 44px;
            border-radius: 26px;
            background: rgba(3, 7, 18, 0.28);
            overflow: hidden;
        }

        .teams {
            width: 100%;
            border-collapse: collapse;
        }

        .teams td {
            padding: 26px 28px;
            vertical-align: middle;
        }

        .team-name {
            font-size: 44px;
            font-weight: 700;
            line-height: 1.05;
        }

        .score {
            width: 210px;
            text-align: center;
            font-size: 88px;
            font-weight: 800;
            letter-spacing: -0.04em;
            background: rgba(21, 128, 61, 0.25);
        }

        .divider {
            width: 1px;
            background: rgba(255, 255, 255, 0.12);
        }

        .details {
            margin-top: 32px;
            font-size: 24px;
            color: rgba(240, 253, 244, 0.92);
        }

        .detail-label {
            color: rgba(187, 247, 208, 0.74);
        }

        .footer {
            margin-top: 30px;
            font-size: 20px;
            color: rgba(220, 252, 231, 0.76);
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
                <h1 class="title">{{ $ruleset?->name ?? 'League result' }}</h1>
                <p class="meta">{{ $section?->name ?? 'Archived section' }}</p>
            </div>

            <div class="scoreboard">
                <table class="teams">
                    <tr>
                        <td>
                            <div class="team-name">{{ $result->home_team_name }}</div>
                        </td>
                        <td class="score">{{ $result->home_score ?? '–' }}</td>
                        <td class="divider"></td>
                        <td class="score">{{ $result->away_score ?? '–' }}</td>
                        <td>
                            <div class="team-name" style="text-align: right;">{{ $result->away_team_name }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="details">
                <span class="detail-label">Date:</span> {{ $fixture?->fixture_date?->format('l j F Y') ?? 'Date TBC' }}
                &nbsp;&nbsp;&nbsp;
                <span class="detail-label">Venue:</span> {{ $venue?->name ?? 'Venue TBC' }}
            </div>

            <div class="footer">
                View the full frame-by-frame result on {{ parse_url(config('app.frontend_url'), PHP_URL_HOST) ?: config('app.frontend_url') }}.
            </div>
        </div>
    </div>
</body>

</html>
