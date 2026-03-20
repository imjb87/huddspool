<?php

namespace App\Support;

use Illuminate\Support\Str;

class OrdinalFormatter
{
    public function for(int $number): string
    {
        $suffix = match (true) {
            $number % 100 >= 11 && $number % 100 <= 13 => 'th',
            $number % 10 === 1 => 'st',
            $number % 10 === 2 => 'nd',
            $number % 10 === 3 => 'rd',
            default => 'th',
        };

        return Str::of($number)->append($suffix)->toString();
    }
}
