<?php

namespace Tests\Unit;

use App\Support\CoordinateFormatter;
use PHPUnit\Framework\TestCase;

class CoordinateFormatterTest extends TestCase
{
    public function test_it_formats_coordinates_to_seven_decimal_places(): void
    {
        $this->assertSame('53.6451235', CoordinateFormatter::sevenDecimalPlaces(53.64512345));
        $this->assertSame('-1.8012000', CoordinateFormatter::sevenDecimalPlaces(-1.8012));
        $this->assertNull(CoordinateFormatter::sevenDecimalPlaces(null));
    }
}
