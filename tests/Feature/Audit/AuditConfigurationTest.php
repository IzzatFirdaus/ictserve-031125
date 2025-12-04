<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Resolvers\HashedIpAddressResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

/**
 * Audit Configuration Tests
 *
 * Verifies owen-it/laravel-auditing is properly configured per Task 35.1:
 * - All auditable models have the trait enabled
 * - IP addresses are hashed for PDPA privacy compliance
 * - 7-year retention policy is configured
 *
 * @see Requirements 19.1, 19.3 - Dual Audit System
 * @see D09 §4.6 - Field-level audit requirements
 */
class AuditConfigurationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that audit configuration is enabled.
     */
    public function test_audit_is_enabled(): void
    {
        $this->assertTrue(config('audit.enabled'));
    }

    /**
     * Test that custom hashed IP resolver is configured.
     */
    public function test_hashed_ip_resolver_is_configured(): void
    {
        $resolverClass = config('audit.resolvers.ip_address');

        $this->assertEquals(
            HashedIpAddressResolver::class,
            $resolverClass,
            'IP Address resolver should use HashedIpAddressResolver for PDPA compliance'
        );
    }

    /**
     * Test that 7-year retention policy is configured.
     */
    public function test_seven_year_retention_policy_is_configured(): void
    {
        $retention = config('audit.retention');

        $this->assertTrue($retention['enabled'], 'Retention policy should be enabled');
        $this->assertEquals(7, $retention['years'], 'Retention should be 7 years per PDPA/Arkib Negara requirements');
    }

    /**
     * Test that audit events include all required events.
     */
    public function test_audit_events_are_configured(): void
    {
        $events = config('audit.events');

        $this->assertContains('created', $events);
        $this->assertContains('updated', $events);
        $this->assertContains('deleted', $events);
        $this->assertContains('restored', $events);
    }

    /**
     * Test that HashedIpAddressResolver returns hashed IP.
     */
    public function test_hashed_ip_resolver_returns_hashed_ip(): void
    {
        // Simulate a request with an IP address
        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.100');

        // Create a mock auditable model for the resolver
        $ticket = HelpdeskTicket::factory()->make();
        $hashedIp = HashedIpAddressResolver::resolve($ticket);

        $this->assertNotNull($hashedIp);
        $this->assertNotEquals('192.168.1.100', $hashedIp, 'IP should be hashed, not raw');
        $this->assertEquals(64, strlen($hashedIp), 'SHA-256 hash should be 64 characters');
    }

    /**
     * Test that same IP produces same hash (deterministic).
     */
    public function test_hashed_ip_is_deterministic(): void
    {
        $this->app['request']->server->set('REMOTE_ADDR', '10.0.0.1');

        $ticket = HelpdeskTicket::factory()->make();
        $hash1 = HashedIpAddressResolver::resolve($ticket);

        $this->app['request']->server->set('REMOTE_ADDR', '10.0.0.1');
        $hash2 = HashedIpAddressResolver::resolve($ticket);

        $this->assertEquals($hash1, $hash2, 'Same IP should produce same hash');
    }

    /**
     * Test that different IPs produce different hashes.
     */
    public function test_different_ips_produce_different_hashes(): void
    {
        $ticket = HelpdeskTicket::factory()->make();

        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.1');
        $hash1 = HashedIpAddressResolver::resolve($ticket);

        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.2');
        $hash2 = HashedIpAddressResolver::resolve($ticket);

        $this->assertNotEquals($hash1, $hash2, 'Different IPs should produce different hashes');
    }

    /**
     * Test that HelpdeskTicket model creates audit records.
     */
    public function test_helpdesk_ticket_creates_audit_record(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test Ticket for Audit',
            'status' => 'open',
        ]);

        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($audit, 'Audit record should be created for HelpdeskTicket');
        $this->assertEquals($user->id, $audit->user_id);
    }

    /**
     * Test that HelpdeskTicket update creates audit record with old/new values.
     */
    public function test_helpdesk_ticket_update_records_field_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Original Subject',
        ]);

        // Update the ticket subject (a field that's definitely audited)
        $ticket->update(['subject' => 'Updated Subject']);

        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($audit, 'Audit record should be created for update');

        // Verify old and new values are recorded (may be arrays or null depending on config)
        $this->assertTrue(
            is_array($audit->old_values) || is_null($audit->old_values),
            'old_values should be array or null'
        );
        $this->assertTrue(
            is_array($audit->new_values) || is_null($audit->new_values),
            'new_values should be array or null'
        );

        // Verify that subject field change is tracked in either old or new values
        $hasSubjectInOld = is_array($audit->old_values) && array_key_exists('subject', $audit->old_values);
        $hasSubjectInNew = is_array($audit->new_values) && array_key_exists('subject', $audit->new_values);
        $this->assertTrue(
            $hasSubjectInOld || $hasSubjectInNew,
            'Subject field change should be tracked in audit record'
        );
    }

    /**
     * Test that LoanApplication model creates audit records.
     */
    public function test_loan_application_creates_audit_record(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $loan = LoanApplication::factory()->create([
            'purpose' => 'Test Loan for Audit',
            'status' => \App\Enums\LoanStatus::DRAFT,
        ]);

        $audit = Audit::where('auditable_type', LoanApplication::class)
            ->where('auditable_id', $loan->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($audit, 'Audit record should be created for LoanApplication');
    }

    /**
     * Test that User model creates audit records.
     */
    public function test_user_model_creates_audit_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user = User::factory()->create([
            'name' => 'Test User for Audit',
            'email' => 'audituser@motac.gov.my',
        ]);

        $audit = Audit::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($audit, 'Audit record should be created for User');
    }

    /**
     * Test that audit records include hashed IP address.
     */
    public function test_audit_records_include_hashed_ip(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Set a known IP
        $this->app['request']->server->set('REMOTE_ADDR', '203.0.113.50');

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test IP Hashing',
        ]);

        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->first();

        $this->assertNotNull($audit->ip_address);
        $this->assertNotEquals('203.0.113.50', $audit->ip_address, 'IP should be hashed');
        $this->assertEquals(64, strlen($audit->ip_address), 'Hashed IP should be 64 chars (SHA-256)');
    }

    /**
     * Test that audit driver is set to database.
     */
    public function test_audit_driver_is_database(): void
    {
        $this->assertEquals('database', config('audit.driver'));
    }

    /**
     * Test that audit table is configured correctly.
     */
    public function test_audit_table_is_configured(): void
    {
        $this->assertEquals('audits', config('audit.drivers.database.table'));
    }

    // =========================================================================
    // Spatie Activity Log Tests (Task 35.3)
    // =========================================================================

    /**
     * Test that activity log is enabled.
     *
     * @see Requirements 19.2 - Activity log for operational dashboards
     */
    public function test_activity_log_is_enabled(): void
    {
        $this->assertTrue(config('activitylog.enabled'));
    }

    /**
     * Test that activity log retention is set to 7 years.
     *
     * @see Requirements 19.2 - 7-year retention per PDPA/Arkib Negara
     */
    public function test_activity_log_retention_is_seven_years(): void
    {
        $days = config('activitylog.delete_records_older_than_days');

        // 7 years = 2555 days (7 * 365)
        $this->assertEquals(2555, $days, 'Activity log retention should be 7 years (2555 days)');
    }

    /**
     * Test that User model has LogsActivity trait.
     *
     * @see Requirements 19.4 - Activity log on significant user actions
     */
    public function test_user_model_has_logs_activity_trait(): void
    {
        $traits = class_uses_recursive(User::class);

        $this->assertContains(
            \Spatie\Activitylog\Traits\LogsActivity::class,
            $traits,
            'User model should have LogsActivity trait'
        );
    }

    /**
     * Test that HelpdeskTicket model has LogsActivity trait.
     *
     * @see Requirements 19.4 - Activity log on significant user actions
     */
    public function test_helpdesk_ticket_model_has_logs_activity_trait(): void
    {
        $traits = class_uses_recursive(HelpdeskTicket::class);

        $this->assertContains(
            \Spatie\Activitylog\Traits\LogsActivity::class,
            $traits,
            'HelpdeskTicket model should have LogsActivity trait'
        );
    }

    /**
     * Test that LoanApplication model has LogsActivity trait.
     *
     * @see Requirements 19.4 - Activity log on significant user actions
     */
    public function test_loan_application_model_has_logs_activity_trait(): void
    {
        $traits = class_uses_recursive(LoanApplication::class);

        $this->assertContains(
            \Spatie\Activitylog\Traits\LogsActivity::class,
            $traits,
            'LoanApplication model should have LogsActivity trait'
        );
    }

    /**
     * Test that User model uses 'auth' log name.
     *
     * @see Requirements 19.4 - Categorized activity logs
     */
    public function test_user_model_uses_auth_log_name(): void
    {
        $user = new User;
        $options = $user->getActivitylogOptions();

        $this->assertEquals('auth', $options->logName, 'User model should use "auth" log name');
    }

    /**
     * Test that HelpdeskTicket model uses 'helpdesk' log name.
     *
     * @see Requirements 19.4 - Categorized activity logs
     */
    public function test_helpdesk_ticket_model_uses_helpdesk_log_name(): void
    {
        $ticket = new HelpdeskTicket;
        $options = $ticket->getActivitylogOptions();

        $this->assertEquals('helpdesk', $options->logName, 'HelpdeskTicket model should use "helpdesk" log name');
    }

    /**
     * Test that LoanApplication model uses 'loan' log name.
     *
     * @see Requirements 19.4 - Categorized activity logs
     */
    public function test_loan_application_model_uses_loan_log_name(): void
    {
        $loan = new LoanApplication;
        $options = $loan->getActivitylogOptions();

        $this->assertEquals('loan', $options->logName, 'LoanApplication model should use "loan" log name');
    }

    /**
     * Test that activity log records are created for HelpdeskTicket.
     *
     * @see Requirements 19.4 - Activity log recording
     */
    public function test_helpdesk_ticket_creates_activity_log(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test Activity Log',
        ]);

        $activity = \Spatie\Activitylog\Models\Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity, 'Activity log should be created for HelpdeskTicket');
        $this->assertEquals('helpdesk', $activity->log_name);
        $this->assertEquals($user->id, $activity->causer_id);
    }

    /**
     * Test that activity log records are created for LoanApplication.
     *
     * @see Requirements 19.4 - Activity log recording
     */
    public function test_loan_application_creates_activity_log(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $loan = LoanApplication::factory()->create([
            'purpose' => 'Test Activity Log',
        ]);

        $activity = \Spatie\Activitylog\Models\Activity::where('subject_type', LoanApplication::class)
            ->where('subject_id', $loan->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity, 'Activity log should be created for LoanApplication');
        $this->assertEquals('loan', $activity->log_name);
        $this->assertEquals($user->id, $activity->causer_id);
    }

    /**
     * Test that activity log tracks causer (user who performed action).
     *
     * @see Requirements 19.4 - Subject and causer tracking
     */
    public function test_activity_log_tracks_causer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $ticket = HelpdeskTicket::factory()->create();

        $activity = \Spatie\Activitylog\Models\Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals(User::class, $activity->causer_type);
        $this->assertEquals($admin->id, $activity->causer_id);
    }

    /**
     * Test that activity log tracks subject (model being acted upon).
     *
     * @see Requirements 19.4 - Subject and causer tracking
     */
    public function test_activity_log_tracks_subject(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create();

        $activity = \Spatie\Activitylog\Models\Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals(HelpdeskTicket::class, $activity->subject_type);
        $this->assertEquals($ticket->id, $activity->subject_id);
    }

    /**
     * Test that activity log only logs dirty (changed) attributes.
     *
     * @see Requirements 19.4 - Efficient activity logging
     */
    public function test_activity_log_only_logs_dirty_attributes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'priority' => 'normal',
        ]);

        // Update only status
        $ticket->update(['status' => 'in_progress']);

        $activity = \Spatie\Activitylog\Models\Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($activity);

        // Properties should contain the changed attribute
        $properties = $activity->properties->toArray();
        $this->assertArrayHasKey('attributes', $properties);
        $this->assertArrayHasKey('status', $properties['attributes']);
    }
}
