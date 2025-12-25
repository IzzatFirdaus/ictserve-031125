<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\ApprovalEmailToken;
use App\Models\AutoReplyDraft;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for ApprovalEmailToken Model
 *
 * @requirements 4.3, 4.4, 4.5
 *
 * @compliance D09 v3.6.0 Dual Audit System
 */
class ApprovalEmailTokenTest extends TestCase
{
    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $token = new ApprovalEmailToken;

        $expected = [
            'auto_reply_draft_id',
            'token',
            'action',
            'expires_at',
            'used',
            'used_at',
            'used_by_ip',
        ];

        $this->assertEquals($expected, $token->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $token = new ApprovalEmailToken;

        $casts = $token->getCasts();

        $this->assertEquals('datetime', $casts['expires_at']);
        $this->assertEquals('boolean', $casts['used']);
        $this->assertEquals('datetime', $casts['used_at']);
    }

    #[Test]
    public function it_belongs_to_auto_reply_draft(): void
    {
        $draft = AutoReplyDraft::factory()->create();
        $token = ApprovalEmailToken::factory()->create(['auto_reply_draft_id' => $draft->id]);

        $this->assertInstanceOf(AutoReplyDraft::class, $token->autoReplyDraft);
        $this->assertEquals($draft->id, $token->autoReplyDraft->id);
    }

    #[Test]
    public function it_can_scope_unused_tokens(): void
    {
        ApprovalEmailToken::factory()->create(['used' => false]);
        ApprovalEmailToken::factory()->create(['used' => true]);
        ApprovalEmailToken::factory()->create(['used' => false]);

        $unused = ApprovalEmailToken::unused()->get();

        $this->assertCount(2, $unused);
        foreach ($unused as $token) {
            $this->assertFalse($token->used);
        }
    }

    #[Test]
    public function it_can_scope_used_tokens(): void
    {
        ApprovalEmailToken::factory()->create(['used' => false]);
        ApprovalEmailToken::factory()->create(['used' => true]);

        $used = ApprovalEmailToken::used()->get();

        $this->assertCount(1, $used);
        $this->assertTrue($used->first()->used);
    }

