<?php

namespace App\Support;

use Carbon\Carbon;

class SeasonLabelFormatter
{
    public function for(?string $seasonName, mixed $seasonDates): string
    {
        return self::format($seasonName, $seasonDates);
    }

    public static function format(?string $seasonName, mixed $seasonDates): string
    {
        $dates = collect(is_string($seasonDates) ? json_decode($seasonDates, true) : $seasonDates);

        $firstDate = $dates
            ->flatten()
            ->filter()
            ->map(function ($value) {
                if ($value instanceof Carbon) {
                    return $value;
                }

                if (is_string($value)) {
                    try {
                        return Carbon::parse($value);
                    } catch (\Throwable) {
                        return null;
                    }
                }

                return null;
            })
            ->filter()
            ->sort()
            ->first();

        if ($firstDate) {
            return $firstDate->isoFormat('MMM YY');
        }

        if ($seasonName) {
            if (preg_match('/\d{4}/', $seasonName, $match)) {
                return Carbon::createFromDate((int) $match[0], 1, 1)->isoFormat('MMM YY');
            }

            if (preg_match('/\d{2}/', $seasonName, $match)) {
                $year = (int) $match[0];
                $year += $year >= 70 ? 1900 : 2000;

                return Carbon::createFromDate($year, 1, 1)->isoFormat('MMM YY');
            }

            return $seasonName;
        }

        return 'Unknown';
    }
}
