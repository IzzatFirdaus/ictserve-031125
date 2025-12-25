<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoReplyEmailActionRequest;
use App\Http\Requests\AutoReplyGenerateRequest;
use App\Jobs\AutoReplyGenerationJob;
use App\Models\ApprovalEmailToken;
use App\Models\AutoReplyDraft;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\AutoReplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Pengawal API untuk Auto-Reply AI
 *
 * Menyediakan endpoint untuk penjanaan dan kelulusan draf auto-reply
 * dengan sokongan token e-mel dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0, D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 3.1, 3.2, 3.4, 3.6
 */
class AutoReplyController extends Controller
{
    /**
     * Perkhidmatan auto-reply
     */
    private AutoReplyService $autoReplyService;

    /**
     * Konstruktor
     */
    public function __construct(AutoReplyService $autoReplyService)
    {
        $this->autoReplyService = $autoReplyService;
    }

    /**
     * Jana draf auto-reply baharu
     */
    public function generate(AutoReplyGenerateRequest $request): JsonResponse
    {
        $requestId = $request->header('X-Request-ID', Str::uuid()->toString());

        try {
            $validated = $request->validated();
            $userId = $request->user()->id;

            // Dapatkan model replyable
            $replyable = $this->getReplyableModel(
                $validated['replyable_type'],
                $validated['replyable_id']
            );

            if (! $replyable) {
                return $this->errorResponse('Model tidak dijumpai.', 404, $requestId);
            }

            // Dispatch job untuk penjanaan async
            if ($request->boolean('async', true)) {
                AutoReplyGenerationJob::dispatch(
                    $validated['replyable_type'],
                    $validated['replyable_id'],
                    $userId,
                    $validated['template_id'] ?? null,
                    $request->boolean('auto_submit', true)
                );

                return $this->successResponse([
                    'message' => 'Penjanaan draf auto-reply sedang diproses.',
                    'async' => true,
                    'request_id' => $requestId,
                ], 202);
            }

            // Penjanaan sinkron
            $draft = $this->autoReplyService->generateDraft(
                $replyable,
                $userId,
                $validated['template_id'] ?? null
            );

            Log::info('Auto-reply draft generated', [
                'request_id' => $requestId,
                'draft_id' => $draft->id,
                'user_id' => $userId,
            ]);

            return $this->successResponse([
                'message' => 'Draf auto-reply berjaya dijana.',
                'draft' => $this->formatDraft($draft),
                'request_id' => $requestId,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Auto-reply generation failed', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Gagal menjana draf auto-reply: '.$e->getMessage(),
                500,
                $requestId
            );
        }
    }

    /**
     * Luluskan draf auto-reply
     *
     * @param  int  $id  ID draf
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $draft = AutoReplyDraft::findOrFail($id);
            $token = $request->input('token');
            $user = $request->user();

            if ($draft->status !== AutoReplyDraft::STATUS_PENDING_REVIEW) {
                return $this->errorResponse('Draf ini sudah diluluskan atau ditolak.', 400);
            }

            if (! $user) {
                if (! is_string($token) || $token === '') {
                    return $this->errorResponse('Token diperlukan untuk kelulusan.', 400);
                }

                $tokenHash = hash('sha256', $token);

                $approvalToken = ApprovalEmailToken::query()
                    ->where('auto_reply_draft_id', $draft->id)
                    ->where('token', $tokenHash)
                    ->where('action', 'approve')
                    ->where('expires_at', '>', now())
                    ->where('used', false)
                    ->first();

                if (! $approvalToken) {
                    return $this->errorResponse('Token kelulusan tidak sah atau telah tamat tempoh.', 400);
                }

                $draft->forceFill([
                    'status' => AutoReplyDraft::STATUS_APPROVED,
                    'approved_by' => null,
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ])->save();

                $approvalToken->use($request->ip());

                return $this->successResponse([
                    'message' => 'Draf berjaya diluluskan.',
                    'draft' => $this->formatDraft($draft->fresh()),
                ]);
            }

            $success = $this->autoReplyService->approveDraft($draft, $user, $token);

            if ($success) {
                return $this->successResponse([
                    'message' => 'Draf berjaya diluluskan.',
                    'draft' => $this->formatDraft($draft->fresh()),
                ]);
            }

            return $this->errorResponse('Gagal meluluskan draf.', 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Draf tidak dijumpai.', 404);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Tolak draf auto-reply
     *
     * @param  int  $id  ID draf
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $draft = AutoReplyDraft::findOrFail($id);
            $token = $request->input('token');
            $user = $request->user();
            $reason = $request->input('reason');

            if ($draft->status !== AutoReplyDraft::STATUS_PENDING_REVIEW) {
                return $this->errorResponse('Draf ini sudah diluluskan atau ditolak.', 400);
            }

            if (! $user) {
                if (! is_string($token) || $token === '') {
                    return $this->errorResponse('Token diperlukan untuk penolakan.', 400);
                }

                $tokenHash = hash('sha256', $token);

                $approvalToken = ApprovalEmailToken::query()
                    ->where('auto_reply_draft_id', $draft->id)
                    ->where('token', $tokenHash)
                    ->where('action', 'reject')
                    ->where('expires_at', '>', now())
                    ->where('used', false)
                    ->first();

                if (! $approvalToken) {
                    return $this->errorResponse('Token penolakan tidak sah atau telah tamat tempoh.', 400);
                }

                $draft->forceFill([
                    'status' => AutoReplyDraft::STATUS_REJECTED,
                    'approved_by' => null,
                    'approved_at' => now(),
                    'rejection_reason' => $reason,
                ])->save();

                $approvalToken->use($request->ip());

                return $this->successResponse([
                    'message' => 'Draf berjaya ditolak.',
                    'draft' => $this->formatDraft($draft->fresh()),
                ]);
            }

            $success = $this->autoReplyService->rejectDraft($draft, $user, $reason, $token);

            if ($success) {
                return $this->successResponse([
                    'message' => 'Draf berjaya ditolak.',
                    'draft' => $this->formatDraft($draft->fresh()),
                ]);
            }

            return $this->errorResponse('Gagal menolak draf.', 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Draf tidak dijumpai.', 404);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Dapatkan status draf
     *
     * @param  int  $id  ID draf
     */
    public function status(int $id): JsonResponse
    {
        try {
            $draft = AutoReplyDraft::with(['generator', 'approver', 'template'])->findOrFail($id);

            return $this->successResponse([
                'draft' => $this->formatDraft($draft),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Draf tidak dijumpai.', 404);
        }
    }

    /**
     * Senaraikan draf pending untuk kelulusan
     */
    public function pending(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $drafts = AutoReplyDraft::with(['generator', 'replyable'])
            ->where('status', AutoReplyDraft::STATUS_PENDING_REVIEW)
            ->latest()
            ->paginate($perPage);

        return $this->successResponse([
            'drafts' => collect($drafts->items())->map(fn ($d) => $this->formatDraft($d)),
            'pagination' => [
                'current_page' => $drafts->currentPage(),
                'last_page' => $drafts->lastPage(),
                'per_page' => $drafts->perPage(),
                'total' => $drafts->total(),
            ],
        ]);
    }

    /**
     * Senaraikan draf dengan pagination dan penapis status.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 15), 100);
        $status = $request->string('status')->toString();

        $drafts = AutoReplyDraft::with(['generator', 'replyable'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);

        return $this->successResponse([
            'drafts' => collect($drafts->items())->map(fn ($d) => $this->formatDraft($d)),
            'pagination' => [
                'current_page' => $drafts->currentPage(),
                'last_page' => $drafts->lastPage(),
                'per_page' => $drafts->perPage(),
                'total' => $drafts->total(),
            ],
        ]);
    }

    /**
     * Tindakan kelulusan melalui token e-mel (tanpa autentikasi).
     */
    public function emailAction(AutoReplyEmailActionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tokenHash = hash('sha256', $validated['token']);

        $approvalToken = ApprovalEmailToken::query()
            ->where('token', $tokenHash)
            ->where('action', $validated['action'])
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();

        if (! $approvalToken) {
            return $this->errorResponse('Token telah tamat tempoh atau tidak sah.', 400);
        }

        $draft = AutoReplyDraft::find($approvalToken->auto_reply_draft_id);

        if (! $draft) {
            return $this->errorResponse('Token telah tamat tempoh atau tidak sah.', 400);
        }

        if ($draft->status !== AutoReplyDraft::STATUS_PENDING_REVIEW) {
            return $this->errorResponse('Draf ini sudah diluluskan atau ditolak.', 400);
        }

        if ($validated['action'] === 'approve') {
            $draft->forceFill([
                'status' => AutoReplyDraft::STATUS_APPROVED,
                'approved_by' => null,
                'approved_at' => now(),
                'rejection_reason' => null,
            ])->save();
        } else {
            $draft->forceFill([
                'status' => AutoReplyDraft::STATUS_REJECTED,
                'approved_by' => null,
                'approved_at' => now(),
                'rejection_reason' => $validated['reason'],
            ])->save();
        }

        if (! $approvalToken->use($request->ip())) {
            return $this->errorResponse('Token telah tamat tempoh atau tidak sah.', 400);
        }

        return $this->successResponse([
            'message' => $validated['action'] === 'approve'
                ? 'Draf berjaya diluluskan.'
                : 'Draf berjaya ditolak.',
        ]);
    }

    /**
     * Dapatkan model replyable
     */
    private function getReplyableModel(string $type, int $id): ?object
    {
        return match ($type) {
            'helpdesk_ticket', HelpdeskTicket::class => HelpdeskTicket::find($id),
            'loan_application', LoanApplication::class => LoanApplication::find($id),
            default => null,
        };
    }

    /**
     * Format draf untuk respons API
     */
    private function formatDraft(AutoReplyDraft $draft): array
    {
        return [
            'id' => $draft->id,
            'replyable_type' => class_basename($draft->replyable_type),
            'replyable_id' => $draft->replyable_id,
            'draft_content' => $draft->draft_content,
            'status' => $draft->status,
            'template_id' => $draft->template_id,
            'generated_by' => $draft->generator?->name,
            'approved_by' => $draft->approver?->name,
            'approved_at' => $draft->approved_at?->toISOString(),
            'rejection_reason' => $draft->rejection_reason,
            'created_at' => $draft->created_at->toISOString(),
            'updated_at' => $draft->updated_at->toISOString(),
        ];
    }

    /**
     * Format respons berjaya
     */
    

/**
 * @param array<string, mixed> $data
 */
private function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Format respons ralat dalam Bahasa Melayu
     */
    private function errorResponse(string $message, int $status, ?string $requestId = null): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $status,
            ],
        ];

        if ($requestId) {
            $response['request_id'] = $requestId;
        }

        return response()->json($response, $status);
    }
}