    #[Test]
    public function it_can_scope_valid_tokens(): void
    {
        ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => false,
        ]);
        ApprovalEmailToken::factory()->create([
            'expires_at' => now()->subDays(1),
            'used' => false,
        ]);
        ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => true,
        ]);

        $valid = ApprovalEmailToken::valid()->get();

        $this->assertCount(1, $valid);
        $token = $valid->first();
        $this->assertFalse($token->used);
        $this->assertTrue($token->expires_at->isFuture());
    }

    #[Test]
    public function it_can_scope_expired_tokens(): void
    {
        ApprovalEmailToken::factory()->create(['expires_at' => now()->addDays(1)]);
        ApprovalEmailToken::factory()->create(['expires_at' => now()->subDays(1)]);

        $expired = ApprovalEmailToken::expired()->get();

        $this->assertCount(1, $expired);
        $this->assertTrue($expired->first()->expires_at->isPast());
    }

    #[Test]
    public function it_can_filter_by_token(): void
    {
        $tokenValue = 'test-token-123';
        ApprovalEmailToken::factory()->create(['token' => $tokenValue]);
        ApprovalEmailToken::factory()->create(['token' => 'other-token']);

        $results = ApprovalEmailToken::byToken($tokenValue)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($tokenValue, $results->first()->token);
    }

    #[Test]
    public function it_can_filter_by_action(): void
    {
        ApprovalEmailToken::factory()->create(['action' => 'approve']);
        ApprovalEmailToken::factory()->create(['action' => 'reject']);
        ApprovalEmailToken::factory()->create(['action' => 'approve']);

        $approveTokens = ApprovalEmailToken::byAction('approve')->get();

        $this->assertCount(2, $approveTokens);
        foreach ($approveTokens as $token) {
            $this->assertEquals('approve', $token->action);
        }
    }

    #[Test]
    public function it_checks_if_token_is_valid(): void
    {
        $validToken = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => false,
        ]);

        $expiredToken = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->subDays(1),
            'used' => false,
        ]);

        $usedToken = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => true,
        ]);

        $this->assertTrue($validToken->is_valid);
        $this->assertFalse($expiredToken->is_valid);
        $this->assertFalse($usedToken->is_valid);
    }

    #[Test]
    public function it_checks_if_token_is_expired(): void
    {
        $expiredToken = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->subDays(1),
        ]);

        $validToken = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
        ]);

        $this->assertTrue($expiredToken->is_expired);
        $this->assertFalse($validToken->is_expired);
    }

    #[Test]
    public function it_gets_action_label_in_malay(): void
    {
        $approveToken = ApprovalEmailToken::factory()->create(['action' => 'approve']);
        $rejectToken = ApprovalEmailToken::factory()->create(['action' => 'reject']);
        $customToken = ApprovalEmailToken::factory()->create(['action' => 'custom']);

        $this->assertEquals('Luluskan', $approveToken->action_label);
        $this->assertEquals('Tolak', $rejectToken->action_label);
        $this->assertEquals('Custom', $customToken->action_label);
    }

    #[Test]
    public function it_can_use_valid_token(): void
    {
        $token = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => false,
        ]);

        $ipAddress = '192.168.1.1';
        $result = $token->use($ipAddress);

        $this->assertTrue($result);
        $fresh = $token->fresh();
        $this->assertTrue($fresh->used);
        $this->assertNotNull($fresh->used_at);
        $this->assertEquals($ipAddress, $fresh->used_by_ip);
    }

    #[Test]
    public function it_cannot_use_expired_token(): void
    {
        $token = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->subDays(1),
            'used' => false,
        ]);

        $result = $token->use('192.168.1.1');

        $this->assertFalse($result);
        $this->assertFalse($token->fresh()->used);
    }

    #[Test]
    public function it_cannot_use_already_used_token(): void
    {
        $token = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => true,
        ]);

        $result = $token->use('192.168.1.1');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_use_token_without_ip_address(): void
    {
        $token = ApprovalEmailToken::factory()->create([
            'expires_at' => now()->addDays(1),
            'used' => false,
        ]);

        $result = $token->use();

        $this->assertTrue($result);
        $fresh = $token->fresh();
        $this->assertTrue($fresh->used);
        $this->assertNull($fresh->used_by_ip);
    }

    #[Test]
    public function it_generates_secure_token(): void
    {
        $token1 = ApprovalEmailToken::generateSecureToken();
        $token2 = ApprovalEmailToken::generateSecureToken();

        $this->assertIsString($token1);
        $this->assertIsString($token2);
        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(64, strlen($token1)); // SHA-256 hash length
    }

    #[Test]
    public function it_creates_token_for_draft(): void
    {
        $draft = AutoReplyDraft::factory()->create();

        $token = ApprovalEmailToken::createForDraft($draft, 'approve', 5);

        $this->assertEquals($draft->id, $token->auto_reply_draft_id);
        $this->assertEquals('approve', $token->action);
        $this->assertNotNull($token->token);
        $this->assertTrue($token->expires_at->isAfter(now()->addDays(4)));
        $this->assertTrue($token->expires_at->isBefore(now()->addDays(6)));
    }

    #[Test]
    public function it_creates_token_with_default_validity(): void
    {
        $draft = AutoReplyDraft::factory()->create();

        $token = ApprovalEmailToken::createForDraft($draft, 'reject');

        $this->assertTrue($token->expires_at->isAfter(now()->addDays(6)));
        $this->assertTrue($token->expires_at->isBefore(now()->addDays(8)));
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $token = new ApprovalEmailToken;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $token);
    }

    #[Test]
    public function it_uses_logs_activity_trait(): void
    {
        $token = new ApprovalEmailToken;

        $this->assertTrue(method_exists($token, 'getActivitylogOptions'));
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $token = new ApprovalEmailToken;

        $this->assertEquals('approval_email_tokens', $token->getTable());
    }

    #[Test]
    public function it_has_correct_activity_log_configuration(): void
    {
        $token = new ApprovalEmailToken;
        $options = $token->getActivitylogOptions();

        $this->assertInstanceOf(\Spatie\Activitylog\LogOptions::class, $options);
    }

    #[Test]
    public function it_can_handle_null_used_at(): void
    {
        $token = ApprovalEmailToken::factory()->create(['used_at' => null]);

        $this->assertNull($token->fresh()->used_at);
    }

    #[Test]
    public function it_can_handle_null_used_by_ip(): void
    {
        $token = ApprovalEmailToken::factory()->create(['used_by_ip' => null]);

        $this->assertNull($token->fresh()->used_by_ip);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $token = ApprovalEmailToken::factory()->create();

        $this->assertNotNull($token->created_at);
        $this->assertNotNull($token->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $token->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $token->updated_at);
    }
}
