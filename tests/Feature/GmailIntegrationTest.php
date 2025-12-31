<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\GmailService;
use Tests\TestCase;

class GmailIntegrationTest extends TestCase
{
    public function test_gmail_service_can_be_instantiated(): void
    {
        // Set Gmail as disabled for testing
        config(['services.google.gmail_enabled' => false]);

        $service = app(GmailService::class);

        $this->assertInstanceOf(GmailService::class, $service);
    }

    public function test_gmail_service_can_validate_email_addresses(): void
    {
        config(['services.google.gmail_enabled' => false]);

        $service = app(GmailService::class);

        // Valid emails
        $this->assertTrue($service->validateEmailAddress('user@motac.gov.my'));
        $this->assertTrue($service->validateEmailAddress('test@example.com'));
        $this->assertTrue($service->validateEmailAddress('admin@ictserve.motac.gov.my'));

        // Invalid emails
        $this->assertFalse($service->validateEmailAddress('invalid-email'));
        $this->assertFalse($service->validateEmailAddress('user@'));
        $this->assertFalse($service->validateEmailAddress('@domain.com'));
        $this->assertFalse($service->validateEmailAddress(''));
    }

    public function test_gmail_transport_is_registered(): void
    {
        $mailManager = app('mail.manager');

        // Test that gmail transport can be created
        $transport = $mailManager->mailer('gmail');

        $this->assertNotNull($transport);
    }

    public function test_test_gmail_mail_can_be_instantiated(): void
    {
        $mailable = new \App\Mail\TestGmailMail('Test message');

        $this->assertInstanceOf(\App\Mail\TestGmailMail::class, $mailable);
        $this->assertEquals('Test message', $mailable->testMessage);
    }

    public function test_gmail_command_exists(): void
    {
        $this->artisan('gmail:test --help')
            ->assertExitCode(0);
    }
}
