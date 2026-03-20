<?php

namespace Tests\Unit;

use App\Support\PercentageFormatter;
use PHPUnit\Framework\TestCase;

class PercentageFormatterTest extends TestCase
{
    public function test_it_formats_whole_or_single_decimal_percentages(): void
    {
        $this->assertSame('50', PercentageFormatter::wholeOrSingleDecimal(50));
        $this->assertSame('50.5', PercentageFormatter::wholeOrSingleDecimal(50.5));
    }

    public function test_it_formats_trimmed_single_decimal_percentages(): void
    {
        $this->assertSame('50', PercentageFormatter::trimmedSingleDecimal(50));
        $this->assertSame('50.5', PercentageFormatter::trimmedSingleDecimal(50.5));
    }

    public function test_it_formats_percentages_from_ratios(): void
    {
        $this->assertSame('0', PercentageFormatter::ratio(0, 0));
        $this->assertSame('50', PercentageFormatter::ratio(2, 4));
        $this->assertSame('66.7', PercentageFormatter::ratio(2, 3));
    }
}
