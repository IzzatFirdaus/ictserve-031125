<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\OllamaClientContract;
use App\Models\ApprovalEmailToken;
use App\Models\AutoReplyDraft;
use App\Models\AutoReplyTemplate;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\AutoReplyService;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for AutoReplyService
 *
 * Tests auto-reply functionality including:
 * - Draft generation
 * - Template processing
 * - Approval workflow
 * - Token validation
 * - Audit logging
 *
 * @requirements 3.1, 3.2, 3.3, 3.4, 3.6
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class AutoReplyServiceTest extends TestCase
{
    use RefreshDatabase;

    private AutoReplyService $autoReplyService;

    private MockInterface $ollamaClientMock;

    private MockInterface $ragServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ollama.auto_reply' => [
                'approval_required' => true,
                'token_validity_days' => 7,
                'notification_timeout' => 60,
                'max_content_length' => 5000,
                'default_template_variables' => [
                    'system_name' => 'ICTServe',
                    'organization' => 'BPM MOTAC',
                    'support_email' => 'ict@motac.gov.my',
                ],
            ],
        ]);

        $this->ollamaClientMock = Mockery::mock(OllamaClientContract::class);
        $this->ragServiceMock = Mockery::mock(RagService::class);

        $this->autoReplyService = new AutoReplyService(
            $this->ollamaClientMock,
            $this->ragServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_generate_draft_for_helpdesk_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Masalah komputer',
            'description' => 'Komputer tidak boleh dihidupkan',
            'priority' => 'high',
        ]);

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn([
                'response' => 'Terima kasih atas laporan anda. Kami akan menyiasat masalah ini.',
            ]);

        $draft = $this->autoReplyService->generateDraft($ticket, $user->id);

        $this->assertInstanceOf(AutoReplyDraft::class, $draft);
        $this->assertEquals(HelpdeskTicket::class, $draft->replyable_type);
        $this->assertEquals($ticket->id, $draft->replyable_id);
        $this->assertEquals(AutoReplyDraft::STATUS_DRAFT, $draft->status);
        $this->assertEquals($user->id, $draft->generated_by);
        $this->assertNotEmpty($draft->draft_content);
    }

    #[Test]
    public function it_can_generate_draft_for_loan_application(): void
    {
        $user = User::factory()->create();
        $loan = LoanApplication::factory()->create([
            'purpose' => 'Mesyuarat jabatan',
        ]);

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn([
                'response' => 'Permohonan pinjaman anda sedang diproses.',
            ]);

        $draft = $this->autoReplyService->generateDraft($loan, $user->id);

        $this->assertInstanceOf(AutoReplyDraft::class, $draft);
        $this->assertEquals(LoanApplication::class, $draft->replyable_type);
        $this->assertEquals($loan->id, $draft->replyable_id);
    }

    #[Test]
    public function it_throws_exception_for_unsupported_model(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tidak disokong untuk auto-reply');

        $this->autoReplyService->generateDraft($user, 1);
    }

    #[Test]
    public function it_can_generate_draft_with_template(): void
    {
        $user = User::factory()->create();
        // PKS 5.2.1 - Use authenticated user only (no guest fields)
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test ticket',
            'user_id' => $user->id,
        ]);

        $template = AutoReplyTemplate::factory()->create([
            'name' => 'Standard Response',
            'template_content' => 'Terima kasih {{submitter_name}}. Tiket #{{ticket_id}} telah diterima.',
            'variables' => ['submitter_name', 'ticket_id'],
            'status' => 'active',
        ]);

        $draft = $this->autoReplyService->generateDraft($ticket, $user->id, $template->id);

        $this->assertInstanceOf(AutoReplyDraft::class, $draft);
        $this->assertEquals($template->id, $draft->template_id);
    }

    #[Test]
    public function it_can_submit_draft_for_approval(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn(['response' => 'Draft content']);

        $draft = $this->autoReplyService->generateDraft($ticket, $user->id);

        $result = $this->autoReplyService->submitForApproval($draft, false);

        $this->assertTrue($result);
        $draft->refresh();
        $this->assertEquals(AutoReplyDraft::STATUS_PENDING_REVIEW, $draft->status);
    }

    #[Test]
    public function it_can_approve_draft(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $result = $this->autoReplyService->approveDraft($draft, $approver);

        $this->assertTrue($result);
        $draft->refresh();
        $this->assertEquals(AutoReplyDraft::STATUS_APPROVED, $draft->status);
        $this->assertEquals($approver->id, $draft->approved_by);
        $this->assertNotNull($draft->approved_at);
    }

    #[Test]
    public function it_can_reject_draft_with_reason(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $reason = 'Kandungan tidak sesuai dengan polisi';

        $result = $this->autoReplyService->rejectDraft($draft, $approver, $reason);

        $this->assertTrue($result);
        $draft->refresh();
        $this->assertEquals(AutoReplyDraft::STATUS_REJECTED, $draft->status);
        $this->assertEquals($approver->id, $draft->approved_by);
        $this->assertEquals($reason, $draft->rejection_reason);
    }

    #[Test]
    public function it_validates_approval_token(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $token = ApprovalEmailToken::create([
            'auto_reply_draft_id' => $draft->id,
            'token' => 'valid-token-'.uniqid(),
            'action' => 'approve',
            'expires_at' => now()->addDays(7),
            'used' => false,
        ]);

        $result = $this->autoReplyService->approveDraft($draft, $approver, $token->token);

        $this->assertTrue($result);
        $token->refresh();
        $this->assertTrue($token->used);
    }

    #[Test]
    public function it_rejects_invalid_approval_token(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token kelulusan tidak sah');

        $this->autoReplyService->approveDraft($draft, $approver, 'invalid-token');
    }

    #[Test]
    public function it_rejects_expired_token(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $token = ApprovalEmailToken::create([
            'auto_reply_draft_id' => $draft->id,
            'token' => 'expired-token-123',
            'action' => 'approve',
            'expires_at' => now()->subDay(),
            'used' => false,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token kelulusan tidak sah');

        $this->autoReplyService->approveDraft($draft, $approver, $token->token);
    }

    #[Test]
    public function it_logs_draft_generation_for_audit(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn(['response' => 'Generated content']);

        $this->autoReplyService->generateDraft($ticket, $user->id);

        $this->assertDatabaseHas('message_logs', [
            'operation_type' => 'auto_reply_generation',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_logs_approval_action_for_audit(): void
    {
        $generator = User::factory()->create();
        $approver = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $draft = AutoReplyDraft::create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
            'draft_content' => 'Test draft content',
            'status' => AutoReplyDraft::STATUS_PENDING_REVIEW,
            'generated_by' => $generator->id,
        ]);

        $this->autoReplyService->approveDraft($draft, $approver);

        $this->assertDatabaseHas('message_logs', [
            'operation_type' => 'auto_reply_approval',
            'user_id' => $approver->id,
        ]);
    }

    #[Test]
    public function it_truncates_long_ai_content(): void
    {
        config(['ollama.auto_reply.max_content_length' => 100]);
        $this->autoReplyService = new AutoReplyService(
            $this->ollamaClientMock,
            $this->ragServiceMock
        );

        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $longContent = str_repeat('Kandungan panjang. ', 100);

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn(['response' => $longContent]);

        $draft = $this->autoReplyService->generateDraft($ticket, $user->id);

        $this->assertLessThanOrEqual(103, strlen($draft->draft_content));
        $this->assertStringEndsWith('...', $draft->draft_content);
    }

    #[Test]
    public function it_handles_ai_generation_failure(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();

        $this->ollamaClientMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn(['response' => '']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Gagal menjana kandungan auto-reply');

        $this->autoReplyService->generateDraft($ticket, $user->id);
    }

    #[Test]
    public function it_extracts_template_variables_for_ticket(): void
    {
        $user = User::factory()->create(['name' => 'John Doe', 'email' => 'john@motac.gov.my']);
        // PKS 5.2.1 - Use authenticated user only (no guest fields)
        $ticket = HelpdeskTicket::factory()->create([
            'subject' => 'Test Subject',
            'priority' => 'high',
            'user_id' => $user->id,
        ]);

        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Tiket: {{ticket_id}}, Keutamaan: {{ticket_priority}}, Pemohon: {{submitter_name}}',
            'variables' => ['ticket_id', 'ticket_priority', 'submitter_name'],
            'status' => 'active',
        ]);

        $draft = $this->autoReplyService->generateDraft($ticket, $user->id, $template->id);

        $this->assertStringContainsString((string) $ticket->id, $draft->draft_content);
        $this->assertStringContainsString('high', $draft->draft_content);
    }

    #[Test]
    public function it_extracts_template_variables_for_loan(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        // PKS 5.2.1 - Use authenticated user only (no guest fields)
        $loan = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Mesyuarat',
        ]);

        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Pemohon: {{applicant_name}}, Tujuan: {{loan_purpose}}',
            'variables' => ['applicant_name', 'loan_purpose'],
            'status' => 'active',
        ]);

        $draft = $this->autoReplyService->generateDraft($loan, $user->id, $template->id);

        $this->assertStringContainsString('Jane Doe', $draft->draft_content);
        $this->assertStringContainsString('Mesyuarat', $draft->draft_content);
    }
}
