<?php

namespace App\Support;

class KnockoutParticipantSheetData
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function __construct(
        public readonly array $rows,
    ) {
    }
}

class KnockoutParticipantSheetParser
{
    public static function parse(?string $payload): KnockoutParticipantSheetData
    {
        $payload = trim((string) $payload);

        if ($payload === '') {
            return new KnockoutParticipantSheetData([]);
        }

        $rows = [];

        foreach (preg_split("/\r\n|\n|\r/", $payload) as $line) {
            if ($line === null) {
                continue;
            }

            $columns = array_map('trim', str_getcsv($line));

            if (empty(array_filter($columns, fn (?string $value) => $value !== null && $value !== ''))) {
                continue;
            }

            $seed = null;
            $startIndex = 0;

            if (isset($columns[0]) && preg_match('/^\d+$/', $columns[0])) {
                $seed = (int) $columns[0];
                $startIndex = 1;
            }

            $values = array_slice($columns, $startIndex);
            $values = array_values(array_filter($values, fn (?string $value) => $value !== null && $value !== ''));

            if ($values === []) {
                continue;
            }

            $rows[] = [
                'seed' => $seed,
                'primary' => $values[0] ?? null,
                'secondary' => $values[1] ?? null,
                'raw' => $values,
            ];
        }

        return new KnockoutParticipantSheetData($rows);
    }
}
