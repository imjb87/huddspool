<?php

namespace App\Support;

class SectionSheetData
{
    /**
     * @param  array<int, array<string, mixed>>  $teams
     */
    public function __construct(
        public readonly ?string $sectionName,
        public readonly array $teams,
    ) {
    }
}

class SectionSheetParser
{
    public static function parse(?string $payload): SectionSheetData
    {
        $payload = trim((string) $payload);

        if ($payload === '') {
            return new SectionSheetData(null, []);
        }

        $rows = array_map(
            fn (string $line): array => array_map('trim', str_getcsv($line)),
            preg_split("/\r\n|\n|\r/", $payload) ?: []
        );

        $sectionName = collect($rows)
            ->map(fn (array $columns) => trim($columns[0] ?? ''))
            ->first(fn (?string $value) => $value !== '' && ! preg_match('/^\d+$/', $value));

        $teams = [];
        $sort = 1;

        foreach ($rows as $columns) {
            $position = trim($columns[0] ?? '');
            $name = trim($columns[1] ?? '');

            if ($position === '' || $name === '') {
                continue;
            }

            if (! preg_match('/^\d+$/', $position)) {
                continue;
            }

            $teams[] = [
                'label' => $position,
                'name' => $name,
                'sort' => $sort++,
            ];
        }

        return new SectionSheetData($sectionName, $teams);
    }
}
