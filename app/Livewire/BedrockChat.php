<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BedrockConversation;
use App\Models\DlpAuditLog;
use App\Services\BedrockService;
use App\Services\DlpFilteringService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * BedrockChat Livewire Component - PKS 5.2.1 Compliant
 *
 * Cloud Hybrid AI Chat Interface with mandatory SSO authentication
 * and DLP filtering per PKS 9.2.1.
 *
 * @see Requirements 19.3, 19.6, 25.1 - PKS Compliance
 */
class BedrockChat extends Component
{
    public string $prompt = '';

    public string $model = '';

    /** @var array<int, array<string, mixed>> */
    public array $messages = [];

    public bool $useInternet = false;

    public ?int $conversationId = null;

    public bool $showSidebar = false;

    public bool $sending = false;

    public ?string $context = null;

    /** @var array<int, string> */
    public array $faqSuggestions = [];

    /**
     * DLP warning message for user feedback
     */
    public ?string $dlpWarning = null;

    /**
     * Validation rules for the component
     *
     * @var array<string, string>
     */
    protected $rules = [
        'model' => 'required|in:opus,sonnet,haiku,nova_micro,nova_lite,nova_pro,titan_text_lite,titan_text_express',
        'prompt' => 'required|string|min:1|max:4000',
        'useInternet' => 'boolean',
    ];

    /**
     * Custom validation messages
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'model.required' => 'Sila pilih model AI sebelum menghantar mesej.',
            'model.in' => 'Model yang dipilih tidak sah.',
            'prompt.required' => 'Sila masukkan mesej anda.',
            'prompt.min' => 'Mesej terlalu pendek.',
            'prompt.max' => 'Mesej terlalu panjang (maksimum 4000 aksara).',
        ];
    }

    /**
     * Mount the component - PKS 5.2.1 requires authenticated user
     */
    public function mount(?int $id = null): void
    {
        // PKS 5.2.1 - Require authenticated user for AI chat access
        if (! Auth::check()) {
            session()->flash('error', 'Sila log masuk untuk menggunakan perkhidmatan AI Chat.');
            $this->redirect(route('login'));

            return;
        }

        // Handle context parameter from request
        $this->context = request()->get('context');

        // Set FAQ suggestions if context is FAQ
        if ($this->context === 'faq') {
            $this->faqSuggestions = [
                'Bagaimana cara menghantar tiket helpdesk?',
                'Apakah prosedur untuk memohon pinjaman aset ICT?',
                'Bagaimana cara menyemak status permohonan saya?',
                'Siapa yang boleh menggunakan sistem ICTServe?',
                'Bagaimana cara mendaftar akaun baru?',
            ];
        }

        if ($id) {
            $this->loadConversation($id);
        } else {
            $this->initializeConversation();
        }

        // Restore conversation from session if available (for page refresh)
        $sessionMessages = session('bedrock_temp_messages');
        if ($sessionMessages && empty($this->messages)) {
            $this->messages = $sessionMessages;
        }
    }

    public function initializeConversation(): void
    {
        $this->messages = [];
        $this->conversationId = null;
        $this->dlpWarning = null;

        // Add system message based on context
        if ($this->context === 'faq') {
            $this->messages[] = [
                'role' => 'system',
                'content' => 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Bantu pengguna dengan soalan berkaitan perkhidmatan helpdesk, pinjaman aset ICT, dan penggunaan sistem. Jawab dalam Bahasa Melayu.',
            ];
        }
    }

    public function newConversation(): void
    {
        $this->initializeConversation();
        $this->prompt = '';
        $this->model = '';
    }

    /**
     * Load conversation - PKS 5.2.1 requires authenticated user ownership
     */
    public function loadConversation(int $id): void
    {
        // PKS 5.2.1 - Only authenticated users can load their own conversations
        if (! Auth::check()) {
            $this->initializeConversation();

            return;
        }

        $conversation = BedrockConversation::find($id);

        // PKS 5.2.1 - Verify conversation belongs to authenticated user
        if ($conversation && $conversation->user_id === Auth::id()) {
            $this->conversationId = $conversation->id;
            $this->messages = $conversation->messages ?? [];
        } else {
            $this->initializeConversation();
        }
    }

    /**
     * Delete conversation - PKS 5.2.1 requires authenticated user ownership
     */
    public function deleteConversation(int $id): void
    {
        // PKS 5.2.1 - Only authenticated users can delete their own conversations
        if (! Auth::check()) {
            return;
        }

        $conversation = BedrockConversation::find($id);

        // PKS 5.2.1 - Verify conversation belongs to authenticated user
        if ($conversation && $conversation->user_id === Auth::id()) {
            $conversation->delete();

            if ($this->conversationId === $id) {
                $this->newConversation();
            }
        }
    }

