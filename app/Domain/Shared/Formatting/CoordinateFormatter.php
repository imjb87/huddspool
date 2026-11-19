<?php

namespace App\Domain\Shared\Formatting;

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
