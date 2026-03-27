<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $season->name }} Invoice</title>
    <style>
        @page { margin: 24px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 12px; margin: 0; }
        h1, h2, p { margin: 0; }
        .page { padding: 8px; }
        .header { width: 100%; margin-bottom: 24px; }
        .header td { vertical-align: top; }
        .logo-cell { width: 76px; }
        .logo { width: 60px; }
        .eyebrow { color: #166534; font-size: 10px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 6px; }
        .heading { font-family: DejaVu Serif, "Times New Roman", serif; font-size: 26px; font-weight: 700; line-height: 1.1; margin-bottom: 6px; }
        .muted { color: #6b7280; }
        .header-meta { font-size: 11px; }
        .reference-box { width: 228px; border: 1px solid #bbf7d0; background: #f0fdf4; padding: 12px 14px; }
        .reference-label { color: #166534; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .reference-value { color: #14532d; font-size: 20px; font-weight: 700; margin-top: 4px; }
        .status-row { margin-top: 10px; font-size: 11px; }
        .info-grid { width: 100%; margin-bottom: 26px; border-collapse: collapse; }
        .info-grid td { width: 50%; vertical-align: top; padding-right: 20px; }
        .section-title { color: #166534; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
        .info-card { border-top: 2px solid #166534; padding-top: 10px; }
        .lines p { margin-bottom: 4px; }
        .item-section { margin-top: 22px; }
        .table { width: 100%; border-collapse: collapse; }
        .table thead th { border-bottom: 1px solid #d1d5db; color: #6b7280; font-size: 10px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; padding: 0 0 8px; text-align: left; }
        .table thead th:last-child { text-align: right; }
        .table td { border-top: 1px solid #e5e7eb; padding: 12px 0; vertical-align: top; }
        .table td:last-child { text-align: right; white-space: nowrap; }
        .name { font-weight: 700; }
        .meta { color: #6b7280; font-size: 11px; margin-top: 4px; }
        .empty { color: #6b7280; }
        .total-row td { border-top: 2px solid #111827; font-weight: 700; padding-top: 14px; }
    </style>
</head>
<body>
    <section class="page">
        <table class="header">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="logo-cell">
                                <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Huddersfield Pool Logo" />
                            </td>
                            <td>
                                <p class="eyebrow">Season registration invoice</p>
                                <h1 class="heading">{{ $season->name }}</h1>
                                <p class="header-meta muted">Submitted {{ $entry->created_at?->format('j M Y H:i') }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 240px;">
                    <div class="reference-box">
                        <p class="reference-label">Reference number</p>
                        <p class="reference-value">{{ $entry->reference }}</p>
                        <p class="status-row muted">
                            Payment status: {{ $entry->paymentStatusLabel() }}
                            @if ($entry->payment_completed_at)
                                (paid {{ $entry->payment_completed_at->format('j M Y H:i') }})
                            @endif
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-grid">
            <tr>
                <td>
                    <h2 class="section-title">Contact</h2>
                    <div class="info-card lines">
                        <p>{{ $entry->contact_name }}</p>
                        <p>{{ $entry->contact_email }}</p>
                        @if (filled($entry->contact_telephone))
                            <p>{{ $entry->contact_telephone }}</p>
                        @endif
                    </div>
                </td>
                <td>
                    <h2 class="section-title">Venue</h2>
                    <div class="info-card lines">
                        <p>{{ $entry->venue_name }}</p>
                        @if (filled($entry->venue_address))
                            <p>{{ $entry->venue_address }}</p>
                        @endif
                        @if (filled($entry->venue_telephone))
                            <p>{{ $entry->venue_telephone }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="item-section">
            <h2 class="section-title">Teams</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Entry</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entry->teams as $team)
                        <tr>
                            <td>
                                <p class="name">{{ $team->team_name }}</p>
                                <p class="meta">
                                    {{ $team->contact_name }}
                                    @if (filled($team->contact_telephone))
                                        · {{ $team->contact_telephone }}
                                    @endif
                                </p>
                                <p class="meta">
                                    {{ $team->ruleset?->name }}
                                    @if ($team->secondRuleset)
                                        / {{ $team->secondRuleset->name }}
                                    @endif
                                </p>
                            </td>
                            <td>£{{ number_format((float) $team->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td><p class="empty">No teams added.</p></td>
                            <td></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="item-section">
            <h2 class="section-title">Knockouts</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Entry</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entry->knockoutRegistrations as $knockoutEntry)
                        <tr>
                            <td>
                                <p class="name">{{ $knockoutEntry->knockout->name }}</p>
                                <p class="meta">{{ $knockoutEntry->entrant_name }}</p>
                            </td>
                            <td>£{{ number_format((float) $knockoutEntry->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td><p class="empty">No knockouts added.</p></td>
                            <td></td>
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td>Total</td>
                        <td>£{{ number_format((float) $entry->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