    public function useFaqSuggestion(string $suggestion): void
    {
        $this->prompt = $suggestion;
        $this->send();
    }

    /**
     * Send message - PKS 5.2.1 requires authenticated user, PKS 9.2.1 requires DLP filtering
     */
    public function send(): void
    {
        // PKS 5.2.1 - Require authenticated user
        if (! Auth::check()) {
            session()->flash('error', 'Sila log masuk untuk menggunakan perkhidmatan AI Chat.');

            return;
        }

        // Validate before sending
        $this->validate();

        if (empty($this->prompt)) {
            return;
        }

        // Clear any previous DLP warning
        $this->dlpWarning = null;

        $this->sending = true;

        try {
            // PKS 9.2.1 - Apply DLP filtering before processing
            $dlpResult = $this->applyDlpFiltering($this->prompt);

            // Add user message
            $this->messages[] = [
                'role' => 'user',
                'content' => $this->prompt,
            ];

            // Get AI response with DLP-aware routing
            $response = $this->getAIResponse($dlpResult);

            if ($response && ($response['success'] ?? false)) {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => $response['content'],
                    'model' => $dlpResult['sensitive'] ? 'ollama' : $this->model,
                    'tokens' => $response['usage']['output_tokens'] ?? null,
                    'dlp_filtered' => $dlpResult['sensitive'],
                ];

                // Show DLP warning if sensitive data was detected
                if ($dlpResult['sensitive'] && $dlpResult['warning']) {
                    $this->dlpWarning = $dlpResult['warning'];
                }
            } else {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => 'Maaf, terdapat ralat semasa memproses permintaan anda. Sila cuba lagi.',
                    'model' => $this->model,
                    'error' => true,
                ];
            }

            // Save conversation
            $this->saveConversation();

