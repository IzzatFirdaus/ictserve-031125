<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\CrossModuleIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Loan Module Integration Tests
 *
 * @trace D03-FR-016.1 (Cross-Module Integration)
 * @trace D03-FR-001.1 (Complete User Workflows)
 */
class LoanModuleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_guest_loan_workflow(): void
    {
        Mail::fake();
        
        $asset = Asset::factory()->create(['status' => 'available']);

        // Step 1: Guest submits application
        $response = $this->post(route('loans.store'), [
            'applicant_name' => 'Ahmad bin Abdullah',
            'applicant_email' => 'ahmad@motac.gov.my',
            'purpose' => 'Project presentation',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(7)->format('Y-m-d'),
            'selected_assets' => [$asset->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loan_applications', [
            'applicant_email' => 'ahmad@motac.gov.my',
            'status' => 'pending',
        ]);

        // Step 2: Verify confirmation email sent
        Mail::assertSent(\App\Mail\Loans\LoanApplicationSubmitted::class);

        // Step 3: Approver receives approval request
        Mail::assertSent(\App\Mail\Loans\LoanApprovalRequest::class);
    }

    public function test_complete_authenticated_loan_workflow(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        // Submit application
        $response = $this->post(route('loans.store'), [
            'purpose' => 'Development work',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(7)->format('Y-m-d'),
            'selected_assets' => [$asset->id],
        ]);

        $response->assertRedirect();
        
        $application = LoanApplication::where('user_id', $user->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals('pending', $application->status);
    }

    public function test_email_approval_workflow(): void
    {
        $application = LoanApplication::factory()->create([
            'status' => 'pending',
            'approval_token' => 'test-token-123',
        ]);

        // Approve via email link
        $response = $this->get(route('loans.approve', [
            'token' => 'test-token-123',
            'action' => 'approve',
        ]));

        $response->assertOk();
        $this->assertDatabaseHas('loan_applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);
    }

    public function test_cross_module_integration_with_helpdesk(): void
    {
        $service = app(CrossModuleIntegrationService::class);
        $asset = Asset::factory()->create();
        $application = LoanApplication::factory()->create();

        // Simulate damaged asset return
        $ticket = $service->createMaintenanceTicket(
            $asset,
            $application,
            ['condition' => 'damaged', 'description' => 'Screen cracked']
        );

        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);
        $this->assertStringContainsString('damaged', $ticket->description);
    }

    public function test_asset_availability_updates_correctly(): void
    {
        $asset = Asset::factory()->create(['status' => 'available']);
        $application = LoanApplication::factory()->create(['status' => 'pending']);

        // Approve application
        $application->update(['status' => 'approved']);
        $application->loanItems()->create(['asset_id' => $asset->id]);

        // Asset should be marked as loaned
        $asset->refresh();
        $this->assertEquals('loaned', $asset->status);
    }

    public function test_loan_extension_workflow(): void
    {
        $user = User::factory()->create();
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved',
            'return_by' => now()->addDays(3),
        ]);

        $this->actingAs($user);

        // Request extension
        $response = $this->post(route('loans.extend', $application), [
            'new_return_date' => now()->addDays(10)->format('Y-m-d'),
            'justification' => 'Project delayed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loan_transactions', [
            'loan_application_id' => $application->id,
            'transaction_type' => 'extension_requested',
        ]);
    }

    public function test_overdue_notification_system(): void
    {
        Notification::fake();

        $application = LoanApplication::factory()->create([
            'status' => 'approved',
            'return_by' => now()->subDays(5),
        ]);

        // Trigger overdue check
        $this->artisan('loans:check-overdue');

        // Verify notification sent
        Notification::assertSentTo(
            $application->user ?? $application,
            \App\Notifications\LoanOverdueNotification::class
        );
    }

    public function test_bulk_approval_workflow(): void
    {
        $applications = LoanApplication::factory()->count(5)->create(['status' => 'pending']);

        // Bulk approve
        $response = $this->post(route('loans.bulk-approve'), [
            'application_ids' => $applications->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        
        foreach ($applications as $application) {
            $this->assertDatabaseHas('loan_applications', [
                'id' => $application->id,
                'status' => 'approved',
            ]);
        }
    }
}
