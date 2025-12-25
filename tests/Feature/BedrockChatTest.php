<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\BedrockChat;
use App\Models\BedrockConversation;
use App\Services\BedrockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BedrockChatTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function send_creates_conversation_and_appends_assistant_message(): void
    {
        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Jawapan ujian',
                'usage' => ['output_tokens' => 10],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::test(BedrockChat::class)
            ->set('model', 'sonnet')
            ->set('prompt', 'Apa khabar?')
            ->call('send')
            ->assertSet('prompt', '');

        $this->assertDatabaseCount('bedrock_conversations', 1);

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame('Apa khabar?', $conversation->messages[0]['content']);
        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertSame('Jawapan ujian', $conversation->messages[1]['content']);
    }

    #[Test]
    public function send_adds_bahasa_melayu_error_message_when_bedrock_fails(): void
    {
        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => false,
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::test(BedrockChat::class)
            ->set('model', 'sonnet')
            ->set('prompt', 'Ujian gagal')
            ->call('send');

        $conversation = BedrockConversation::query()->firstOrFail();

        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertStringContainsString('Maaf', $conversation->messages[1]['content']);
    }

    #[Test]
    public function nova_models_are_supported(): void
    {
        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Nova model response',
                'usage' => ['output_tokens' => 15],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::test(BedrockChat::class)
            ->set('model', 'nova_micro')
            ->set('prompt', 'Test Nova Micro')
            ->call('send')
            ->assertSet('prompt', '');

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame('Test Nova Micro', $conversation->messages[0]['content']);
        $this->assertSame('Nova model response', $conversation->messages[1]['content']);
        $this->assertSame('nova_micro', $conversation->messages[1]['model']);
    }

    #[Test]
    public function titan_models_are_supported(): void
    {
        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => true,
                'content' => 'Titan model response',
                'usage' => ['output_tokens' => 20],
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::test(BedrockChat::class)
            ->set('model', 'titan_text_lite')
            ->set('prompt', 'Test Titan Text Lite')
            ->call('send')
            ->assertSet('prompt', '');

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame('Test Titan Text Lite', $conversation->messages[0]['content']);
        $this->assertSame('Titan model response', $conversation->messages[1]['content']);
        $this->assertSame('titan_text_lite', $conversation->messages[1]['model']);
    }
}
