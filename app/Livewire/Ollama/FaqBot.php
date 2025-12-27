<?php

declare(strict_types=1);

namespace App\Livewire\Ollama;

use App\Services\RagService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * FAQ Bot Main Interface Component
 *
 * Antara muka utama FAQ Bot AI untuk ICTServe v3.6.1.
 * Mematuhi True Hybrid Architecture dan WCAG 2.2 Level AA.
 * Bahasa Melayu sahaja mengikut D15 v3.6.0+.
 *
 * Cloud Hybrid AI Architecture:
 * - Ollama (Local): Untuk pertanyaan FAQ dan data sensitif (PKS 4.2 compliance)
 * - AWS Bedrock: Untuk pertanyaan kompleks dengan data awam (selepas DLP filters)
 *
 * @version 3.6.1
 *
 * @see D18_AI_CHATBOT_OLLAMA_BEDROCK.md (Cloud Hybrid AI Architecture)
 * @see D00_SYSTEM_OVERVIEW.md (True Hybrid Architecture)
 * @see D15_LANGUAGE_MS_EN.md (Bahasa Melayu sahaja)
 *
 * @requirements 1.1, 1.2, 1.3, 1.4, 5.1, 5.2, 5.6, 5.7
 *
 * @trace D18-§2.4 (Integration Context), D18-§5.1 (Query Classification)
 */
#[Layout('layouts.faq-bot')]
class FaqBot extends Component
{
    /**
     * User query input
     */
    public string $query = '';

    /**
     * Conversation messages
     *
     * @var array<int, array{role: string, content: string, timestamp: string, sources?: array}>
     */
    public array $messages = [];

    /**
     * Loading state
     */
    public bool $isLoading = false;

    /**
     * Error message
     */
    public ?string $errorMessage = null;

    /**
     * Screen reader announcement
     */
    public string $announcement = '';

    /**
     * Show conversation history panel
     */
    public bool $showHistory = false;

    /**
     * AI Provider selection (ollama or bedrock)
     */
    public string $aiProvider = 'ollama';

    /**
     * Bedrock model selection
     */
    public string $bedrockModel = 'haiku';

    /**
     * Maximum conversation turns to keep
     */
    private const MAX_CONVERSATION_TURNS = 10;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Load conversation history from session
        $this->messages = session('ollama_faq_messages', []);

        // Transfer from widget if available
        if (empty($this->messages) && session()->has('ollama_widget_messages')) {
            $this->messages = session('ollama_widget_messages', []);
            session()->forget('ollama_widget_messages');
        }

        // Initialize with welcome message if no conversation exists
        if (empty($this->messages)) {
            $this->addMessage('assistant', __('ollama.faq.welcome_message', [], 'ms'));
        }

