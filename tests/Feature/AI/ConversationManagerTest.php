<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\BedrockConversation;
use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for Conversation Management in Cloud Hybrid AI system.
 *
 * trace: D03-SRS-AI-005, D03-SRS-AI-006
 * trace: Phase 15.1 (Conversation Manager Tests)
 */
#[Group('ai')]
#[Group('bedrock')]
class ConversationManagerTest extends TestCase
{
    #[Test]
    public function it_creates_conversation_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $conversation = BedrockConversation::create([
            'user_id' => $user->id,
            'title' => 'Perbualan Ujian',
            'messages' => [
                ['role' => 'user', 'content' => 'Soalan pertama'],
            ],
            'model' => 'haiku',
            'total_tokens' => 10,
        ]);

        $this->assertDatabaseHas('bedrock_conversations', [
            'user_id' => $user->id,
            'title' => 'Perbualan Ujian',
            'model' => 'haiku',
        ]);

        $this->assertCount(1, $conversation->messages);
    }

    #[Test]
    public function it_creates_conversation_for_guest_without_user(): void
    {
        $conversation = BedrockConversation::create([
            'user_id' => null,
            'title' => 'Perbualan Tetamu',
            'messages' => [
                ['role' => 'user', 'content' => 'Soalan tetamu'],
            ],
            'model' => 'haiku',
            'total_tokens' => 5,
        ]);

        $this->assertDatabaseHas('bedrock_conversations', [
            'user_id' => null,
            'title' => 'Perbualan Tetamu',
        ]);

        $this->assertNull($conversation->user_id);
    }

    #[Test]
    public function it_appends_messages_to_existing_conversation(): void
    {
        $user = User::factory()->create();

        $conversation = BedrockConversation::create([
            'user_id' => $user->id,
            'title' => 'Perbualan',
            'messages' => [
                ['role' => 'user', 'content' => 'Soalan 1'],
            ],
            'model' => 'haiku',
            'total_tokens' => 10,
        ]);

        $messages = $conversation->messages;
        $messages[] = ['role' => 'assistant', 'content' => 'Jawapan 1'];
        $messages[] = ['role' => 'user', 'content' => 'Soalan 2'];

        $conversation->update([
            'messages' => $messages,
            'total_tokens' => 30,
        ]);

        $conversation->refresh();

        $this->assertCount(3, $conversation->messages);
        $this->assertSame('Jawapan 1', $conversation->messages[1]['content']);
    }

    #[Test]
    public function it_loads_user_conversations(): void
    {
        $user = User::factory()->create();

        BedrockConversation::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        BedrockConversation::factory()->count(2)->create([
            'user_id' => null,
        ]);

        $userConversations = BedrockConversation::where('user_id', $user->id)->get();

        $this->assertCount(3, $userConversations);
    }

    #[Test]
    public function it_deletes_conversation(): void
    {
        $user = User::factory()->create();

        $conversation = BedrockConversation::create([
            'user_id' => $user->id,
            'title' => 'Untuk Dipadam',
            'messages' => [],
            'model' => 'haiku',
            'total_tokens' => 0,
        ]);

        $conversationId = $conversation->id;
        $conversation->delete();

        $this->assertDatabaseMissing('bedrock_conversations', [
            'id' => $conversationId,
        ]);
    }
}
