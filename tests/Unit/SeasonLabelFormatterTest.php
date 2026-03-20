<?php

namespace Tests\Unit;

use App\Support\SeasonLabelFormatter;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SeasonLabelFormatterTest extends TestCase
{
    public function test_it_formats_label_from_dates(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for('Winter Season', [
            ['2025-08-12', '2025-09-02'],
        ]);

        $this->assertSame('Aug 25', $label);
    }

    public function test_it_falls_back_to_four_digit_year_in_name(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for('August 2023', []);

        $this->assertSame('Jan 23', $label);
    }

    public function test_it_falls_back_to_two_digit_year_in_name(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for('Winter 98', []);

        $this->assertSame('Jan 98', $label);
    }

    public function test_it_returns_name_when_no_date_can_be_derived(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for('Knockout Showcase', []);

        $this->assertSame('Knockout Showcase', $label);
    }

    public function test_it_returns_unknown_when_no_name_or_dates_exist(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for(null, []);

        $this->assertSame('Unknown', $label);
    }

    public function test_it_supports_carbon_instances_in_dates(): void
    {
        $formatter = new SeasonLabelFormatter;

        $label = $formatter->for(null, [[Carbon::create(2024, 2, 6)]]);

        $this->assertSame('Feb 24', $label);
    }
}
