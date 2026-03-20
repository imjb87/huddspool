<?php

namespace App\Support;

class CoordinateFormatter
{
    public static function sevenDecimalPlaces(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return number_format((float) $value, 7);
    }
}
