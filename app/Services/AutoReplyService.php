<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use App\Models\AutoReplyTemplate;
use App\Models\AutoReplyDraft;
use App\Models\ApprovalEmailToken;
use App\Models\User;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Perkhidmatan Auto-Reply untuk penjanaan draf respons AI
 *
 * Mengendalikan penjanaan draf respons automatik dengan template,
 * aliran kerja kelulusan, dan notifikasi e-mel untuk sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D10 Source Code Documentation v3.6.0
 * @requirements 3.1, 3.2, 3.3, 3.4, 3.6
 */
class AutoReplyService
{
    /**
     * Klien Ollama untuk AI operations
     */
    private OllamaClientContract $ollamaClient;

    /**
     * Perkhidmatan RAG untuk konteks
     */
    private RagService $ragService;

    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Konstruktor
     */
    public function __construct(
        OllamaClientContract $ollamaClient,
        RagService $ragService
    ) {
        $this->ollamaClient = $ollamaClient;
        $this->ragService = $ragService;
        $this->config = config('ollama.auto_reply', [
            'approval_required' => true,
            'token_validity_days' => 7,
            'notification_timeout' => 60, // saat
            'max_content_length' => 5000,
            'default_template_variables' => [
                'system_name' => 'ICTServe',
                'organization' => 'BPM MOTAC',
                'support_email' => 'ict@motac.gov.my',
            ],
        ]);
    }

