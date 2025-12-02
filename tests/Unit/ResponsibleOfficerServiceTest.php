<?php

namespace Tests\Unit;

use App\Models\LoanApplication;
use App\Services\ResponsibleOfficerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsibleOfficerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ResponsibleOfficerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResponsibleOfficerService;
    }

    public function test_handle_delegated_application_generates_token()
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'responsible_officer_email' => 'boss@motac.gov.my',
        ]);

        $this->service->handleDelegatedApplication($application);

        $this->assertNotNull($application->sponsorship_token);
        $this->assertNotNull($application->sponsorship_token_expires_at);
    }

    public function test_acknowledge_sponsorship_token()
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'sponsorship_token' => 'valid-token',
            'sponsorship_token_expires_at' => now()->addHour(),
        ]);

        $acknowledgedApp = $this->service->acknowledgeSponsorshipToken('valid-token');

        $this->assertNotNull($acknowledgedApp);
        $this->assertEquals($application->id, $acknowledgedApp->id);
        $this->assertNotNull($acknowledgedApp->responsible_officer_acknowledged_at);
    }

    public function test_acknowledge_invalid_token()
    {
        $result = $this->service->acknowledgeSponsorshipToken('invalid-token');
        $this->assertNull($result);
    }

    public function test_get_responsible_party()
    {
        $app1 = LoanApplication::factory()->create([
            'is_applicant_responsible' => true,
            'applicant_name' => 'John Doe',
        ]);

        $party1 = $this->service->getResponsibleParty($app1);
        $this->assertEquals('John Doe', $party1['name']);
        $this->assertEquals('applicant', $party1['type']);

        $app2 = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'responsible_officer_name' => 'Jane Boss',
        ]);

        $party2 = $this->service->getResponsibleParty($app2);
        $this->assertEquals('Jane Boss', $party2['name']);
        $this->assertEquals('officer', $party2['type']);
    }
}
