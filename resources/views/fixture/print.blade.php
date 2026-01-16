<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $section->name }} Fixtures</title>
    <style>
        @page { margin: 18px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; }
        .page { padding: 6px; }
        .header { text-align: center; margin-bottom: 16px; }
        .logo { width: 108px; margin: 0 auto 12px; display: block; }
        h1 { font-family: DejaVu Serif, "Times New Roman", serif; font-size: 32px; font-weight: 700; margin: 0 0 4px; letter-spacing: -0.75px; }
        .season { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; margin: 0; }
        .table-wrap { border: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #166534; color: #ffffff; font-size: 9px; padding: 4px 8px; }
        tbody td { border: 1px solid #e5e7eb; padding: 4px 8px; font-size: 9px; text-align: center; }
        .index-cell { width: 24px; }
        .team-cell { width: 128px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: DejaVu Sans, Arial, sans-serif; }
        .date-cell { width: 24px; }
    </style>
</head>
<body>
    <section class="page">
        <div class="header">
            <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Huddersfield Pool Logo" />
            <h1>{{ $section->name }}</h1>
            <p class="season">{{ $section->season->name }}</p>
        </div>
        <div class="table-wrap">
            @php
                $headerDates = $dates;
                if (empty($headerDates) && !empty($grid)) {
                    $firstRow = reset($grid);
                    $headerDates = is_array($firstRow) ? array_keys($firstRow) : [];
                }
            @endphp
            <table>
                <thead>
                    <tr>
                        <th class="index-cell"></th>
                        <th class="team-cell"></th>
                        @foreach ($headerDates as $date)
                            <th class="date-cell">{{ $date }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grid as $team => $row)
                        <tr>
                            <td class="index-cell">{{ $loop->iteration == 10 ? 0 : $loop->iteration }}</td>
                            <td class="team-cell">{{ $team }}</td>
                            @foreach ($headerDates as $date)
                                <td>{{ $row[$date] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
