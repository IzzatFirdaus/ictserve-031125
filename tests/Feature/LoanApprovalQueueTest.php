<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Livewire\Loans\ApprovalQueue;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class LoanApprovalQueueTest extends TestCase
{
    use DatabaseMigrations;

    public function test_approver_sees_pending_applications(): void
    {
        Mail::fake();

        $approver = User::factory()->approver()->create([
            'email' => 'approver@example.com',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => $approver->email,
            'approval_token' => 'token-123',
            'approval_token_expires_at' => now()->addDay(),
        ]);

        $this->actingAs($approver);

        Livewire::test(ApprovalQueue::class)
            ->assertSee($application->application_number)
            ->assertSee($application->applicant_name);
    }

    public function test_approver_can_approve_application(): void
    {
        Mail::fake();

        $approver = User::factory()->approver()->create([
            'email' => 'approver@example.com',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => $approver->email,
            'approval_token' => 'token-approve',
            'approval_token_expires_at' => now()->addDay(),
        ]);

        $this->actingAs($approver);

        Livewire::test(ApprovalQueue::class)
            ->set("remarks.{$application->id}", 'Semua maklumat lengkap.')
            ->call('approve', $application->id);

        $application->refresh();

        $this->assertSame(LoanStatus::APPROVED, $application->status);
        $this->assertSame('portal', $application->approval_method);
        $this->assertSame('Semua maklumat lengkap.', $application->approval_remarks);
        $this->assertNull($application->approval_token);
        $this->assertNotNull($application->approved_at);
    }

    public function test_approver_can_decline_application(): void
    {
        Mail::fake();

        $approver = User::factory()->approver()->create([
            'email' => 'approver@example.com',
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => $approver->email,
            'approval_token' => 'token-decline',
            'approval_token_expires_at' => now()->addDay(),
        ]);

        $this->actingAs($approver);

        Livewire::test(ApprovalQueue::class)
            ->set("remarks.{$application->id}", 'Maklumat tidak mencukupi.')
            ->call('decline', $application->id);

        $application->refresh();

        $this->assertSame(LoanStatus::REJECTED, $application->status);
        $this->assertSame('portal', $application->approval_method);
        $this->assertSame('Maklumat tidak mencukupi.', $application->approval_remarks);
        $this->assertSame('Maklumat tidak mencukupi.', $application->rejected_reason);
        $this->assertNull($application->approval_token);
        $this->assertNull($application->approved_at);
    }
}
