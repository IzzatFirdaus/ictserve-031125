<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\ResponsibleOfficerServiceInterface;
use App\Models\LoanApplication;
use App\Services\ResponsibleOfficerService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for ResponsibleOfficerService
 *
 * @see Requirements 25.1, 25.2, 25.3, 25.4
 */
class ResponsibleOfficerServiceTest extends TestCase
{
    protected ResponsibleOfficerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResponsibleOfficerService;
    }

    #[Test]
    public function service_implements_interface(): void
    {
        $this->assertInstanceOf(ResponsibleOfficerServiceInterface::class, $this->service);
    }

    #[Test]
    public function handle_delegated_application_generates_token(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'responsible_officer_email' => 'boss@motac.gov.my',
        ]);

        $this->service->handleDelegatedApplication($application);

        $this->assertNotNull($application->sponsorship_token);
        $this->assertNotNull($application->sponsorship_token_expires_at);
    }

    #[Test]
    public function acknowledge_sponsorship_token(): void
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

    #[Test]
    public function acknowledge_invalid_token(): void
    {
        $result = $this->service->acknowledgeSponsorshipToken('invalid-token');
        $this->assertNull($result);
    }

    #[Test]
    public function get_responsible_party(): void
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

    /**
     * Test setResponsibleOfficer stores officer data correctly
     *
     * @see Requirements 25.4 - Responsible Officer data storage
     */
    #[Test]
    public function set_responsible_officer_stores_data(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => true,
        ]);

        $officerData = [
            'name' => 'Ahmad bin Abdullah',
            'grade' => 'N41',
            'phone' => '0123456789',
            'email' => 'ahmad@motac.gov.my',
            'position' => 'Ketua Unit',
        ];

        $this->service->setResponsibleOfficer($application, $officerData);

        $application->refresh();

        $this->assertFalse($application->is_applicant_responsible);
        $this->assertEquals('Ahmad bin Abdullah', $application->responsible_officer_name);
        $this->assertEquals('N41', $application->responsible_officer_grade);
        $this->assertEquals('0123456789', $application->responsible_officer_phone);
        $this->assertEquals('ahmad@motac.gov.my', $application->responsible_officer_email);
        $this->assertEquals('Ketua Unit', $application->responsible_officer_position);
    }

    /**
     * Test copyApplicantAsResponsibleOfficer auto-populates data
     *
     * @see Requirements 25.3 - Auto-populate from Applicant data
     */
    #[Test]
    public function copy_applicant_as_responsible_officer(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'applicant_name' => 'Siti binti Hassan',
            'applicant_email' => 'siti@motac.gov.my',
            'applicant_phone' => '0198765432',
            'applicant_grade' => 'N29',
            'applicant_position' => 'Pembantu Tadbir',
        ]);

        $this->service->copyApplicantAsResponsibleOfficer($application);

        $application->refresh();

        $this->assertTrue($application->is_applicant_responsible);
        $this->assertEquals('Siti binti Hassan', $application->responsible_officer_name);
        $this->assertEquals('siti@motac.gov.my', $application->responsible_officer_email);
        $this->assertEquals('0198765432', $application->responsible_officer_phone);
        $this->assertEquals('N29', $application->responsible_officer_grade);
    }

    /**
     * Test getResponsibleOfficerDetails returns correct data
     *
     * @see Requirements 25.5 - Display Responsible Officer information
     */
    #[Test]
    public function get_responsible_officer_details_when_applicant(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => true,
            'applicant_name' => 'Ali bin Abu',
            'applicant_email' => 'ali@motac.gov.my',
            'applicant_phone' => '0111234567',
            'applicant_grade' => 'N32',
            'applicant_position' => 'Pegawai Teknologi Maklumat',
        ]);

        $details = $this->service->getResponsibleOfficerDetails($application);

        $this->assertEquals('Ali bin Abu', $details['name']);
        $this->assertEquals('ali@motac.gov.my', $details['email']);
        $this->assertEquals('0111234567', $details['phone']);
        $this->assertEquals('N32', $details['grade']);
        $this->assertEquals('applicant', $details['type']);
    }

    #[Test]
    public function get_responsible_officer_details_when_different_officer(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'responsible_officer_name' => 'Fatimah binti Yusof',
            'responsible_officer_email' => 'fatimah@motac.gov.my',
            'responsible_officer_phone' => '0129876543',
            'responsible_officer_grade' => 'N44',
            'responsible_officer_position' => 'Pengarah',
        ]);

        $details = $this->service->getResponsibleOfficerDetails($application);

        $this->assertEquals('Fatimah binti Yusof', $details['name']);
        $this->assertEquals('fatimah@motac.gov.my', $details['email']);
        $this->assertEquals('0129876543', $details['phone']);
        $this->assertEquals('N44', $details['grade']);
        $this->assertEquals('officer', $details['type']);
    }

    /**
     * Test isApplicantResponsible returns correct boolean
     *
     * @see Requirements 25.2 - Conditional fields toggle
     */
    #[Test]
    public function is_applicant_responsible(): void
    {
        $appSelf = LoanApplication::factory()->create([
            'is_applicant_responsible' => true,
        ]);

        $appDelegated = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
        ]);

        $this->assertTrue($this->service->isApplicantResponsible($appSelf));
        $this->assertFalse($this->service->isApplicantResponsible($appDelegated));
    }

    /**
     * Test validateResponsibleOfficerData validates correctly
     */
    #[Test]
    public function validate_responsible_officer_data_valid(): void
    {
        $validData = [
            'name' => 'Test Officer',
            'grade' => 'N41',
            'phone' => '0123456789',
            'email' => 'test@motac.gov.my',
        ];

        $this->assertTrue($this->service->validateResponsibleOfficerData($validData));
    }

    #[Test]
    public function validate_responsible_officer_data_missing_name(): void
    {
        $invalidData = [
            'grade' => 'N41',
            'phone' => '0123456789',
        ];

        $this->assertFalse($this->service->validateResponsibleOfficerData($invalidData));
    }

    #[Test]
    public function validate_responsible_officer_data_invalid_phone(): void
    {
        $invalidData = [
            'name' => 'Test Officer',
            'grade' => 'N41',
            'phone' => '123', // Too short
        ];

        $this->assertFalse($this->service->validateResponsibleOfficerData($invalidData));
    }

    #[Test]
    public function validate_responsible_officer_data_invalid_email(): void
    {
        $invalidData = [
            'name' => 'Test Officer',
            'grade' => 'N41',
            'phone' => '0123456789',
            'email' => 'not-an-email',
        ];

        $this->assertFalse($this->service->validateResponsibleOfficerData($invalidData));
    }

    /**
     * Test clearResponsibleOfficer removes data
     */
    #[Test]
    public function clear_responsible_officer(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'responsible_officer_name' => 'To Be Cleared',
            'responsible_officer_email' => 'clear@motac.gov.my',
            'responsible_officer_phone' => '0123456789',
            'responsible_officer_grade' => 'N41',
            'sponsorship_token' => 'some-token',
            'sponsorship_token_expires_at' => now()->addHours(48),
        ]);

        $this->service->clearResponsibleOfficer($application);

        $application->refresh();

        $this->assertTrue($application->is_applicant_responsible);
        $this->assertNull($application->responsible_officer_name);
        $this->assertNull($application->responsible_officer_email);
        $this->assertNull($application->responsible_officer_phone);
        $this->assertNull($application->responsible_officer_grade);
        $this->assertNull($application->sponsorship_token);
        $this->assertNull($application->sponsorship_token_expires_at);
    }

    #[Test]
    public function handle_delegated_application_skips_when_applicant_responsible(): void
    {
        $application = LoanApplication::factory()->create([
            'is_applicant_responsible' => true,
        ]);

        $this->service->handleDelegatedApplication($application);

        $application->refresh();

        $this->assertNull($application->sponsorship_token);
        $this->assertNull($application->sponsorship_token_expires_at);
    }

    #[Test]
    public function acknowledge_expired_token_returns_null(): void
    {
        LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'sponsorship_token' => 'expired-token',
            'sponsorship_token_expires_at' => now()->subHour(), // Expired
        ]);

        $result = $this->service->acknowledgeSponsorshipToken('expired-token');

        $this->assertNull($result);
    }

    #[Test]
    public function acknowledge_already_acknowledged_token_returns_null(): void
    {
        LoanApplication::factory()->create([
            'is_applicant_responsible' => false,
            'sponsorship_token' => 'used-token',
            'sponsorship_token_expires_at' => now()->addHour(),
            'responsible_officer_acknowledged_at' => now()->subMinutes(30), // Already acknowledged
        ]);

        $result = $this->service->acknowledgeSponsorshipToken('used-token');

        $this->assertNull($result);
    }
}
