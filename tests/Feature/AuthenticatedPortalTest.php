<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Livewire\Approver\ApproverDashboard;
use App\Livewire\Loans\LoanExtension;
use App\Livewire\Loans\LoanHistory;
use App\Livewire\Staff\AuthenticatedDashboard;
use App\Livewire\UserProfile;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedPortalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticatedUserCanAccessDashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSuccessful()
            ->assertSeeLivewire(AuthenticatedDashboard::class);
    }

    #[Test]
    public function userCanViewLoanHistory(): void
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

    #[Test]
    public function userCanUpdateProfile(): void
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

    #[Test]
    public function userCanRequestLoanExtension(): void
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

        $loan->refresh();

        $this->assertSame(
            now()->addDays(7)->format('Y-m-d'),
            $loan->loan_end_date->format('Y-m-d')
        );
    }

    #[Test]
    public function approverCanApproveApplication(): void
    {
        $approver = User::factory()->approver()->create();
        // Mock Grade 41+
        $grade = Grade::factory()->create([
            'level' => 41,
            'can_approve_loans' => true,
        ]);
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

    #[Test]
    public function approverCanRejectApplication(): void
    {
        $approver = User::factory()->approver()->create();
        // Mock Grade 41+
        $grade = Grade::factory()->create([
            'level' => 41,
            'can_approve_loans' => true,
        ]);
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
