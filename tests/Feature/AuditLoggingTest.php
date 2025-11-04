<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Audit Logging System Tests
 *
 * Tests comprehensive audit logging with 7-year retention,
 * immutable storage, and search capabilities.
 *
 * @see D03-FR-010.2 Audit logging system
 * @see D09 Database Documentation - Audit requirements
 */
class AuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable auditing for tests (disabled by default in console)
        config(['audit.console' => true]);
    }

    public function test_audit_records_are_created_for_model_changes(): void
    {
        $user = User::factory()->create();

        // Create a loan application (should trigger audit)
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        // Check audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => LoanApplication::class,
            'auditable_id' => $loanApplication->id,
            'event' => 'created',
        ]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('created', $audit->event);
        $this->assertNotNull($audit->new_values);
        // old_values can be null or empty array for created event
        $this->assertTrue(empty($audit->old_values), 'old_values should be empty for created event');
    }

    public function test_audit_records_track_updates(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        // Update the loan application
        $loanApplication->update(['status' => 'approved']);

        // Check update audit record
        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('updated', $audit->event);
        $this->assertArrayHasKey('status', $audit->old_values);
        $this->assertArrayHasKey('status', $audit->new_values);
        $this->assertEquals('submitted', $audit->old_values['status']);
        $this->assertEquals('approved', $audit->new_values['status']);
    }

    public function test_audit_records_are_immutable(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->first();

        $this->assertNotNull($audit);

        // Try to update the audit record
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Audit records are immutable and cannot be updated.');

        $audit->update(['event' => 'modified']);
    }

    public function test_audit_records_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->first();

        $this->assertNotNull($audit);

        // Try to delete the audit record
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Audit records cannot be deleted to maintain compliance.');

        $audit->delete();
    }

    public function test_audit_search_functionality(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Authenticate as user1 to ensure audit records have user_id
        $this->actingAs($user1);
        $loan1 = LoanApplication::factory()->create(['user_id' => $user1->id]);

        // Authenticate as user2
        $this->actingAs($user2);
        $loan2 = LoanApplication::factory()->create(['user_id' => $user2->id]);

        // Search by user
        $userAudits = Audit::search(['user_id' => $user1->id])->get();
        $this->assertGreaterThan(0, $userAudits->count());

        // Search by event
        $createdAudits = Audit::search(['event' => 'created'])->get();
        $this->assertGreaterThan(0, $createdAudits->count());

        // Search by auditable type
        $loanAudits = Audit::search(['auditable_type' => LoanApplication::class])->get();
        $this->assertGreaterThan(0, $loanAudits->count());
    }

    public function test_audit_statistics(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(3)->create(['user_id' => $user->id]);

        $stats = Audit::getStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_records', $stats);
        $this->assertArrayHasKey('records_last_30_days', $stats);
        $this->assertArrayHasKey('security_events_last_30_days', $stats);
        $this->assertArrayHasKey('oldest_record', $stats);
        $this->assertArrayHasKey('newest_record', $stats);
        $this->assertArrayHasKey('retention_cutoff', $stats);
        $this->assertArrayHasKey('expired_records', $stats);

        $this->assertGreaterThan(0, $stats['total_records']);
    }

    public function test_audit_retention_period_check(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->first();

        $this->assertNotNull($audit);
        $this->assertTrue($audit->isWithinRetentionPeriod());

        // Simulate old record by directly updating database (bypass immutability)
        DB::table('audits')
            ->where('id', $audit->id)
            ->update(['created_at' => now()->subYears(8)]);

        // Reload the audit record
        $audit = $audit->fresh();

        $this->assertFalse($audit->isWithinRetentionPeriod());
    }

    public function test_security_events_scope(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create(['user_id' => $user->id]);

        $securityEvents = Audit::securityEvents()->get();
        $this->assertGreaterThan(0, $securityEvents->count());

        foreach ($securityEvents as $event) {
            $this->assertContains($event->event, ['created', 'updated', 'deleted']);
            $this->assertContains($event->auditable_type, [
                'App\\Models\\User',
                'App\\Models\\LoanApplication',
                'App\\Models\\HelpdeskTicket',
            ]);
        }
    }

    public function test_audit_user_info_attribute(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Authenticate as the user so audit records have user context
        $this->actingAs($user);

        $loanApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('John Doe (john@example.com)', $audit->user_info);
    }

    public function test_audit_changes_summary_attribute(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        // Update to trigger audit with changes
        $loanApplication->update(['status' => 'approved']);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loanApplication->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($audit);
        $changesSummary = $audit->changes_summary;
        $this->assertStringContainsString('status:', $changesSummary);
        $this->assertStringContainsString('submitted', $changesSummary);
        $this->assertStringContainsString('approved', $changesSummary);
    }

    public function test_audit_cleanup_command_dry_run(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create(['user_id' => $user->id]);

        $this->artisan('audit:cleanup --dry-run')
            ->expectsOutput('No expired audit records found.')
            ->assertExitCode(0);
    }

    public function test_audit_date_range_scope(): void
    {
        $user = User::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['user_id' => $user->id]);

        $startDate = now()->subDay();
        $endDate = now()->addDay();

        $audits = Audit::dateRange($startDate, $endDate)->get();
        $this->assertGreaterThan(0, $audits->count());

        // Test outside range
        $oldStartDate = now()->subYears(2);
        $oldEndDate = now()->subYears(1);

        $oldAudits = Audit::dateRange($oldStartDate, $oldEndDate)->get();
        $this->assertEquals(0, $oldAudits->count());
    }
}
