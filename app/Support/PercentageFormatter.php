<?php

namespace App\Support;

class PercentageFormatter
{
    public static function wholeOrSingleDecimal(float|int $value): string
    {
        return fmod((float) $value, 1.0) === 0.0
            ? number_format((float) $value, 0)
            : number_format((float) $value, 1);
    }

    public static function trimmedSingleDecimal(float|int $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1), '0'), '.');
    }

    public static function ratio(int|float $part, int|float $whole): string
    {
        if ((float) $whole <= 0.0) {
            return '0';
        }

        return self::wholeOrSingleDecimal(((float) $part / (float) $whole) * 100);
    }
}
