<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadsPageTest extends TestCase
{
    public function test_downloads_page_lists_the_score_card_and_registration_forms(): void
    {
        $this->assertSame('/download-centre', parse_url(route('downloads.index'), PHP_URL_PATH));

        $this->get(route('downloads.index'))
            ->assertOk()
            ->assertSee('data-downloads-page', false)
            ->assertSeeText('Downloads')
            ->assertSeeText('Score card')
            ->assertSeeText('Download scorecard')
            ->assertSee('class="ui-button-primary mt-4 shrink-0 sm:mt-0"', false)
            ->assertSee('href="'.asset('downloads/scorecard.pdf').'"', false)
            ->assertSeeText('December 2026 registration forms')
            ->assertSeeText('Download registration forms')
            ->assertSee('href="'.asset('downloads/registration-forms-december-2026.pdf').'"', false)
            ->assertSee('download="registration-forms-december-2026.pdf"', false)
            ->assertSee('data-download-link="registration-forms-december-2026"', false);
    }

    public function test_scorecard_pdf_is_available_as_the_supplied_file(): void
    {
        $path = public_path('downloads/scorecard.pdf');

        $this->assertFileExists($path);
        $this->assertSame('%PDF-', file_get_contents($path, false, null, 0, 5));
        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page\b/', file_get_contents($path)));
    }

    public function test_december_2026_registration_forms_are_available_as_the_supplied_file(): void
    {
        $path = public_path('downloads/registration-forms-december-2026.pdf');

        $this->assertFileExists($path);
        $this->assertSame('%PDF-', file_get_contents($path, false, null, 0, 5));
        $this->assertSame(6, preg_match_all('/\/Type\s*\/Page\b/', file_get_contents($path)));
    }
}
