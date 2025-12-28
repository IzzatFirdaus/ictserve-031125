<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Audit Logging System Tests
 *
 * Tests comprehensive dual audit logging system:
 * - Owen-it/laravel-auditing: Field-level compliance tracking with old/new values
 * - Spatie/laravel-activitylog: User activity operational logging
 *
 * Both systems maintain 7-year retention per PDPA/Arkib Negara requirements.
 *
 * @see D03-FR-010.2 Audit logging system
 * @see D09 Database Documentation - Dual Audit System (§4.6-4.7)
 * @see Requirements 6.1, 6.2, 6.3 - Dual Audit System Validation
 */
class AuditLoggingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Enable auditing for tests (disabled by default in console)
        config(['audit.console' => true]);
    }

    #[Test]
    public function audit_records_are_created_for_model_changes(): void
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

    #[Test]
    public function audit_records_track_updates(): void
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

    #[Test]
    public function audit_records_are_immutable(): void
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

    #[Test]
    public function audit_records_cannot_be_deleted(): void
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

    #[Test]
    public function audit_search_functionality(): void
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

    #[Test]
    public function audit_statistics(): void
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

    #[Test]
    public function audit_retention_period_check(): void
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

    #[Test]
    public function security_events_scope(): void
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

    #[Test]
    public function audit_user_info_attribute(): void
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

    #[Test]
    public function audit_changes_summary_attribute(): void
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

    #[Test]
    public function audit_cleanup_command_dry_run(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->create(['user_id' => $user->id]);

        $this->artisan('audit:cleanup --dry-run')
            ->expectsOutput('No expired audit records found.')
            ->assertExitCode(0);
    }

    #[Test]
    public function audit_date_range_scope(): void
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

    // =========================================================================
    // Dual Audit System Tests (v3.6.0)
    // =========================================================================

    /**
     * Test that model changes are recorded in BOTH audit systems.
     *
     * @see Requirements 6.1, 6.2 - Dual Audit System
     */
    #[Test]
    public function model_changes_are_recorded_in_both_audit_systems(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Ujian Sistem Audit Dwi', // BM: Dual Audit System Test
            'status' => 'open',
        ]);

        // Verify Owen-it audit record (compliance - field-level tracking)
        $this->assertDatabaseHas('audits', [
            'auditable_type' => HelpdeskTicket::class,
            'auditable_id' => $ticket->id,
            'event' => 'created',
        ]);

        // Verify Spatie activity log record (operational logging)
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => HelpdeskTicket::class,
            'subject_id' => $ticket->id,
            'event' => 'created',
            'log_name' => 'helpdesk',
        ]);
    }

    /**
     * Test that updates are tracked in both audit systems with old/new values.
     *
     * @see Requirements 6.1, 6.2 - Field-level tracking
     */
    #[Test]
    public function updates_are_tracked_in_both_audit_systems(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Ujian Asal', // BM: Original Test
        ]);

        // Update the ticket subject (a field that's definitely tracked)
        $ticket->update(['subject' => 'Ujian Dikemaskini']); // BM: Updated Test

        // Verify Owen-it audit tracks the update event
        $owenItAudit = \OwenIt\Auditing\Models\Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($owenItAudit, 'Owen-it audit record should exist for update');
        // Verify that either old_values or new_values contains the subject change
        $hasSubjectInOld = is_array($owenItAudit->old_values) && array_key_exists('subject', $owenItAudit->old_values);
        $hasSubjectInNew = is_array($owenItAudit->new_values) && array_key_exists('subject', $owenItAudit->new_values);
        $this->assertTrue(
            $hasSubjectInOld || $hasSubjectInNew,
            'Subject field change should be tracked in audit record'
        );

        // Verify Spatie activity log tracks the update
        $spatieActivity = Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($spatieActivity, 'Spatie activity log should exist for update');
        $this->assertEquals($user->id, $spatieActivity->causer_id);
    }

    /**
     * Test that both audit systems track the causer (user who performed action).
     *
     * @see Requirements 6.3 - Audit trail access
     */
    #[Test]
    public function both_audit_systems_track_causer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $loan = LoanApplication::factory()->create([
            'purpose' => 'Ujian Penjejakan Pengguna', // BM: User Tracking Test
        ]);

        // Verify Owen-it tracks user
        $owenItAudit = \OwenIt\Auditing\Models\Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loan->id)
            ->first();

        $this->assertNotNull($owenItAudit);
        $this->assertEquals($admin->id, $owenItAudit->user_id);

        // Verify Spatie tracks causer
        $spatieActivity = Activity::where('subject_type', LoanApplication::class)
            ->where('subject_id', $loan->id)
            ->first();

        $this->assertNotNull($spatieActivity);
        $this->assertEquals(User::class, $spatieActivity->causer_type);
        $this->assertEquals($admin->id, $spatieActivity->causer_id);
    }

    /**
     * Test that different log names are used for different modules.
     *
     * @see Requirements 6.2 - Categorized activity logs
     */
    #[Test]
    #[DataProvider('moduleLogNameProvider')]
    public function activity_log_uses_correct_log_names(string $modelClass, string $expectedLogName): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($modelClass === HelpdeskTicket::class) {
            $model = HelpdeskTicket::factory()->create();
        } elseif ($modelClass === LoanApplication::class) {
            $model = LoanApplication::factory()->create();
        } else {
            $this->fail("Unknown model class: {$modelClass}");
        }

        $activity = Activity::where('subject_type', $modelClass)
            ->where('subject_id', $model->id)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($expectedLogName, $activity->log_name);
    }

    /**
     * Data provider for module log names.
     *
     * @return array<string, array{string, string}>
     */
    public static function moduleLogNameProvider(): array
    {
        return [
            'helpdesk module uses helpdesk log' => [HelpdeskTicket::class, 'helpdesk'],
            'loan module uses loan log' => [LoanApplication::class, 'loan'],
        ];
    }

    /**
     * Test that 7-year retention is configured for both audit systems.
     *
     * @see Requirements 6.3 - 7-year retention per PDPA/Arkib Negara
     */
    #[Test]
    public function seven_year_retention_is_configured_for_both_systems(): void
    {
        // Owen-it retention configuration
        $owenItRetention = config('audit.retention');
        $this->assertTrue($owenItRetention['enabled'], 'Owen-it retention should be enabled');
        $this->assertEquals(7, $owenItRetention['years'], 'Owen-it retention should be 7 years');

        // Spatie retention configuration (2555 days = 7 years)
        $spatieRetentionDays = config('activitylog.delete_records_older_than_days');
        $this->assertEquals(2555, $spatieRetentionDays, 'Spatie retention should be 2555 days (7 years)');
    }

    /**
     * Test that audit records include BM content when applicable.
     *
     * @see Requirements 3.1 - Bahasa Melayu UI
     */
    #[Test]
    public function audit_records_preserve_bahasa_melayu_content(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Masalah Pencetak Tidak Berfungsi', // BM: Printer Not Working Issue
            'description' => 'Pencetak di tingkat 3 tidak dapat mencetak dokumen.', // BM: Printer on floor 3 cannot print documents
        ]);

        // Verify Owen-it audit preserves BM content
        $owenItAudit = \OwenIt\Auditing\Models\Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($owenItAudit);
        $this->assertStringContainsString('Masalah Pencetak', $owenItAudit->new_values['subject']);
        $this->assertStringContainsString('tingkat 3', $owenItAudit->new_values['description']);
    }
}
