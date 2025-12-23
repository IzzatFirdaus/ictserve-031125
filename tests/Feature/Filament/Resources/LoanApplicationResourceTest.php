<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Enums\LoanStatus;
use App\Filament\Resources\LoanApplications\LoanApplicationResource;
use App\Filament\Resources\LoanApplications\Pages\CreateLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\EditLoanApplication;
use App\Filament\Resources\LoanApplications\Pages\ListLoanApplications;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Loan Application Resource Test v3.6.0
 *
 * Tests comprehensive loan application management functionality including
 * CRUD operations, approval workflow actions, and Bahasa Melayu content.
 *
 * @see D03 Requirements 8.3, 8.5, 11.1, 11.2
 * @see D04 Software Design - Filament Resources
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
 */
class LoanApplicationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set application locale to Bahasa Melayu
        app()->setLocale('ms');
    }

    #[Test]
    public function can_render_index_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(LoanApplicationResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function can_render_create_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(LoanApplicationResource::getUrl('create'))
            ->assertSuccessful();
    }

    #[Test]
    public function can_render_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $loanApplication = LoanApplication::factory()->create();

        $this->get(LoanApplicationResource::getUrl('edit', ['record' => $loanApplication]))
            ->assertSuccessful();
    }

    #[Test]
    public function can_render_view_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $loanApplication = LoanApplication::factory()->create();

        $this->get(LoanApplicationResource::getUrl('view', ['record' => $loanApplication]))
            ->assertSuccessful();
    }

    #[Test]
    public function can_list_loan_applications_with_livewire(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $applications = LoanApplication::factory()->count(3)->create();

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->assertCanSeeTableRecords($applications);
    }

    #[Test]
    public function can_create_loan_application_with_livewire(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($user)
            ->test(CreateLoanApplication::class)
            ->fillForm([
                'applicant_name' => 'Ahmad bin Ali',
                'applicant_email' => 'ahmad@motac.gov.my',
                'applicant_phone' => '03-12345678',
                'purpose' => 'Untuk mesyuarat dengan klien',
                'loan_start_date' => now()->addDay()->format('Y-m-d'),
                'loan_end_date' => now()->addDays(7)->format('Y-m-d'),
                'status' => LoanStatus::DRAFT->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('loan_applications', [
            'applicant_name' => 'Ahmad bin Ali',
            'applicant_email' => 'ahmad@motac.gov.my',
            'purpose' => 'Untuk mesyuarat dengan klien',
        ]);
    }

    #[Test]
    public function can_validate_loan_application_form(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($user)
            ->test(CreateLoanApplication::class)
            ->fillForm([
                'applicant_name' => '',
                'applicant_email' => 'invalid-email',
                'loan_start_date' => now()->subDay()->format('Y-m-d'), // Past date
            ])
            ->call('create')
            ->assertHasFormErrors([
                'applicant_name' => 'required',
                'applicant_email' => 'email',
                'loan_start_date' => 'after_or_equal',
            ]);
    }

    #[Test]
    public function can_edit_loan_application_with_livewire(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $application = LoanApplication::factory()->create([
            'applicant_name' => 'Original Name',
        ]);

        Livewire::actingAs($user)
            ->test(EditLoanApplication::class, [
                'record' => $application->getRouteKey(),
            ])
            ->fillForm([
                'applicant_name' => 'Updated Name',
                'purpose' => 'Updated purpose',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'applicant_name' => 'Updated Name',
            'purpose' => 'Updated purpose',
        ]);
    }

    #[Test]
    public function can_filter_loan_applications_by_status(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $draftApplications = LoanApplication::factory()->count(2)->create(['status' => LoanStatus::DRAFT]);
        $approvedApplications = LoanApplication::factory()->count(3)->create(['status' => LoanStatus::APPROVED]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->filterTable('status', LoanStatus::DRAFT->value)
            ->assertCanSeeTableRecords($draftApplications)
            ->assertCanNotSeeTableRecords($approvedApplications);
    }

    #[Test]
    public function can_search_loan_applications_by_applicant_name(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $searchableApplication = LoanApplication::factory()->create(['applicant_name' => 'Ahmad bin Ali']);
        $otherApplication = LoanApplication::factory()->create(['applicant_name' => 'Siti binti Omar']);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->searchTable('Ahmad')
            ->assertCanSeeTableRecords([$searchableApplication])
            ->assertCanNotSeeTableRecords([$otherApplication]);
    }

    #[Test]
    public function can_sort_loan_applications_by_created_date(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $oldApplication = LoanApplication::factory()->create(['created_at' => now()->subDays(2)]);
        $newApplication = LoanApplication::factory()->create(['created_at' => now()]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords([$newApplication, $oldApplication], inOrder: true);
    }

    #[Test]
    public function can_approve_loan_application(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->callTableAction('approve', $application, data: [
                'remarks' => 'Diluluskan untuk kegunaan rasmi',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => LoanStatus::APPROVED->value,
        ]);

        $application->refresh();
        $this->assertNotNull($application->approved_at);
    }

    #[Test]
    public function can_decline_loan_application(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->callTableAction('decline', $application, data: [
                'reason' => 'Dokumen tidak lengkap',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => LoanStatus::REJECTED->value,
            'rejected_reason' => 'Dokumen tidak lengkap',
        ]);
    }

    #[Test]
    public function can_extend_loan_application(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::IN_USE,
            'loan_end_date' => now()->addDays(3),
        ]);

        $newEndDate = now()->addDays(10);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->callTableAction('extend', $application, data: [
                'loan_end_date' => $newEndDate->format('Y-m-d'),
                'special_instructions' => 'Sambungan untuk projek khas',
            ])
            ->assertNotified();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'loan_end_date' => $newEndDate->format('Y-m-d'),
            'special_instructions' => 'Sambungan untuk projek khas',
            'status' => LoanStatus::RETURN_DUE->value,
        ]);
    }

    #[Test]
    public function can_bulk_approve_loan_applications(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $applications = LoanApplication::factory()->count(3)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->callTableBulkAction('approve', $applications)
            ->assertNotified();

        foreach ($applications as $application) {
            $this->assertDatabaseHas('loan_applications', [
                'id' => $application->id,
                'status' => LoanStatus::APPROVED->value,
            ]);
        }
    }

    #[Test]
    public function can_bulk_decline_loan_applications(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $applications = LoanApplication::factory()->count(2)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->callTableBulkAction('decline', $applications, [
                'reason' => 'Tidak memenuhi kriteria',
            ])
            ->assertNotified();

        foreach ($applications as $application) {
            $this->assertDatabaseHas('loan_applications', [
                'id' => $application->id,
                'status' => LoanStatus::REJECTED->value,
                'rejected_reason' => 'Tidak memenuhi kriteria',
            ]);
        }
    }

    #[Test]
    public function displays_bahasa_melayu_table_headers(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        LoanApplication::factory()->create();

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('No. Permohonan'); // Application Number
        $response->assertSee('Pemohon'); // Applicant
        $response->assertSee('Status'); // Status
        $response->assertSee('Bahagian'); // Division
        $response->assertSee('Keutamaan'); // Priority
    }

    #[Test]
    public function displays_loan_status_options_in_bahasa_melayu(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        LoanApplication::factory()->create(['status' => LoanStatus::APPROVED]);

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('Diluluskan'); // Approved status in BM
    }

    #[Test]
    public function displays_approval_workflow_actions_in_bahasa_melayu(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $application = LoanApplication::factory()->create(['status' => LoanStatus::SUBMITTED]);

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('Luluskan'); // Approve action
        $response->assertSee('Tolak'); // Decline action
    }

    #[Test]
    public function displays_overdue_status_indicators(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $overdueApplication = LoanApplication::factory()->create([
            'status' => LoanStatus::IN_USE,
            'loan_end_date' => now()->subDays(5), // 5 days overdue
        ]);

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('Lewat Tempoh'); // Overdue indicator
    }

    #[Test]
    public function displays_submission_type_indicators(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $guestApplication = LoanApplication::factory()->create(['user_id' => null]);
        $authenticatedApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('Permohonan Tetamu'); // Guest submission
        $response->assertSee('Permohonan Pengguna'); // Authenticated submission
    }

    #[Test]
    public function can_use_pagination(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        LoanApplication::factory()->count(30)->create();

        Livewire::actingAs($user)
            ->test(ListLoanApplications::class)
            ->assertCanRenderTableColumn('applicant_name')
            ->assertCountTableRecords(25); // Default pagination
    }

    #[Test]
    public function can_export_loan_applications(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        LoanApplication::factory()->count(5)->create();

        $response = Livewire::actingAs($user)
            ->test(ListLoanApplications::class);

        $response->assertSee('Eksport Excel'); // Export Excel action
        $response->assertSee('Eksport Laporan'); // Export Report action
    }
}
