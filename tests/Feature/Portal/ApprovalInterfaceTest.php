<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Livewire\Portal\ApprovalInterface;
use App\Livewire\Portal\ApprovalModal;
use App\Mail\LoanApprovedMail;
use App\Mail\LoanRejectedMail;
use App\Models\Asset;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Approval Interface Feature Tests
 *
 * Tests Grade 41+ authorization, approval/rejection actions,
 * bulk operations, and email notifications.
 *
 * Requirements: 4.1, 4.2, 4.3, 4.4, 4.5
 * Traceability: D03 SRS-FR-004, D04 §3.4
 */
class ApprovalInterfaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $approver;

    protected User $staff;

    protected Division $division;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->asset = Asset::factory()->create(['name' => 'Test Laptop']);

        $this->approver = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 41, // Grade 41+ for approver
        ]);

        $this->staff = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40, // Below Grade 41
        ]);
    }

    #[Test]
    public function grade_41_plus_user_can_access_approval_interface(): void
    {
        $response = $this->actingAs($this->approver)->get('/portal/approvals');

        $response->assertStatus(200);
        $response->assertSee('Pending Approvals');
    }

    #[Test]
    public function below_grade_41_user_cannot_access_approval_interface(): void
    {
        $response = $this->actingAs($this->staff)->get('/portal/approvals');

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_approval_interface(): void
    {
        $response = $this->get('/portal/approvals');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function approval_interface_displays_pending_applications(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalInterface::class)
            ->assertSee($application->application_number)
            ->assertSee($this->staff->name);
    }

    #[Test]
    public function approval_interface_does_not_display_approved_applications(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'approved',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalInterface::class)
            ->assertDontSee($application->application_number);
    }

    #[Test]
    public function approver_can_view_application_details(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
            'purpose' => 'Testing purposes',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->assertSee($application->application_number)
            ->assertSee('Testing purposes')
            ->assertSee($this->staff->name)
            ->assertSee($this->asset->name);
    }

    #[Test]
    public function approver_can_approve_loan_application(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->set('remarks', 'Approved for testing')
            ->call('processApproval')
            ->assertHasNoErrors();

        $this->assertEquals('approved', $application->fresh()->status);
        $this->assertEquals('portal', $application->fresh()->approval_method);
        $this->assertEquals('Approved for testing', $application->fresh()->approval_remarks);
        $this->assertNotNull($application->fresh()->approved_at);
        $this->assertEquals($this->approver->id, $application->fresh()->approved_by);
    }

    #[Test]
    public function approver_can_reject_loan_application(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'reject')
            ->set('remarks', 'Insufficient justification')
            ->call('processApproval')
            ->assertHasNoErrors();

        $this->assertEquals('rejected', $application->fresh()->status);
        $this->assertEquals('portal', $application->fresh()->approval_method);
        $this->assertEquals('Insufficient justification', $application->fresh()->approval_remarks);
    }

    #[Test]
    public function approval_remarks_are_optional(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->set('remarks', '')
            ->call('processApproval')
            ->assertHasNoErrors();

        $this->assertEquals('approved', $application->fresh()->status);
    }

    #[Test]
    public function approval_remarks_cannot_exceed_500_characters(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->set('remarks', str_repeat('a', 501))
            ->call('processApproval')
            ->assertHasErrors(['remarks' => 'max']);
    }

    #[Test]
    public function email_notification_sent_on_approval(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->call('processApproval');

        Mail::assertQueued(LoanApprovedMail::class, function ($mail) use ($application) {
            return $mail->hasTo($this->staff->email) &&
                $mail->application->id === $application->id;
        });
    }

    #[Test]
    public function email_notification_sent_on_rejection(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'reject')
            ->set('remarks', 'Not approved')
            ->call('processApproval');

        Mail::assertQueued(LoanRejectedMail::class, function ($mail) use ($application) {
            return $mail->hasTo($this->staff->email) &&
                $mail->application->id === $application->id;
        });
    }

    #[Test]
    public function approver_can_select_multiple_applications(): void
    {
        $app1 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        $app2 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalInterface::class)
            ->call('selectApplication', $app1->id)
            ->call('selectApplication', $app2->id)
            ->assertSet('selectedApplications', [$app1->id, $app2->id]);
    }

    #[Test]
    public function approver_can_bulk_approve_applications(): void
    {
        Mail::fake();

        $app1 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        $app2 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalInterface::class)
            ->set('selectedApplications', [$app1->id, $app2->id])
            ->call('bulkApprove')
            ->assertHasNoErrors();

        $this->assertEquals('approved', $app1->fresh()->status);
        $this->assertEquals('approved', $app2->fresh()->status);
    }

    #[Test]
    public function approver_can_bulk_reject_applications(): void
    {
        Mail::fake();

        $app1 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        $app2 = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalInterface::class)
            ->set('selectedApplications', [$app1->id, $app2->id])
            ->call('bulkReject')
            ->assertHasNoErrors();

        $this->assertEquals('rejected', $app1->fresh()->status);
        $this->assertEquals('rejected', $app2->fresh()->status);
    }

    #[Test]
    public function approval_action_is_audited(): void
    {
        Mail::fake();

        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->call('processApproval');

        $this->assertDatabaseHas('audits', [
            'user_id' => $this->approver->id,
            'auditable_type' => LoanApplication::class,
            'auditable_id' => $application->id,
            'event' => 'updated',
        ]);
    }

    #[Test]
    public function approver_cannot_approve_already_approved_application(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'approved',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->call('processApproval')
            ->assertHasErrors();
    }

    #[Test]
    public function confirmation_modal_displayed_before_approval(): void
    {
        $application = LoanApplication::factory()->create([
            'user_id' => $this->staff->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->call('confirmAction')
            ->assertSet('showConfirmation', true);
    }
}
