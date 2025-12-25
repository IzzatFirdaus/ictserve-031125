<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\BedrockChat;
use App\Models\BedrockConversation;
use App\Models\User;
use App\Services\BedrockService;
use App\Services\DlpFilteringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * BedrockChat Livewire Component Tests
 *
 * PKS 5.2.1 Compliance: All tests use authenticated users
 * PKS 9.2.1 Compliance: DLP filtering is mocked for test isolation
 */
class BedrockChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up DLP mock for all tests to ensure consistent behavior
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mock DLP service to return PUBLIC classification for test isolation
        $dlpMock = $this->createMock(DlpFilteringService::class);
        $dlpMock->method('classifyData')
            ->willReturn([
                'classification' => DlpFilteringService::CLASSIFICATION_PUBLIC,
                'routing_decision' => DlpFilteringService::ROUTE_CLOUD_ALLOWED,
                'risk_score' => 0,
                'detected_patterns' => [],
                'processing_time_ms' => 1.0,
                'user_id' => null,
                'content_length' => 100,
                'timestamp' => now()->toISOString(),
            ]);

        $this->instance(DlpFilteringService::class, $dlpMock);
    }

    #[Test]
    public function send_creates_conversation_and_appends_assistant_message(): void
    {
        // PKS 5.2.1 - Create authenticated user
        $user = User::factory()->create();

        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Jawapan ujian',
                'usage' => ['output_tokens' => 10],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('model', 'sonnet')
            ->set('prompt', 'Apa khabar?')
            ->call('send')
            ->assertSet('prompt', '');

        $this->assertDatabaseCount('bedrock_conversations', 1);

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame($user->id, $conversation->user_id); // PKS 5.2.1 - Verify user_id linkage
        $this->assertSame('Apa khabar?', $conversation->messages[0]['content']);
        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertSame('Jawapan ujian', $conversation->messages[1]['content']);
    }

    #[Test]
    public function send_adds_bahasa_melayu_error_message_when_bedrock_fails(): void
    {
        // PKS 5.2.1 - Create authenticated user
        $user = User::factory()->create();

        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => false,
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('model', 'sonnet')
            ->set('prompt', 'Ujian gagal')
            ->call('send');

        $conversation = BedrockConversation::query()->firstOrFail();

        $this->assertSame($user->id, $conversation->user_id); // PKS 5.2.1 - Verify user_id linkage
        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertStringContainsString('Maaf', $conversation->messages[1]['content']);
    }

    #[Test]
    public function nova_models_are_supported(): void
    {
        // PKS 5.2.1 - Create authenticated user
        $user = User::factory()->create();

        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Nova model response',
                'usage' => ['output_tokens' => 15],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('model', 'nova_micro')
            ->set('prompt', 'Test Nova Micro')
            ->call('send')
            ->assertSet('prompt', '');

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame($user->id, $conversation->user_id); // PKS 5.2.1 - Verify user_id linkage
        $this->assertSame('Test Nova Micro', $conversation->messages[0]['content']);
        $this->assertSame('Nova model response', $conversation->messages[1]['content']);
    }

    #[Test]
    public function titan_models_are_supported(): void
    {
        // PKS 5.2.1 - Create authenticated user
        $user = User::factory()->create();

        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Titan model response',
                'usage' => ['output_tokens' => 20],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::actingAs($user)
            ->test(BedrockChat::class)
            ->set('model', 'titan_text_lite')
            ->set('prompt', 'Test Titan Text Lite')
            ->call('send')
            ->assertSet('prompt', '');

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame($user->id, $conversation->user_id); // PKS 5.2.1 - Verify user_id linkage
        $this->assertSame('Test Titan Text Lite', $conversation->messages[0]['content']);
        $this->assertSame('Titan model response', $conversation->messages[1]['content']);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_chat(): void
    {
        // PKS 5.2.1 - Verify unauthenticated access is blocked
        Livewire::test(BedrockChat::class)
            ->assertRedirect(route('login'));
    }
}
