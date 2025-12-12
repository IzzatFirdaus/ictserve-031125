<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MessageLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for MessageLog Model
 *
 * @requirements 4.1, 4.2, 4.6, 6.5, 1.7, 3.6
 *
 * @compliance D09 v3.6.0 Dual Audit System
 */
class MessageLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $messageLog = new MessageLog;

        $expected = [
            'request_id',
            'operation_type',
            'user_id',
            'sanitized_input',
            'response_summary',
            'metadata',
            'hash',
            'previous_hash',
            'processed_at',
        ];

        $this->assertEquals($expected, $messageLog->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $messageLog = new MessageLog;

        $casts = $messageLog->getCasts();

        $this->assertEquals('array', $casts['metadata']);
        $this->assertEquals('datetime', $casts['processed_at']);
    }

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $messageLog = MessageLog::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $messageLog->user);
        $this->assertEquals($user->id, $messageLog->user->id);
    }

    #[Test]
    public function it_can_have_null_user_for_true_hybrid_architecture(): void
    {
        $messageLog = MessageLog::factory()->create(['user_id' => null]);

        $this->assertNull($messageLog->user_id);
        // Should return default user model due to withDefault()
        $this->assertNotNull($messageLog->user);
    }

    #[Test]
    public function it_can_filter_by_operation_type(): void
    {
        MessageLog::factory()->create(['operation_type' => 'faq_query']);
        MessageLog::factory()->create(['operation_type' => 'document_analysis']);
        MessageLog::factory()->create(['operation_type' => 'faq_query']);

        $faqLogs = MessageLog::byOperationType('faq_query')->get();

        $this->assertCount(2, $faqLogs);
        foreach ($faqLogs as $log) {
            $this->assertEquals('faq_query', $log->operation_type);
        }
    }

    #[Test]
    public function it_can_filter_by_date_range(): void
    {
        $startDate = now()->subDays(7);
        $endDate = now()->subDays(1);

        MessageLog::factory()->create(['processed_at' => now()->subDays(10)]);
        MessageLog::factory()->create(['processed_at' => now()->subDays(5)]);
        MessageLog::factory()->create(['processed_at' => now()]);

        $logs = MessageLog::byDateRange($startDate, $endDate)->get();

        $this->assertCount(1, $logs);
        $this->assertTrue($logs->first()->processed_at->between($startDate, $endDate));
    }

    #[Test]
    public function it_gets_user_display_name_for_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'Ahmad Ali']);
        $messageLog = MessageLog::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('Ahmad Ali', $messageLog->user_display_name);
    }

    #[Test]
    public function it_gets_guest_display_name_for_null_user(): void
    {
        $messageLog = MessageLog::factory()->create(['user_id' => null]);

        $this->assertEquals('Tetamu', $messageLog->user_display_name);
    }

    #[Test]
    public function it_converts_hash_to_lowercase(): void
    {
        $messageLog = MessageLog::factory()->create(['hash' => 'ABCDEF123456']);

        $this->assertEquals('abcdef123456', $messageLog->fresh()->hash);
    }

    #[Test]
    public function it_converts_previous_hash_to_lowercase(): void
    {
        $messageLog = MessageLog::factory()->create(['previous_hash' => 'FEDCBA654321']);

        $this->assertEquals('fedcba654321', $messageLog->fresh()->previous_hash);
    }

    #[Test]
    public function it_handles_null_previous_hash(): void
    {
        $messageLog = MessageLog::factory()->create(['previous_hash' => null]);

        $this->assertNull($messageLog->fresh()->previous_hash);
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $messageLog = new MessageLog;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $messageLog);
    }

    #[Test]
    public function it_uses_logs_activity_trait(): void
    {
        $messageLog = new MessageLog;

        $this->assertTrue(method_exists($messageLog, 'getActivitylogOptions'));
    }

    #[Test]
    public function it_stores_metadata_as_array(): void
    {
        $metadata = [
            'model' => 'llama3.1',
            'tokens' => 150,
            'processing_time' => 2.5,
        ];

        $messageLog = MessageLog::factory()->create(['metadata' => $metadata]);

        $this->assertEquals($metadata, $messageLog->fresh()->metadata);
        $this->assertIsArray($messageLog->fresh()->metadata);
    }

    #[Test]
    public function it_stores_processed_at_as_datetime(): void
    {
        $processedAt = now()->subHours(2);
        $messageLog = MessageLog::factory()->create(['processed_at' => $processedAt]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $messageLog->fresh()->processed_at);
        $this->assertEquals($processedAt->format('Y-m-d H:i:s'), $messageLog->fresh()->processed_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $messageLog = new MessageLog;

        $this->assertEquals('message_logs', $messageLog->getTable());
    }

    #[Test]
    public function it_has_correct_activity_log_configuration(): void
    {
        $messageLog = new MessageLog;
        $options = $messageLog->getActivitylogOptions();

        $this->assertInstanceOf(\Spatie\Activitylog\LogOptions::class, $options);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $messageLog = MessageLog::factory()->create();

        $this->assertNotNull($messageLog->created_at);
        $this->assertNotNull($messageLog->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $messageLog->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $messageLog->updated_at);
    }

    #[Test]
    public function it_can_handle_complex_metadata(): void
    {
        $complexMetadata = [
            'request' => [
                'model' => 'llama3.1',
                'temperature' => 0.7,
                'max_tokens' => 500,
            ],
            'response' => [
                'tokens_used' => 150,
                'finish_reason' => 'stop',
            ],
            'performance' => [
                'processing_time_ms' => 2500,
                'cache_hit' => true,
            ],
        ];

        $messageLog = MessageLog::factory()->create(['metadata' => $complexMetadata]);

        $this->assertEquals($complexMetadata, $messageLog->fresh()->metadata);
        $this->assertEquals('llama3.1', $messageLog->fresh()->metadata['request']['model']);
        $this->assertTrue($messageLog->fresh()->metadata['performance']['cache_hit']);
    }
}
