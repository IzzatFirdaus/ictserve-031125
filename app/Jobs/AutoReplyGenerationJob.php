<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AutoReplyDraft;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\AutoReplyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk penjanaan auto-reply secara latar belakang
 *
 * Mengendalikan penjanaan draf respons AI secara asinkron dengan
 * sokongan template dan notifikasi kelulusan dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0
 *
 * @requirements 3.1, 3.3, 3.4
 */
class AutoReplyGenerationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Bilangan percubaan maksimum sebelum job gagal
     */
    public int $tries = 3;

    /**
     * Masa menunggu antara percubaan (saat) - exponential backoff
     *
     * @var array<int>
     */
    public array $backoff = [1, 2, 4];

    /**
     * Masa maksimum untuk job berjalan (saat)
     */
    public int $timeout = 300;

    /**
     * Jenis model yang memerlukan respons
     */
    private string $replyableType;

    /**
     * ID model yang memerlukan respons
     */
    private int $replyableId;

    /**
     * ID pengguna yang menjana draf
     */
    private int $generatedBy;

    /**
     * ID template untuk digunakan (opsyen)
     */
    private ?int $templateId;

    /**
     * Hantar untuk kelulusan secara automatik
     */
    private bool $autoSubmitForApproval;

    /**
     * Cipta instance job baharu
     */
    public function __construct(
        string $replyableType,
        int $replyableId,
        int $generatedBy,
        ?int $templateId = null,
        bool $autoSubmitForApproval = true
    ) {
        $this->replyableType = $replyableType;
        $this->replyableId = $replyableId;
        $this->generatedBy = $generatedBy;
        $this->templateId = $templateId;
        $this->autoSubmitForApproval = $autoSubmitForApproval;
        $this->onQueue('auto-reply');
    }

    /**
     * Laksanakan job
     */
    public function handle(AutoReplyService $autoReplyService): void
    {
        $startTime = microtime(true);

        Log::info('AutoReplyGenerationJob started', [
            'replyable_type' => $this->replyableType,
            'replyable_id' => $this->replyableId,
            'generated_by' => $this->generatedBy,
            'template_id' => $this->templateId,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Dapatkan model replyable
            $replyable = $this->getReplyableModel();

            if (! $replyable) {
                throw new \RuntimeException(
                    "Model tidak dijumpai: {$this->replyableType} ID {$this->replyableId}"
                );
            }

            // Jana draf menggunakan AutoReplyService
            $draft = $autoReplyService->generateDraft(
                $replyable,
                $this->generatedBy,
                $this->templateId
            );

            // Hantar untuk kelulusan jika dikonfigurasi
            if ($this->autoSubmitForApproval) {
                $autoReplyService->submitForApproval($draft, true);
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('AutoReplyGenerationJob completed successfully', [
                'draft_id' => $draft->id,
                'replyable_type' => $this->replyableType,
                'replyable_id' => $this->replyableId,
                'status' => $draft->status,
                'processing_time' => $processingTime,
                'content_length' => strlen($draft->draft_content),
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Dapatkan model replyable berdasarkan jenis dan ID
     */
    private function getReplyableModel(): ?Model
    {
        return match ($this->replyableType) {
            HelpdeskTicket::class, 'helpdesk_ticket' => HelpdeskTicket::find($this->replyableId),
            LoanApplication::class, 'loan_application' => LoanApplication::find($this->replyableId),
            default => null,
        };
    }

    /**
     * Kendalikan kegagalan job
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('AutoReplyGenerationJob failed', [
            'replyable_type' => $this->replyableType,
            'replyable_id' => $this->replyableId,
            'generated_by' => $this->generatedBy,
            'template_id' => $this->templateId,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Kendalikan kegagalan job selepas semua percubaan habis
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('AutoReplyGenerationJob permanently failed', [
            'replyable_type' => $this->replyableType,
            'replyable_id' => $this->replyableId,
            'generated_by' => $this->generatedBy,
            'error' => $exception->getMessage(),
        ]);

        // Cipta draf dengan status failed untuk tracking
        try {
            AutoReplyDraft::create([
                'replyable_type' => $this->replyableType,
                'replyable_id' => $this->replyableId,
                'draft_content' => 'Penjanaan auto-reply gagal: '.$exception->getMessage(),
                'template_id' => $this->templateId,
                'status' => 'failed',
                'generated_by' => $this->generatedBy,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create failed draft record', [
                'error' => $e->getMessage(),
            ]);
        }

        // Notifikasi admin tentang kegagalan (boleh diperluas)
        // Notification::send($admins, new AutoReplyGenerationFailed($this->replyableType, $this->replyableId, $exception));
    }

    /**
     * Tentukan tag untuk job (untuk monitoring)
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'auto-reply',
            'replyable:'.class_basename($this->replyableType).':'.$this->replyableId,
            'generated_by:'.$this->generatedBy,
        ];
    }

    /**
     * Cipta job untuk tiket helpdesk
     */
    public static function forTicket(
        HelpdeskTicket $ticket,
        int $generatedBy,
        ?int $templateId = null,
        bool $autoSubmit = true
    ): self {
        return new self(
            HelpdeskTicket::class,
            $ticket->id,
            $generatedBy,
            $templateId,
            $autoSubmit
        );
    }

    /**
     * Cipta job untuk permohonan pinjaman
     */
    public static function forLoanApplication(
        LoanApplication $loan,
        int $generatedBy,
        ?int $templateId = null,
        bool $autoSubmit = true
    ): self {
        return new self(
            LoanApplication::class,
            $loan->id,
            $generatedBy,
            $templateId,
            $autoSubmit
        );
    }
}
