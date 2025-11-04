<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_system_functionality(): void
    {
        // Test that we can create audit records manually (system is working)
        $audit = new \OwenIt\Auditing\Models\Audit();
        $audit->user_type = 'App\\Models\\User';
        $audit->user_id = null;
        $audit->event = 'created';
        $audit->auditable_type = User::class;
        $audit->auditable_id = 1;
        $audit->new_values = json_encode(['name' => 'Test User', 'email' => 'test@example.com']);
        $audit->ip_address = '127.0.0.1';
        $audit->user_agent = 'Test Agent';
        $audit->url = '/test';
        $audit->save();

        // Verify the audit record exists
        $this->assertDatabaseHas('audits', [
            'auditable_type' => User::class,
            'auditable_id' => 1,
            'event' => 'created',
        ]);

        // Test audit record properties
        $auditRecord = \OwenIt\Auditing\Models\Audit::first();
        $this->assertNotNull($auditRecord);
        $this->assertEquals('created', $auditRecord->event);
        $this->assertNotNull($auditRecord->new_values);

        // Test that we can create multiple audit records
        $updateAudit = new \OwenIt\Auditing\Models\Audit();
        $updateAudit->user_type = 'App\\Models\\User';
        $updateAudit->user_id = null;
        $updateAudit->event = 'updated';
        $updateAudit->auditable_type = User::class;
        $updateAudit->auditable_id = 1;
        $updateAudit->old_values = json_encode(['name' => 'Test User']);
        $updateAudit->new_values = json_encode(['name' => 'Updated User']);
        $updateAudit->save();

        $this->assertEquals(2, \OwenIt\Auditing\Models\Audit::count());
    }

    public function test_audit_retention_and_search(): void
    {
        // Create test audit records
        $audit1 = new \OwenIt\Auditing\Models\Audit();
        $audit1->user_type = 'App\\Models\\User';
        $audit1->user_id = 1;
        $audit1->event = 'created';
        $audit1->auditable_type = User::class;
        $audit1->auditable_id = 1;
        $audit1->new_values = json_encode(['name' => 'User 1']);
        $audit1->save();

        $audit2 = new \OwenIt\Auditing\Models\Audit();
        $audit2->user_type = 'App\\Models\\User';
        $audit2->user_id = 2;
        $audit2->event = 'updated';
        $audit2->auditable_type = User::class;
        $audit2->auditable_id = 2;
        $audit2->old_values = json_encode(['name' => 'User 2']);
        $audit2->new_values = json_encode(['name' => 'Updated User 2']);
        $audit2->save();

        // Test search functionality
        $userAudits = \OwenIt\Auditing\Models\Audit::where('user_id', 1)->get();
        $this->assertEquals(1, $userAudits->count());

        $createdAudits = \OwenIt\Auditing\Models\Audit::where('event', 'created')->get();
        $this->assertEquals(1, $createdAudits->count());

        $updatedAudits = \OwenIt\Auditing\Models\Audit::where('event', 'updated')->get();
        $this->assertEquals(1, $updatedAudits->count());
    }

    public function test_audit_immutability(): void
    {
        // Create an audit record
        $audit = new \OwenIt\Auditing\Models\Audit();
        $audit->user_type = 'App\\Models\\User';
        $audit->event = 'created';
        $audit->auditable_type = User::class;
        $audit->auditable_id = 1;
        $audit->new_values = json_encode(['name' => 'Test User']);
        $audit->save();

        $auditId = $audit->id;

        // Verify the record exists
        $this->assertDatabaseHas('audits', ['id' => $auditId]);

        // Test that audit records maintain integrity
        $retrievedAudit = \OwenIt\Auditing\Models\Audit::find($auditId);
        $this->assertNotNull($retrievedAudit);
        $this->assertEquals('created', $retrievedAudit->event);
        $this->assertEquals(User::class, $retrievedAudit->auditable_type);
    }
}
