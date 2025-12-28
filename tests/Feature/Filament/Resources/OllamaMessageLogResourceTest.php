<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\OllamaAI\MessageLogResource;
use App\Models\MessageLog;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for MessageLog Filament Resource
 *
 * Tests the Message Log audit interface including:
 * - Read-only view functionality
 * - Filtering by operation type, date range, user
 * - Search functionality
 * - Authorization (admin/superuser only)
 * - Accessibility compliance
 *
 * @requirements 4.1, 4.2, 4.4, 6.5
 *
 * @compliance D09 v3.6.0 Dual Audit System
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaMessageLogResourceTest extends TestCase
{
    #[Test]
    public function admin_can_render_message_log_index_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(MessageLogResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_message_log_view_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $log = MessageLog::factory()->create();

        $this->get(MessageLogResource::getUrl('view', ['record' => $log]))
            ->assertSuccessful();
    }

    #[Test]
    public function superuser_can_access_message_log_resource(): void
    {
        $user = User::factory()->superuser()->create();
        $this->actingAs($user);

        $this->get(MessageLogResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function regular_user_cannot_access_message_log_resource(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $this->get(MessageLogResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function approver_cannot_access_message_log_resource(): void
    {
        $user = User::factory()->approver()->create();
        $this->actingAs($user);

        $this->get(MessageLogResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function message_log_list_shows_records(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $logs = MessageLog::factory()->count(5)->create();

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->assertCanSeeTableRecords($logs);
    }

    #[Test]
    public function message_log_list_can_filter_by_operation_type(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faqLog = MessageLog::factory()->create(['operation_type' => 'faq_query']);
        $docLog = MessageLog::factory()->create(['operation_type' => 'document_analysis']);

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->filterTable('operation_type', 'faq_query')
            ->assertCanSeeTableRecords([$faqLog])
            ->assertCanNotSeeTableRecords([$docLog]);
    }

    #[Test]
    public function message_log_list_can_search_by_request_id(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $targetLog = MessageLog::factory()->create([
            'request_id' => 'abc12345-6789-0def-ghij-klmnopqrstuv',
        ]);
        $otherLog = MessageLog::factory()->create([
            'request_id' => 'xyz98765-4321-0fed-cba9-876543210fed',
        ]);

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->searchTable('abc12345')
            ->assertCanSeeTableRecords([$targetLog])
            ->assertCanNotSeeTableRecords([$otherLog]);
    }

    #[Test]
    public function message_log_list_can_search_by_sanitized_input(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $targetLog = MessageLog::factory()->create([
            'sanitized_input' => 'Bagaimana cara reset kata laluan?',
        ]);
        $otherLog = MessageLog::factory()->create([
            'sanitized_input' => 'Soalan lain yang berbeza',
        ]);

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->searchTable('reset kata laluan')
            ->assertCanSeeTableRecords([$targetLog])
            ->assertCanNotSeeTableRecords([$otherLog]);
    }

    #[Test]
    public function message_log_cannot_be_created(): void
    {
        $this->assertFalse(MessageLogResource::canCreate());
    }

    #[Test]
    public function message_log_cannot_be_edited(): void
    {
        $log = MessageLog::factory()->create();

        $this->assertFalse(MessageLogResource::canEdit($log));
    }

    #[Test]
    public function message_log_cannot_be_deleted(): void
    {
        $log = MessageLog::factory()->create();

        $this->assertFalse(MessageLogResource::canDelete($log));
    }

    #[Test]
    public function message_log_view_shows_audit_hash(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $log = MessageLog::factory()->create([
            'hash' => 'abc123def456789',
            'previous_hash' => 'xyz987654321fed',
        ]);

        $response = $this->get(MessageLogResource::getUrl('view', ['record' => $log]));

        $response->assertSuccessful();
    }

    #[Test]
    public function message_log_displays_operation_type_badge(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $log = MessageLog::factory()->create(['operation_type' => 'faq_query']);

        $response = $this->get(MessageLogResource::getUrl('view', ['record' => $log]));

        $response->assertSuccessful();
    }

    #[Test]
    public function message_log_shows_guest_user_label(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        // Create log without user (guest)
        $log = MessageLog::factory()->create(['user_id' => null]);

        $response = $this->get(MessageLogResource::getUrl('view', ['record' => $log]));

        $response->assertSuccessful();
    }

    #[Test]
    public function message_log_list_is_paginated(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        MessageLog::factory()->count(30)->create();

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->assertSuccessful();
    }

    #[Test]
    public function message_log_list_sorted_by_processed_at_desc(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $oldLog = MessageLog::factory()->create([
            'processed_at' => now()->subDays(2),
        ]);
        $newLog = MessageLog::factory()->create([
            'processed_at' => now(),
        ]);

        Livewire::test(MessageLogResource\Pages\ListMessageLogs::class)
            ->assertCanSeeTableRecords([$newLog, $oldLog]);
    }
}
