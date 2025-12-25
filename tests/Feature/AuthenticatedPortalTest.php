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
    public function authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSuccessful();

        // Check that the page contains the Livewire component
        $response->assertSee('wire:id');
    }

    #[Test]
    public function user_can_view_loan_history(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'application_number' => 'LA2024010001',
        ]);

        $response = $this->actingAs($user)
            ->get(route('loan.history'))
            ->assertSuccessful()
            ->assertSee('LA2024010001');

        // Check that the page contains the Livewire component
        $response->assertSee('wire:id');
    }

    #[Test]
    public function user_can_update_profile(): void
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
    public function user_can_request_loan_extension(): void
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
    public function approver_can_approve_application(): void
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
    public function approver_can_reject_application(): void
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