            // Clear prompt
            $this->prompt = '';
        } catch (\Exception $e) {
            Log::error('Bedrock chat error: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Maaf, terdapat ralat semasa memproses permintaan anda. Sila cuba lagi.',
                'model' => $this->model,
                'error' => true,
            ];

            // Save conversation even on error
            $this->saveConversation();

            // Clear prompt
            $this->prompt = '';
        } finally {
            $this->sending = false;
        }
    }

    /**
     * Apply DLP filtering per PKS 9.2.1
     *
     * @return array<string, mixed>
     */
    private function applyDlpFiltering(string $content): array
    {
        try {
            /** @var DlpFilteringService $dlpService */
            $dlpService = app(DlpFilteringService::class);
            $analysis = $dlpService->classifyData($content, Auth::id());

            $isSensitive = $analysis['classification'] === DlpFilteringService::CLASSIFICATION_SENSITIVE;

            // Log DLP decision for audit trail
            $this->logDlpDecision($analysis, $content);

            if ($isSensitive) {
                // Route to Ollama only for sensitive data
                return [
                    'sensitive' => true,
                    'warning' => 'Data sensitif dikesan. Permintaan diproses secara tempatan sahaja (PKS 9.2.1).',
                    'routing' => [
                        'provider' => 'ollama',
                        'reason' => 'PKS 9.2.1: Data sensitif dikesan. Pemprosesan tempatan sahaja.',
                    ],
                ];
            }

            return [
                'sensitive' => false,
                'warning' => null,
                'routing' => [
                    'provider' => 'bedrock',
                    'reason' => 'Data awam. Pemprosesan cloud dibenarkan.',
                ],
            ];
        } catch (\Exception $e) {
            Log::warning('DLP filtering failed, using conservative approach', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            // Conservative approach - route to Ollama if DLP fails
            return [
                'sensitive' => true,
                'warning' => 'Pengesahan DLP gagal. Menggunakan pemprosesan tempatan.',
                'routing' => [
                    'provider' => 'ollama',
                    'reason' => 'DLP fallback: Pemprosesan tempatan sahaja.',
                ],
            ];
        }
    }

    /**
     * Log DLP decision for audit trail per PKS 9.2.1
     *
     * @param  array<string, mixed>  $analysis
     */
    private function logDlpDecision(array $analysis, string $content): void
    {
        try {
            DlpAuditLog::create([
                'user_id' => Auth::id(),
                'classification' => $analysis['classification'],
                'routing_decision' => $analysis['routing_decision'],
                'risk_score' => $analysis['risk_score'],
                'content_hash' => sha1($content),
                'content_length' => strlen($content),
                'detected_patterns' => json_encode($analysis['detected_patterns']),
                'source' => 'bedrock_chat',
                'target_provider' => $analysis['routing_decision'] === DlpFilteringService::ROUTE_LOCAL_ONLY
                    ? DlpAuditLog::PROVIDER_OLLAMA
                    : DlpAuditLog::PROVIDER_BEDROCK,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log DLP decision', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get AI response with DLP-aware routing
     *
     * @param  array<string, mixed>  $dlpResult
     * @return array<string, mixed>|null
     */
    private function getAIResponse(array $dlpResult = []): ?array
    {
        $provider = $dlpResult['routing']['provider'] ?? 'bedrock';

        if ($provider === 'ollama') {
            return $this->getOllamaResponse();
        }

        return $this->getBedrockResponse();
    }

    /**
     * Get response from AWS Bedrock (for public data only per PKS 9.2.1)
     *
     * @return array<string, mixed>|null
     */
    private function getBedrockResponse(): ?array
    {
        $bedrockService = app(BedrockService::class);

        // Call Bedrock service using the invoke method with the current prompt
        return $bedrockService->invoke(
            prompt: $this->prompt,
            maxTokens: 1000,
            modelId: $this->getModelId(),
            context: [
                'user_id' => Auth::id(),
                'context' => $this->context,
                'use_internet' => $this->useInternet,
            ]
        );
    }

    /**
     * Get response from local Ollama (for sensitive data per PKS 9.2.1)
     *
     * @return array<string, mixed>|null
     */
    private function getOllamaResponse(): ?array
    {
        try {
            /** @var \App\Contracts\OllamaClientContract $ollamaClient */
            $ollamaClient = app(\App\Contracts\OllamaClientContract::class);

            $response = $ollamaClient->generate([
                'model' => config('ollama.default_model', 'llama3.2'),
                'prompt' => $this->prompt,
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            return [
                'success' => true,
                'content' => $response['response'] ?? '',
                'usage' => [
                    'output_tokens' => $response['eval_count'] ?? 0,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Ollama response error: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'content' => 'Maaf, perkhidmatan AI tempatan tidak tersedia. Sila cuba lagi.',
                'usage' => [],
            ];
        }
    }

    private function getModelId(): string
    {
        return match ($this->model) {
            // Claude 4.5 Models
            'opus' => config('bedrock.models.opus'),
            'sonnet' => config('bedrock.models.sonnet'),
            'haiku' => config('bedrock.models.haiku'),

            // Amazon Nova Models
            'nova_micro' => config('bedrock.models.nova_micro'),
            'nova_lite' => config('bedrock.models.nova_lite'),
            'nova_pro' => config('bedrock.models.nova_pro'),

            // Amazon Titan Models
            'titan_text_lite' => config('bedrock.models.titan_text_lite'),
            'titan_text_express' => config('bedrock.models.titan_text_express'),

            // Default fallback
            default => config('bedrock.models.sonnet'),
        };
    }

    /**
     * Save conversation - PKS 5.2.1 requires mandatory user_id
     */
    private function saveConversation(): void
    {
        // PKS 5.2.1 - Require authenticated user for saving conversations
        if (! Auth::check()) {
            return;
        }

        // Always save to session for page refresh persistence
        session(['bedrock_temp_messages' => $this->messages]);

        if (! $this->conversationId) {
            // Create new conversation with mandatory user_id (PKS 5.2.1)
            $conversation = BedrockConversation::create([
                'user_id' => Auth::id(), // MANDATORY - NOT NULL per PKS 5.2.1
                'title' => $this->generateConversationTitle(),
                'messages' => $this->messages,
                'model' => $this->model,
            ]);

            $this->conversationId = $conversation->id;
        } else {
            // Update existing conversation
            $conversation = BedrockConversation::find($this->conversationId);
            if ($conversation && $conversation->user_id === Auth::id()) {
                $conversation->update([
                    'messages' => $this->messages,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function generateConversationTitle(): string
    {
        $userMessages = array_filter($this->messages, fn ($message) => $message['role'] === 'user');

        if (! empty($userMessages)) {
            $firstMessage = reset($userMessages)['content'];

            return Str::limit($firstMessage, 50);
        }

        return 'Perbualan Baharu';
    }

    /**
     * Get conversations for authenticated user - PKS 5.2.1 compliant
     */
    public function getConversationsProperty(): Collection
    {
        // PKS 5.2.1 - Only authenticated users can view their conversations
        if (! Auth::check()) {
            return collect();
        }

        return BedrockConversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.bedrock-chat', [
            'conversations' => $this->conversations,
        ]);
    }
}
