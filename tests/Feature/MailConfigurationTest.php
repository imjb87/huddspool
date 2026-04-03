<?php

namespace Tests\Feature;

use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;
use Tests\TestCase;

class MailConfigurationTest extends TestCase
{
    public function test_smtp_mailer_uses_a_default_timeout(): void
    {
        $this->assertSame(15, config('mail.mailers.smtp.timeout'));
    }

    public function test_brevo_mailer_can_build_the_api_transport(): void
    {
        config()->set('services.brevo.key', 'brevo-test-key');

        $transport = app(MailManager::class)->createSymfonyTransport(config('mail.mailers.brevo'));

        $this->assertInstanceOf(BrevoApiTransport::class, $transport);
    }
}