    /**
     * Jana draf auto-reply untuk tiket atau permohonan
     *
     * @param Model $replyable Model yang memerlukan respons (HelpdeskTicket/LoanApplication)
     * @param int $generatedBy ID pengguna yang menjana draf
     * @param int|null $templateId ID template untuk digunakan (opsyen)
     * @return AutoReplyDraft Draf yang dicipta
     */
    public function generateDraft(
        Model $replyable,
        int $generatedBy,
        ?int $templateId = null
    ): AutoReplyDraft {
        $startTime = microtime(true);
        $requestId = Str::uuid()->toString();

        try {
            // Validasi model yang disokong
            $this->validateReplyableModel($replyable);

            // Dapatkan konteks untuk penjanaan respons
            $context = $this->buildContext($replyable);

            // Jana kandungan draf
            $draftContent = $this->generateDraftContent($context, $templateId);

            // Cipta draf dalam database
            $draft = AutoReplyDraft::create([
                'replyable_type' => get_class($replyable),
                'replyable_id' => $replyable->id,
                'draft_content' => $draftContent,
                'template_id' => $templateId,
                'status' => AutoReplyDraft::STATUS_DRAFT,
                'generated_by' => $generatedBy,
            ]);

            // Log operasi untuk audit
            $this->logDraftGeneration($requestId, $draft, $context, microtime(true) - $startTime);

            Log::info('Auto-reply draft generated successfully', [
                'draft_id' => $draft->id,
                'replyable_type' => get_class($replyable),
                'replyable_id' => $replyable->id,
                'generated_by' => $generatedBy,
                'template_id' => $templateId,
                'processing_time' => microtime(true) - $startTime,
            ]);

            return $draft;

        } catch (\Exception $e) {
            Log::error('Auto-reply draft generation failed', [
                'request_id' => $requestId,
                'replyable_type' => get_class($replyable),
                'replyable_id' => $replyable->id,
                'generated_by' => $generatedBy,
                'template_id' => $templateId,
                'error' => $e->getMessage(),
                'processing_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }
    /**
     * Hantar draf untuk kelulusan
     *
     * @param AutoReplyDraft $draft Draf untuk dihantar
     * @param bool $sendEmailNotification Hantar notifikasi e-mel
     * @return bool Status penghantaran
     */
    public function submitForApproval(AutoReplyDraft $draft, bool $sendEmailNotification = true): bool
    {
        try {
            if (!$draft->update(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW])) {
                return false;
            }

            if ($sendEmailNotification && $this->config['approval_required']) {
                $this->sendApprovalNotifications($draft);
            }

            Log::info('Draft submitted for approval', [
                'draft_id' => $draft->id,
                'replyable_type' => $draft->replyable_type,
                'replyable_id' => $draft->replyable_id,
                'email_sent' => $sendEmailNotification,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to submit draft for approval', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Luluskan draf auto-reply
     *
     * @param AutoReplyDraft $draft Draf untuk diluluskan
     * @param User $approver Pengguna yang meluluskan
     * @param string|null $token Token kelulusan (untuk e-mel approval)
     * @return bool Status kelulusan
     */
    public function approveDraft(AutoReplyDraft $draft, User $approver, ?string $token = null): bool
    {
        try {
            // Validasi token jika disediakan
            if ($token && !$this->validateApprovalToken($token, $draft, 'approve')) {
                throw new \InvalidArgumentException('Token kelulusan tidak sah atau telah tamat tempoh');
            }

            // Luluskan draf
            if (!$draft->approve($approver)) {
                return false;
            }

            // Tandakan token sebagai digunakan jika ada
            if ($token) {
                $this->markTokenAsUsed($token);
            }

            // Log kelulusan untuk audit
            $this->logApprovalAction($draft, $approver, 'approved', $token);

            Log::info('Draft approved successfully', [
                'draft_id' => $draft->id,
                'approved_by' => $approver->id,
                'approver_name' => $approver->name,
                'via_token' => $token !== null,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to approve draft', [
                'draft_id' => $draft->id,
                'approver_id' => $approver->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Tolak draf auto-reply
     *
     * @param AutoReplyDraft $draft Draf untuk ditolak
     * @param User $approver Pengguna yang menolak
     * @param string $reason Sebab penolakan
     * @param string|null $token Token kelulusan (untuk e-mel approval)
     * @return bool Status penolakan
     */
    public function rejectDraft(
        AutoReplyDraft $draft,
        User $approver,
        string $reason,
        ?string $token = null
    ): bool {
        try {
            // Validasi token jika disediakan
            if ($token && !$this->validateApprovalToken($token, $draft, 'reject')) {
                throw new \InvalidArgumentException('Token penolakan tidak sah atau telah tamat tempoh');
            }

            // Tolak draf
            if (!$draft->reject($approver, $reason)) {
                return false;
            }

            // Tandakan token sebagai digunakan jika ada
            if ($token) {
                $this->markTokenAsUsed($token);
            }

            // Log penolakan untuk audit
            $this->logApprovalAction($draft, $approver, 'rejected', $token, $reason);

            Log::info('Draft rejected successfully', [
                'draft_id' => $draft->id,
                'approved_by' => $approver->id,
                'approver_name' => $approver->name,
                'reason' => $reason,
                'via_token' => $token !== null,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to reject draft', [
                'draft_id' => $draft->id,
                'approver_id' => $approver->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
    /**
     * Validasi model yang disokong untuk auto-reply
     */
    private function validateReplyableModel(Model $model): void
    {
        $supportedModels = [
            HelpdeskTicket::class,
            LoanApplication::class,
        ];

        if (!in_array(get_class($model), $supportedModels)) {
            throw new \InvalidArgumentException(
                'Model ' . get_class($model) . ' tidak disokong untuk auto-reply'
            );
        }
    }

    /**
     * Bina konteks untuk penjanaan respons
     */
    private function buildContext(Model $replyable): array
    {
        $context = [
            'type' => $this->getReplyableType($replyable),
            'model' => $replyable,
            'variables' => $this->extractTemplateVariables($replyable),
            'history' => $this->getReplyableHistory($replyable),
        ];

        // Tambah konteks khusus berdasarkan jenis model
        if ($replyable instanceof HelpdeskTicket) {
            $context['ticket_context'] = $this->buildTicketContext($replyable);
        } elseif ($replyable instanceof LoanApplication) {
            $context['loan_context'] = $this->buildLoanContext($replyable);
        }

        return $context;
    }

    /**
     * Dapatkan jenis replyable dalam format yang mudah dibaca
     */
    private function getReplyableType(Model $replyable): string
    {
        return match (get_class($replyable)) {
            HelpdeskTicket::class => 'helpdesk_ticket',
            LoanApplication::class => 'loan_application',
            default => 'unknown',
        };
    }

    /**
     * Ekstrak pembolehubah template dari model
     */
    private function extractTemplateVariables(Model $replyable): array
    {
        $variables = $this->config['default_template_variables'];

        if ($replyable instanceof HelpdeskTicket) {
            $variables = array_merge($variables, [
                'ticket_id' => $replyable->id,
                'ticket_title' => $replyable->title,
                'ticket_category' => $replyable->category,
                'ticket_priority' => $replyable->priority,
                'ticket_status' => $replyable->status,
                'submitter_name' => $replyable->name,
                'submitter_email' => $replyable->email,
                'submission_date' => $replyable->created_at->format('d/m/Y'),
            ]);
        } elseif ($replyable instanceof LoanApplication) {
            $variables = array_merge($variables, [
                'loan_id' => $replyable->id,
                'applicant_name' => $replyable->name,
                'applicant_email' => $replyable->email,
                'loan_purpose' => $replyable->purpose,
                'loan_status' => $replyable->status,
                'application_date' => $replyable->created_at->format('d/m/Y'),
            ]);
        }

        return $variables;
    }

    /**
     * Dapatkan sejarah replyable untuk konteks
     */
    private function getReplyableHistory(Model $replyable): array
    {
        // Implementasi bergantung pada model - boleh diperluas
        return [
            'created_at' => $replyable->created_at,
            'updated_at' => $replyable->updated_at,
            'status_changes' => [], // Boleh diperluas dengan audit trail
        ];
    }

    /**
     * Bina konteks khusus untuk tiket helpdesk
     */
    private function buildTicketContext(HelpdeskTicket $ticket): array
    {
        return [
            'description' => $ticket->description,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
            'department' => $ticket->department,
            'attachments_count' => $ticket->attachments()->count(),
            'comments_count' => $ticket->comments()->count(),
        ];
    }

    /**
     * Bina konteks khusus untuk permohonan pinjaman
     */
    private function buildLoanContext(LoanApplication $loan): array
    {
        return [
            'purpose' => $loan->purpose,
            'department' => $loan->department,
            'grade' => $loan->grade,
            'items_count' => $loan->items()->count(),
            'total_value' => $loan->items()->sum('estimated_value'),
        ];
    }
    /**
     * Jana kandungan draf menggunakan template atau AI
     */
    private function generateDraftContent(array $context, ?int $templateId = null): string
    {
        if ($templateId) {
            return $this->generateFromTemplate($context, $templateId);
        }

        return $this->generateWithAI($context);
    }

    /**
     * Jana kandungan menggunakan template
     */
    private function generateFromTemplate(array $context, int $templateId): string
    {
        $template = AutoReplyTemplate::active()->findOrFail($templateId);

        // Validasi pembolehubah template
        if (!$template->validateVariables($context['variables'])) {
            throw new \InvalidArgumentException('Pembolehubah template tidak lengkap');
        }

        // Proses template dengan pembolehubah
        $content = $template->processTemplate($context['variables']);

        // Tambah konteks AI jika diperlukan
        if (str_contains($content, '{{ai_context}}')) {
            $aiContext = $this->generateAIContext($context);
            $content = str_replace('{{ai_context}}', $aiContext, $content);
        }

        return $content;
    }

    /**
     * Jana kandungan menggunakan AI sahaja
     */
    private function generateWithAI(array $context): string
    {
        $prompt = $this->buildAIPrompt($context);

        $response = $this->ollamaClient->generate([
            'prompt' => $prompt,
            'temperature' => 0.7,
            'max_tokens' => 1500,
            'top_p' => 0.9,
        ]);

        $content = $response['response'] ?? '';

        if (empty($content)) {
            throw new \RuntimeException('Gagal menjana kandungan auto-reply');
        }

        // Validasi panjang kandungan
        if (strlen($content) > $this->config['max_content_length']) {
            $content = substr($content, 0, $this->config['max_content_length']) . '...';
        }

        return trim($content);
    }

    /**
     * Bina prompt AI untuk penjanaan respons
     */
    private function buildAIPrompt(array $context): string
    {
        $type = $context['type'];
        $model = $context['model'];

        $systemPrompt = "Anda adalah pembantu AI untuk sistem ICTServe MOTAC. " .
                       "Jana respons profesional dalam Bahasa Melayu sahaja untuk ";

        if ($type === 'helpdesk_ticket') {
            $systemPrompt .= "tiket helpdesk berikut:\n\n";
            $systemPrompt .= "Tajuk: {$model->title}\n";
            $systemPrompt .= "Kategori: {$model->category}\n";
            $systemPrompt .= "Keutamaan: {$model->priority}\n";
            $systemPrompt .= "Penerangan: {$model->description}\n";
        } elseif ($type === 'loan_application') {
            $systemPrompt .= "permohonan pinjaman aset berikut:\n\n";
            $systemPrompt .= "Pemohon: {$model->name}\n";
            $systemPrompt .= "Tujuan: {$model->purpose}\n";
            $systemPrompt .= "Jabatan: {$model->department}\n";
        }

        $systemPrompt .= "\n\nSila jana respons yang:\n";
        $systemPrompt .= "- Profesional dan membantu\n";
        $systemPrompt .= "- Dalam Bahasa Melayu sahaja\n";
        $systemPrompt .= "- Menyediakan maklumat yang berguna\n";
        $systemPrompt .= "- Sesuai dengan konteks permohonan\n";
        $systemPrompt .= "- Tidak melebihi 500 perkataan\n\n";
        $systemPrompt .= "Respons:";

        return $systemPrompt;
    }

    /**
     * Jana konteks AI untuk template
     */
    private function generateAIContext(array $context): string
    {
        // Gunakan RAG untuk dapatkan konteks yang berkaitan
        $query = $this->buildContextQuery($context);
        $ragResponse = $this->ragService->processQuery($query);

        if ($ragResponse['success'] && !empty($ragResponse['answer'])) {
            return $ragResponse['answer'];
        }

        return 'Tiada konteks tambahan tersedia.';
    }

    /**
     * Bina query untuk RAG berdasarkan konteks
     */
    private function buildContextQuery(array $context): string
    {
        $model = $context['model'];

        if ($context['type'] === 'helpdesk_ticket') {
            return "Bagaimana untuk menyelesaikan masalah {$model->category} dengan keutamaan {$model->priority}?";
        } elseif ($context['type'] === 'loan_application') {
            return "Apakah prosedur untuk permohonan pinjaman aset untuk tujuan {$model->purpose}?";
        }

        return 'Maklumat am tentang perkhidmatan ICT MOTAC';
    }
    /**
     * Hantar notifikasi kelulusan kepada admin/superuser
     */
    private function sendApprovalNotifications(AutoReplyDraft $draft): void
    {
        try {
            // Dapatkan pengguna yang boleh meluluskan (admin + superuser)
            $approvers = User::role(['admin', 'superuser'])->get();

            if ($approvers->isEmpty()) {
                Log::warning('No approvers found for auto-reply draft', [
                    'draft_id' => $draft->id,
                ]);
                return;
            }

            // Cipta token kelulusan untuk setiap approver
            $approveToken = ApprovalEmailToken::createForDraft($draft, 'approve', $this->config['token_validity_days']);
            $rejectToken = ApprovalEmailToken::createForDraft($draft, 'reject', $this->config['token_validity_days']);

            // Hantar e-mel kepada setiap approver
            foreach ($approvers as $approver) {
                $this->sendApprovalEmail($draft, $approver, $approveToken, $rejectToken);
            }

            Log::info('Approval notifications sent', [
                'draft_id' => $draft->id,
                'approver_count' => $approvers->count(),
                'approve_token' => $approveToken->token,
                'reject_token' => $rejectToken->token,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send approval notifications', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Hantar e-mel kelulusan kepada approver
     */
    private function sendApprovalEmail(
        AutoReplyDraft $draft,
        User $approver,
        ApprovalEmailToken $approveToken,
        ApprovalEmailToken $rejectToken
    ): void {
        // Bina URL kelulusan dengan signed URLs
        $approveUrl = URL::signedRoute('api.auto-reply.approve', [
            'draft' => $draft->id,
            'token' => $approveToken->token,
        ]);

        $rejectUrl = URL::signedRoute('api.auto-reply.reject', [
            'draft' => $draft->id,
            'token' => $rejectToken->token,
        ]);

        $emailData = [
            'draft' => $draft,
            'approver' => $approver,
            'approve_url' => $approveUrl,
            'reject_url' => $rejectUrl,
            'expires_at' => $approveToken->expires_at,
        ];

        // Hantar e-mel (implementasi sebenar bergantung pada Mail class)
        // Mail::to($approver->email)->send(new AutoReplyApprovalMail($emailData));

        Log::info('Approval email prepared', [
            'draft_id' => $draft->id,
            'approver_id' => $approver->id,
            'approver_email' => $approver->email,
        ]);
    }

    /**
     * Validasi token kelulusan
     */
    private function validateApprovalToken(string $token, AutoReplyDraft $draft, string $action): bool
    {
        $tokenModel = ApprovalEmailToken::byToken($token)
            ->where('auto_reply_draft_id', $draft->id)
            ->where('action', $action)
            ->valid()
            ->first();

        return $tokenModel !== null;
    }

    /**
     * Tandakan token sebagai digunakan
     */
    private function markTokenAsUsed(string $token): void
    {
        $tokenModel = ApprovalEmailToken::byToken($token)->first();

        if ($tokenModel) {
            $tokenModel->use(request()->ip());
        }
    }

    /**
     * Log tindakan kelulusan untuk audit
     */
    private function logApprovalAction(
        AutoReplyDraft $draft,
        User $approver,
        string $action,
        ?string $token = null,
        ?string $reason = null
    ): void {
        \App\Models\MessageLog::create([
            'request_id' => Str::uuid()->toString(),
            'operation_type' => 'auto_reply_approval',
            'user_id' => $approver->id,
            'sanitized_input' => "Draft ID: {$draft->id}, Action: {$action}",
            'response_summary' => $reason ?? "Draft {$action} successfully",
            'metadata' => [
                'draft_id' => $draft->id,
                'action' => $action,
                'via_token' => $token !== null,
                'approver_id' => $approver->id,
                'approver_name' => $approver->name,
                'reason' => $reason,
            ],
            'hash' => hash('sha256', $draft->id . $action . $approver->id . now()->timestamp),
            'processed_at' => now(),
        ]);
    }

    /**
     * Log penjanaan draf untuk audit
     */
    private function logDraftGeneration(
        string $requestId,
        AutoReplyDraft $draft,
        array $context,
        float $processingTime
    ): void {
        \App\Models\MessageLog::create([
            'request_id' => $requestId,
            'operation_type' => 'auto_reply_generation',
            'user_id' => $draft->generated_by,
            'sanitized_input' => "Auto-reply for {$context['type']} ID: {$context['model']->id}",
            'response_summary' => Str::limit($draft->draft_content, 200),
            'metadata' => [
                'draft_id' => $draft->id,
                'replyable_type' => $draft->replyable_type,
                'replyable_id' => $draft->replyable_id,
                'template_id' => $draft->template_id,
                'processing_time' => $processingTime,
                'content_length' => strlen($draft->draft_content),
            ],
            'hash' => hash('sha256', $requestId . $draft->draft_content),
            'processed_at' => now(),
        ]);
    }
}
