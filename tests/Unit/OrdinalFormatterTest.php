<?php

namespace Tests\Unit;

use App\Support\OrdinalFormatter;
use PHPUnit\Framework\TestCase;

class OrdinalFormatterTest extends TestCase
{
    public function test_it_formats_ordinal_numbers(): void
    {
        $formatter = new OrdinalFormatter;

        $this->assertSame('1st', $formatter->for(1));
        $this->assertSame('2nd', $formatter->for(2));
        $this->assertSame('3rd', $formatter->for(3));
        $this->assertSame('4th', $formatter->for(4));
        $this->assertSame('11th', $formatter->for(11));
        $this->assertSame('12th', $formatter->for(12));
        $this->assertSame('13th', $formatter->for(13));
        $this->assertSame('21st', $formatter->for(21));
        $this->assertSame('22nd', $formatter->for(22));
        $this->assertSame('23rd', $formatter->for(23));
    }
}
