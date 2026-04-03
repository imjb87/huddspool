<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailConfigurationTest extends TestCase
{
    public function test_smtp_mailer_uses_a_default_timeout(): void
    {
        $this->assertSame(15, config('mail.mailers.smtp.timeout'));
    }
}
