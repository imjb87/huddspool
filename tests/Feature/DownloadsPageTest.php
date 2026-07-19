<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadsPageTest extends TestCase
{
    public function test_downloads_page_lists_the_score_card_without_registration_sheets(): void
    {
        $this->get(route('downloads.index'))
            ->assertOk()
            ->assertSee('data-downloads-page', false)
            ->assertSeeText('Downloads')
            ->assertSeeText('Score card')
            ->assertSeeText('Download scorecard.pdf')
            ->assertSee('href="'.asset('downloads/scorecard.pdf').'"', false)
            ->assertDontSeeText('Registration sheets')
            ->assertDontSee('data-download-link="registration-sheets"', false);
    }

    public function test_scorecard_pdf_is_available_as_the_supplied_file(): void
    {
        $path = public_path('downloads/scorecard.pdf');

        $this->assertFileExists($path);
        $this->assertSame('%PDF-', file_get_contents($path, false, null, 0, 5));
        $this->assertSame(2, preg_match_all('/\/Type\s*\/Page\b/', file_get_contents($path)));
    }
}
