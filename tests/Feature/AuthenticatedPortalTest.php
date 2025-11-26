<?php

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Livewire\Approver\ApproverDashboard;
use App\Livewire\AuthenticatedDashboard;
use App\Livewire\Loans\LoanExtension;
use App\Livewire\Loans\LoanHistory;
use App\Livewire\UserProfile;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticatedPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSuccessful()
            ->assertSeeLivewire(AuthenticatedDashboard::class);
    }

    public function test_user_can_view_loan_history()
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'application_number' => 'LA2024010001',
        ]);

        $this->actingAs($user)
            ->get(route('loan.history'))
            ->assertSuccessful()
            ->assertSeeLivewire(LoanHistory::class)
            ->assertSee('LA2024010001');
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UserProfile::class)
            ->set('phone', '0123456789')
            ->set('bio', 'Updated bio')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '0123456789',
            'bio' => 'Updated bio',
        ]);
    }

    public function test_user_can_request_loan_extension()
    {
        $user = User::factory()->create();
        $loan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatus::IN_USE,
            'loan_end_date' => now()->addDays(1),
        ]);

        Livewire::actingAs($user)
            ->test(LoanExtension::class, ['application' => $loan])
            ->set('newEndDate', now()->addDays(7)->format('Y-m-d'))
            ->set('justification', 'Need more time for project')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $loan->id,
            'loan_end_date' => now()->addDays(7)->format('Y-m-d'),
        ]);
    }

    public function test_approver_can_approve_application()
    {
        $approver = User::factory()->create();
        // Mock Grade 41+
        $grade = Grade::factory()->create(['level' => 41]);
        $approver->grade()->associate($grade);
        $approver->save();

        $loan = LoanApplication::factory()->create([
            'approver_id' => $approver->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($approver)
            ->test(ApproverDashboard::class)
            ->call('openApprovalModal', $loan->id)
            ->set('remarks', 'Approved via portal')
            ->call('approve')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $loan->id,
            'status' => LoanStatus::APPROVED,
        ]);
    }

    public function test_approver_can_reject_application()
    {
        $approver = User::factory()->create();
        // Mock Grade 41+
        $grade = Grade::factory()->create(['level' => 41]);
        $approver->grade()->associate($grade);
        $approver->save();

        $loan = LoanApplication::factory()->create([
            'approver_id' => $approver->id,
            'status' => LoanStatus::SUBMITTED,
        ]);

        Livewire::actingAs($approver)
            ->test(ApproverDashboard::class)
            ->call('openRejectionModal', $loan->id)
            ->set('remarks', 'Rejected due to policy')
            ->call('reject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'id' => $loan->id,
            'status' => LoanStatus::REJECTED,
        ]);
    }
}
