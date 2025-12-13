<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\BedrockChat;
use App\Models\BedrockConversation;
use App\Services\BedrockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BedrockChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_creates_conversation_and_appends_assistant_message(): void
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
            ->set('prompt', 'Apa khabar?')
            ->call('send')
            ->assertSet('prompt', '');

        $this->assertDatabaseCount('bedrock_conversations', 1);

        $conversation = BedrockConversation::query()->firstOrFail();
        $this->assertSame('Apa khabar?', $conversation->messages[0]['content']);
        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertSame('Jawapan ujian', $conversation->messages[1]['content']);
    }

    public function test_send_adds_bahasa_melayu_error_message_when_bedrock_fails(): void
    {
        $mock = $this->createMock(BedrockService::class);
        $mock->expects($this->once())
            ->method('invoke')
            ->willReturn([
                'success' => false,
            ]);

        $this->instance(BedrockService::class, $mock);

        Livewire::test(BedrockChat::class)
            ->set('prompt', 'Ujian gagal')
            ->call('send');

        $conversation = BedrockConversation::query()->firstOrFail();

        $this->assertSame('assistant', $conversation->messages[1]['role']);
        $this->assertStringContainsString('Maaf', $conversation->messages[1]['content']);
    }
}
