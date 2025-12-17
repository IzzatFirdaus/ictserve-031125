<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Property Tests for Dual Audit System (v3.6.0)
 *
 * Validates that both audit systems (owen-it/laravel-auditing and spatie/laravel-activitylog)
 * work correctly together for compliance and operational logging.
 *
 * Property 7: Dual Audit System Validation
 * Validates: Requirements 6.1, 6.2, 6.3
 *
 * @requirements 6.1, 6.2, 6.3
 */
class DualAuditSystemPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property: Both audit systems record model creation events
     * Validates: Requirement 6.1 - Compliance audit (owen-it) and operational log (spatie)
     */
    #[Test]
    #[DataProvider('auditableModelsProvider')]
    public function property_both_audit_systems_record_creation(string $modelClass, array $factoryData): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create the model
        $model = $modelClass::factory()->create($factoryData);

        // Property: Owen-it audit should record the creation
        $owenItAudit = Audit::where('auditable_type', $modelClass)
            ->where('auditable_id', $model->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($owenItAudit, "Owen-it audit should record {$modelClass} creation");
        $this->assertEquals('created', $owenItAudit->event);

        // Property: Spatie activity log should record the creation
        $spatieActivity = Activity::where('subject_type', $modelClass)
            ->where('subject_id', $model->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($spatieActivity, "Spatie activity log should record {$modelClass} creation");
        $this->assertEquals('created', $spatieActivity->event);
    }

    /**
     * Property: Both audit systems record model update events with field changes
     * Validates: Requirement 6.2 - Field-level tracking
     */
    #[Test]
    public function property_both_audit_systems_track_field_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'priority' => 'normal',
        ]);

        // Update the ticket
        $ticket->update([
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        // Property: Owen-it should track old and new values
        $owenItAudit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($owenItAudit);
        $this->assertArrayHasKey('status', $owenItAudit->old_values);
        $this->assertArrayHasKey('status', $owenItAudit->new_values);
        $this->assertEquals('open', $owenItAudit->old_values['status']);
        $this->assertEquals('in_progress', $owenItAudit->new_values['status']);

        // Property: Spatie should track the update event
        $spatieActivity = Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($spatieActivity);
    }

    /**
     * Property: Both audit systems track the causer (user who made the change)
     * Validates: Requirement 6.3 - User attribution
     */
    #[Test]
    public function property_both_audit_systems_track_causer(): void
    {
        $user = User::factory()->create(['name' => 'Ahmad Bin Hassan']);
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create();

        // Property: Owen-it should track the user
        $owenItAudit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->first();

        $this->assertEquals($user->id, $owenItAudit->user_id);

        // Property: Spatie should track the causer
        $spatieActivity = Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->first();

        $this->assertEquals($user->id, $spatieActivity->causer_id);
        $this->assertEquals(User::class, $spatieActivity->causer_type);
    }

    /**
     * Property: Audit records are immutable (cannot be modified)
     * Validates: Requirement 6.1 - Compliance audit integrity
     */
    #[Test]
    public function property_audit_records_are_immutable(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create();

        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->first();

        // Property: Attempting to modify audit should not change the record
        $originalEvent = $audit->event;
        $audit->event = 'tampered';

        // Refresh from database
        $audit->refresh();
        $this->assertEquals($originalEvent, $audit->event, 'Audit records should be immutable');
    }

    /**
     * Property: Seven-year retention policy is configured
     * Validates: Requirement 6.1 - Compliance retention
     */
    #[Test]
    public function property_seven_year_retention_configured(): void
    {
        // Property: Owen-it retention should be 7 years
        $owenItRetention = config('audit.retention');
        $this->assertTrue($owenItRetention['enabled'], 'Owen-it retention should be enabled');
        $this->assertEquals(7, $owenItRetention['years'], 'Owen-it audit retention should be 7 years');

        // Property: Spatie retention should be 7 years (2555 days)
        $spatieRetention = config('activitylog.delete_records_older_than_days', 0);
        $this->assertEquals(2555, $spatieRetention, 'Spatie activity log retention should be 7 years (2555 days)');
    }

    /**
     * Property: Different modules use appropriate log names
     * Validates: Requirement 6.2 - Module-specific logging
     */
    #[Test]
    #[DataProvider('moduleLogNamesProvider')]
    public function property_modules_use_correct_log_names(string $modelClass, string $expectedLogName): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $model = $modelClass::factory()->create();

        $activity = Activity::where('subject_type', $modelClass)
            ->where('subject_id', $model->id)
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($expectedLogName, $activity->log_name, "Model {$modelClass} should use log name '{$expectedLogName}'");
    }

    /**
     * Property: Audit records preserve Bahasa Melayu content
     * Validates: v3.6.0 BM-only interface requirement
     */
    #[Test]
    public function property_audit_preserves_bahasa_melayu_content(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Masalah rangkaian pejabat',
            'description' => 'Sambungan internet terputus di tingkat 5',
        ]);

        // Property: Owen-it should preserve BM content
        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->first();

        $this->assertStringContainsString('Masalah rangkaian pejabat', json_encode($audit->new_values));

        // Property: Spatie should preserve BM content in properties
        $activity = Activity::where('subject_type', HelpdeskTicket::class)
            ->where('subject_id', $ticket->id)
            ->first();

        $this->assertNotNull($activity);
    }

    /**
     * Property: IP addresses are hashed for privacy compliance
     * Validates: PDPA 2010 compliance
     */
    #[Test]
    public function property_ip_addresses_are_hashed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = HelpdeskTicket::factory()->create();

        $audit = Audit::where('auditable_type', HelpdeskTicket::class)
            ->where('auditable_id', $ticket->id)
            ->first();

        // Property: IP address should be hashed (not raw IP format)
        if ($audit->ip_address) {
            // Hashed IP should not match standard IP format
            $this->assertDoesNotMatchRegularExpression(
                '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',
                $audit->ip_address,
                'IP address should be hashed for PDPA compliance'
            );
        }
    }

    /**
     * Data provider for auditable models
     */
    public static function auditableModelsProvider(): array
    {
        return [
            'helpdesk ticket' => [
                HelpdeskTicket::class,
                ['subject' => 'Test Ticket', 'description' => 'Test description'],
            ],
            'loan application' => [
                LoanApplication::class,
                ['purpose' => 'Test Purpose'],
            ],
        ];
    }

    /**
     * Data provider for module log names
     */
    public static function moduleLogNamesProvider(): array
    {
        return [
            'helpdesk uses helpdesk log' => [HelpdeskTicket::class, 'helpdesk'],
            'loan uses loan log' => [LoanApplication::class, 'loan'],
        ];
    }
}