        // Load conversation history for authenticated users
        if (Auth::check()) {
            $this->loadUserConversationHistory();
        }
    }

    /**
     * Submit query to FAQ Bot
     */
    public function submitQuery(): void
    {
        $this->validate([
            'query' => ['required', 'string', 'max:500'],
            'aiProvider' => ['required', 'in:ollama,bedrock'],
            'bedrockModel' => ['required_if:aiProvider,bedrock', 'in:opus,sonnet,haiku,nova_micro,nova_lite,nova_pro,titan_text_lite,titan_text_express'],
        ], [
            'query.required' => __('ollama.validation.query_required', [], 'ms'),
            'query.max' => __('ollama.validation.query_too_long', [], 'ms'),
            'aiProvider.required' => __('Sila pilih penyedia AI', [], 'ms'),
            'bedrockModel.required_if' => __('Sila pilih model Bedrock', [], 'ms'),
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->announcement = __('ollama.accessibility.processing_query', [], 'ms');

        // Dispatch browser event for screen reader
        $this->dispatch('announce', message: $this->announcement);

        try {
            // Add user message to history
            $this->addMessage('user', $this->query);

            if ($this->aiProvider === 'bedrock') {
                // Use Bedrock service
                $response = $this->processBedrockQuery();
            } else {
                // Use Ollama service (original)
                $response = $this->processOllamaQuery();
            }

            if ($response['success']) {
                // Add AI response to history with sources
                $this->addMessage('assistant', $response['answer'], $response['sources'] ?? []);

                // Update announcement for screen reader
                $preview = mb_substr($response['answer'], 0, 100);
                $this->announcement = __('ollama.accessibility.response_received', ['preview' => $preview], 'ms');
            } else {
                $this->errorMessage = $response['error'] ?? __('ollama.errors.general_error', [], 'ms');
                $this->announcement = __('ollama.accessibility.error_occurred', [], 'ms');
            }

            // Clear input
            $this->query = '';

            // Save to session
            $this->saveConversationToSession();
        } catch (\Exception $e) {
            Log::error('FAQ Bot error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'query' => $this->query,
                'ai_provider' => $this->aiProvider,
                'bedrock_model' => $this->bedrockModel,
                'component' => 'FaqBot',
            ]);

            $this->errorMessage = __('ollama.errors.server_error', [], 'ms');
            $this->announcement = __('ollama.accessibility.error_occurred', [], 'ms');
        } finally {
            $this->isLoading = false;
            $this->dispatch('announce', message: $this->announcement);
        }
    }

    /**
     * Process query using Ollama (original method)
     */
    private function processOllamaQuery(): array
    {
        $ragService = app(RagService::class);

        return $ragService->processQuery(
            $this->query,
            session()->getId(),
            Auth::id(),
            Auth::user()?->email
        );
    }

    /**
     * Process query using Bedrock
     *
     * @trace D18-§5.1 (Query Classification), D18-§7.1 (Response Structure)
     */
    private function processBedrockQuery(): array
    {
        try {
            $bedrockService = app(\App\Services\BedrockService::class);

            $response = $bedrockService->invoke(
                prompt: $this->query,
                maxTokens: 1000,
                modelId: $this->getBedrockModelId(),
                context: [
                    'user_id' => Auth::id(),
                    'context' => 'faq',
                    'use_internet' => false,
                    'query_type' => 'faq_bedrock',
                ]
            );

            if ($response && ($response['success'] ?? false)) {
                return [
                    'success' => true,
                    'answer' => $response['content'],
                    'sources' => [], // Bedrock doesn't provide sources like RAG
                    'model' => $this->bedrockModel,
                    'tokens' => $response['usage']['output_tokens'] ?? null,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response['error'] ?? 'Ralat semasa memproses dengan Bedrock AI.',
                    'error_code' => $response['error_code'] ?? 'BEDROCK_ERROR',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Bedrock FAQ Bot error', [
                'error' => $e->getMessage(),
                'model' => $this->bedrockModel,
                'query' => $this->query,
                'component' => 'FaqBot',
            ]);

            return [
                'success' => false,
                'error' => 'Ralat sambungan ke Bedrock AI.',
                'error_code' => 'BEDROCK_CONNECTION_ERROR',
            ];
        }
    }

    /**
     * Get Bedrock model ID based on selection
     *
     * Uses inference profile format as required by AWS Bedrock on-demand throughput.
     *
     * @trace D18-§6.2 (Inference Profile Requirements)
     * @trace D18-§4.4 (Model Rate Limits)
     */
    private function getBedrockModelId(): string
    {
        return match ($this->bedrockModel) {
            // Claude 4.5 Models (Anthropic)
            // Note: Opus 4.5 requires global inference profile
            'opus' => config('bedrock.models.opus'),      // global.anthropic.claude-opus-4-5-*
            'sonnet' => config('bedrock.models.sonnet'),  // us.anthropic.claude-sonnet-4-5-*
            'haiku' => config('bedrock.models.haiku'),    // us.anthropic.claude-haiku-4-5-*

            // Amazon Nova Models (New in v3.6.1)
            'nova_micro' => config('bedrock.models.nova_micro'),  // amazon.nova-micro-v1:0
            'nova_lite' => config('bedrock.models.nova_lite'),    // amazon.nova-lite-v1:0
            'nova_pro' => config('bedrock.models.nova_pro'),      // amazon.nova-pro-v1:0

            // Amazon Titan Models
            'titan_text_lite' => config('bedrock.models.titan_text_lite'),      // amazon.titan-text-lite-v1
            'titan_text_express' => config('bedrock.models.titan_text_express'), // amazon.titan-text-express-v1

            // Default fallback to Haiku (fast response for FAQ)
            default => config('bedrock.models.haiku'),
        };
    }

    /**
     * Switch AI provider
     *
     * @trace D18-§5.1 (Query Classification), D18-§8.1 (Cost Optimization)
     */
    public function switchProvider(string $provider): void
    {
        if (in_array($provider, ['ollama', 'bedrock'])) {
            $this->aiProvider = $provider;

            // Reset model selection when switching
            if ($provider === 'bedrock' && empty($this->bedrockModel)) {
                $this->bedrockModel = 'haiku'; // Default to fast model for FAQ
            }

            $this->announcement = $provider === 'bedrock'
                ? 'Bertukar ke AWS Bedrock AI. Model: '.ucfirst(str_replace('_', ' ', $this->bedrockModel))
                : 'Bertukar ke Ollama AI tempatan.';

            $this->dispatch('announce', message: $this->announcement);
        }
    }

    /**
     * Clear conversation history
     */
    public function clearConversation(): void
    {
        $this->messages = [];
        $this->query = '';
        $this->errorMessage = null;
        session()->forget('ollama_faq_messages');

        // Add welcome message back
        $this->addMessage('assistant', __('ollama.faq.welcome_message', [], 'ms'));
        $this->saveConversationToSession();

        $this->announcement = __('ollama.accessibility.conversation_cleared', [], 'ms');
        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Toggle conversation history panel
     */
    public function toggleHistory(): void
    {
        $this->showHistory = ! $this->showHistory;

        $this->announcement = $this->showHistory
            ? __('ollama.accessibility.history_opened', [], 'ms')
            : __('ollama.accessibility.history_closed', [], 'ms');

        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Create helpdesk ticket from conversation
     */
    public function createTicket(): void
    {
        // Prepare conversation summary for ticket
        $conversationSummary = $this->getConversationSummary();
        session(['helpdesk_ai_context' => $conversationSummary]);

        // Redirect to ticket creation
        $this->dispatch('redirect', url: route('helpdesk.create'));
    }

    /**
     * Add message to conversation history
     */

    /**
     * @param  array<string, mixed>  $sources
     */
    private function addMessage(string $role, string $content, array $sources = []): void
    {
        $message = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ];

        if (! empty($sources)) {
            $message['sources'] = $sources;
        }

        // Add provider information for assistant messages
        if ($role === 'assistant') {
            $message['provider'] = $this->aiProvider;
            if ($this->aiProvider === 'bedrock') {
                $message['model'] = $this->bedrockModel;
            }
        }

        $this->messages[] = $message;

        // Keep only recent messages
        if (\count($this->messages) > self::MAX_CONVERSATION_TURNS * 2) {
            $this->messages = \array_slice($this->messages, -self::MAX_CONVERSATION_TURNS * 2);
        }
    }

    /**
     * Load conversation history for authenticated users
     */
    private function loadUserConversationHistory(): void
    {
        // Try to claim any guest conversations for this user
        $ragService = app(RagService::class);
        $ragService->claimGuestConversation(
            session()->getId(),
            Auth::id(),
            Auth::user()->email
        );
    }

    /**
     * Save conversation to session
     */
    private function saveConversationToSession(): void
    {
        session(['ollama_faq_messages' => $this->messages]);
    }

    /**
     * Get conversation context for RAG
     *
     * @return array<int, array{role: string, content: string}>
     */
    public function getConversationContext(): array
    {
        return \array_map(fn ($msg) => [
            'role' => $msg['role'],
            'content' => $msg['content'],
        ], \array_slice($this->messages, -5 * 2)); // Last 5 turns
    }

    /**
     * Get conversation summary for ticket creation
     */
    private function getConversationSummary(): string
    {
        $summary = "Ringkasan Perbualan AI FAQ Bot:\n\n";

        foreach ($this->messages as $message) {
            $role = $message['role'] === 'user' ? 'Pengguna' : 'AI Bot';
            $summary .= "{$role}: ".mb_substr($message['content'], 0, 200)."\n\n";
        }

        return $summary;
    }

    /**
     * Check if user is authenticated
     */
    #[Computed]
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Get current user name for personalization
     */
    #[Computed]
    public function userName(): ?string
    {
        return Auth::user()?->name;
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.ollama.faq-bot');
    }
}
