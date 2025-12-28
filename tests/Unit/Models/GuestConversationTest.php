<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\GuestConversation;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for GuestConversation Model
 *
 * @requirements 1.7, 3.6
 *
 * @compliance D09 v3.6.0 Dual Audit System, True Hybrid Architecture
 */
class GuestConversationTest extends TestCase
{
    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $conversation = new GuestConversation;

        $expected = [
            'session_id',
            'email',
            'conversation_history',
            'claimed_by_user_id',
            'claimed_at',
            'expires_at',
        ];

        $this->assertEquals($expected, $conversation->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $conversation = new GuestConversation;

        $casts = $conversation->getCasts();

        $this->assertEquals('array', $casts['conversation_history']);
        $this->assertEquals('datetime', $casts['claimed_at']);
        $this->assertEquals('datetime', $casts['expires_at']);
    }

    #[Test]
    public function it_belongs_to_a_claimed_by_user(): void
    {
        $user = User::factory()->create();
        $conversation = GuestConversation::factory()->create(['claimed_by_user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $conversation->claimedByUser);
        $this->assertEquals($user->id, $conversation->claimedByUser->id);
    }

    #[Test]
    public function it_can_have_null_claimed_by_user(): void
    {
        $conversation = GuestConversation::factory()->create(['claimed_by_user_id' => null]);

        $this->assertNull($conversation->claimed_by_user_id);
        // Should return default user model due to withDefault()
        $this->assertNotNull($conversation->claimedByUser);
    }

    #[Test]
    public function it_can_scope_unclaimed_conversations(): void
    {
        $user = User::factory()->create();
        GuestConversation::factory()->create(['claimed_by_user_id' => null]);
        GuestConversation::factory()->create(['claimed_by_user_id' => $user->id]);
        GuestConversation::factory()->create(['claimed_by_user_id' => null]);

        $unclaimed = GuestConversation::unclaimed()->get();

        $this->assertCount(2, $unclaimed);
        foreach ($unclaimed as $conversation) {
            $this->assertNull($conversation->claimed_by_user_id);
        }
    }

    #[Test]
    public function it_can_scope_claimed_conversations(): void
    {
        $user = User::factory()->create();
        GuestConversation::factory()->create(['claimed_by_user_id' => null]);
        GuestConversation::factory()->create(['claimed_by_user_id' => $user->id]);

        $claimed = GuestConversation::claimed()->get();

        $this->assertCount(1, $claimed);
        $this->assertNotNull($claimed->first()->claimed_by_user_id);
    }

    #[Test]
    public function it_can_scope_active_conversations(): void
    {
        GuestConversation::factory()->create(['expires_at' => now()->addHours(1)]);
        GuestConversation::factory()->create(['expires_at' => now()->subHours(1)]);

        $active = GuestConversation::active()->get();

        $this->assertCount(1, $active);
        $this->assertTrue($active->first()->expires_at->isFuture());
    }

    #[Test]
    public function it_can_scope_expired_conversations(): void
    {
        GuestConversation::factory()->create(['expires_at' => now()->addHours(1)]);
        GuestConversation::factory()->create(['expires_at' => now()->subHours(1)]);

        $expired = GuestConversation::expired()->get();

        $this->assertCount(1, $expired);
        $this->assertTrue($expired->first()->expires_at->isPast());
    }

    #[Test]
    public function it_can_filter_by_email(): void
    {
        GuestConversation::factory()->create(['email' => 'test1@motac.gov.my']);
        GuestConversation::factory()->create(['email' => 'test2@motac.gov.my']);

        $conversations = GuestConversation::byEmail('test1@motac.gov.my')->get();

        $this->assertCount(1, $conversations);
        $this->assertEquals('test1@motac.gov.my', $conversations->first()->email);
    }

    #[Test]
    public function it_can_filter_by_session(): void
    {
        GuestConversation::factory()->create(['session_id' => 'session-123']);
        GuestConversation::factory()->create(['session_id' => 'session-456']);

        $conversations = GuestConversation::bySession('session-123')->get();

        $this->assertCount(1, $conversations);
        $this->assertEquals('session-123', $conversations->first()->session_id);
    }

    #[Test]
    public function it_checks_if_claimed(): void
    {
        $user = User::factory()->create();
        $claimedConversation = GuestConversation::factory()->create(['claimed_by_user_id' => $user->id]);
        $unclaimedConversation = GuestConversation::factory()->create(['claimed_by_user_id' => null]);

        $this->assertTrue($claimedConversation->is_claimed);
        $this->assertFalse($unclaimedConversation->is_claimed);
    }

    #[Test]
    public function it_checks_if_active(): void
    {
        $activeConversation = GuestConversation::factory()->create(['expires_at' => now()->addHours(1)]);
        $expiredConversation = GuestConversation::factory()->create(['expires_at' => now()->subHours(1)]);

        $this->assertTrue($activeConversation->is_active);
        $this->assertFalse($expiredConversation->is_active);
    }

    #[Test]
    public function it_counts_messages_in_conversation(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Hello'],
            ['role' => 'assistant', 'content' => 'Hi there'],
            ['role' => 'user', 'content' => 'How are you?'],
        ];

        $conversation = GuestConversation::factory()->create(['conversation_history' => $history]);

        $this->assertEquals(3, $conversation->message_count);
    }

    #[Test]
    public function it_returns_zero_for_empty_conversation_history(): void
    {
        $conversation = GuestConversation::factory()->create(['conversation_history' => []]);

        $this->assertEquals(0, $conversation->message_count);
    }

    #[Test]
    public function it_returns_zero_for_null_conversation_history(): void
    {
        $conversation = GuestConversation::factory()->create(['conversation_history' => null]);

        $this->assertEquals(0, $conversation->message_count);
    }

    #[Test]
    public function it_can_be_claimed_by_user(): void
    {
        $user = User::factory()->create();
        $conversation = GuestConversation::factory()->create([
            'claimed_by_user_id' => null,
            'expires_at' => now()->addHours(1),
        ]);

        $result = $conversation->claimByUser($user);

        $this->assertTrue($result);
        $fresh = $conversation->fresh();
        $this->assertEquals($user->id, $fresh->claimed_by_user_id);
        $this->assertNotNull($fresh->claimed_at);
    }

    #[Test]
    public function it_cannot_be_claimed_if_already_claimed(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = GuestConversation::factory()->create([
            'claimed_by_user_id' => $user1->id,
            'expires_at' => now()->addHours(1),
        ]);

        $result = $conversation->claimByUser($user2);

        $this->assertFalse($result);
        $this->assertEquals($user1->id, $conversation->fresh()->claimed_by_user_id);
    }

    #[Test]
    public function it_cannot_be_claimed_if_expired(): void
    {
        $user = User::factory()->create();
        $conversation = GuestConversation::factory()->create([
            'claimed_by_user_id' => null,
            'expires_at' => now()->subHours(1),
        ]);

        $result = $conversation->claimByUser($user);

        $this->assertFalse($result);
        $this->assertNull($conversation->fresh()->claimed_by_user_id);
    }

    #[Test]
    public function it_can_add_message_to_conversation(): void
    {
        $conversation = GuestConversation::factory()->create(['conversation_history' => []]);

        $conversation->addMessage('user', 'Hello, I need help');

        $fresh = $conversation->fresh();
        $this->assertCount(1, $fresh->conversation_history);
        $this->assertEquals('user', $fresh->conversation_history[0]['role']);
        $this->assertEquals('Hello, I need help', $fresh->conversation_history[0]['content']);
        $this->assertArrayHasKey('timestamp', $fresh->conversation_history[0]);
    }

    #[Test]
    public function it_can_extend_expiry(): void
    {
        $originalExpiry = now()->addMinutes(5);
        $conversation = GuestConversation::factory()->create(['expires_at' => $originalExpiry]);

        $conversation->extendExpiry();

        $fresh = $conversation->fresh();
        $this->assertTrue($fresh->expires_at->isAfter($originalExpiry));
        $this->assertTrue($fresh->expires_at->isAfter(now()->addMinutes(25)));
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $conversation = new GuestConversation;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $conversation);
    }

    #[Test]
    public function it_uses_logs_activity_trait(): void
    {
        $conversation = new GuestConversation;

        $this->assertTrue(method_exists($conversation, 'getActivitylogOptions'));
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $conversation = new GuestConversation;

        $this->assertEquals('guest_conversations', $conversation->getTable());
    }

    #[Test]
    public function it_stores_conversation_history_as_array(): void
    {
        $history = [
            ['role' => 'user', 'content' => 'Question 1'],
            ['role' => 'assistant', 'content' => 'Answer 1'],
        ];

        $conversation = GuestConversation::factory()->create(['conversation_history' => $history]);

        $this->assertEquals($history, $conversation->fresh()->conversation_history);
        $this->assertIsArray($conversation->fresh()->conversation_history);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $conversation = GuestConversation::factory()->create();

        $this->assertNotNull($conversation->created_at);
        $this->assertNotNull($conversation->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $conversation->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $conversation->updated_at);
    }
}
