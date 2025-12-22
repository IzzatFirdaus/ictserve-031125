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
 * Antara muka utama FAQ Bot AI untuk ICTServe v3.6.0.
 * Mematuhi True Hybrid Architecture dan WCAG 2.2 Level AA.
 * Bahasa Melayu sahaja mengikut D15 v3.6.0.
 *
 * @version 3.6.0
 *
 * @see D00_SYSTEM_OVERVIEW.md (True Hybrid Architecture)
 * @see D15_LANGUAGE_MS_EN.md (Bahasa Melayu sahaja)
 *
 * @requirements 1.1, 1.2, 1.3, 1.4, 5.1, 5.2, 5.6, 5.7
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
                ]
            );

            if ($response && ($response['success'] ?? false)) {
                return [
                    'success' => true,
                    'answer' => $response['content'],
                    'sources' => [], // Bedrock doesn't provide sources like RAG
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Ralat semasa memproses dengan Bedrock AI.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Bedrock FAQ Bot error', [
                'error' => $e->getMessage(),
                'model' => $this->bedrockModel,
                'query' => $this->query,
            ]);

            return [
                'success' => false,
                'error' => 'Ralat sambungan ke Bedrock AI.',
            ];
        }
    }

    /**
     * Get Bedrock model ID based on selection
     */
    private function getBedrockModelId(): string
    {
        return match ($this->bedrockModel) {
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
            default => config('bedrock.models.haiku'),
        };
    }

    /**
     * Switch AI provider
     */
    public function switchProvider(string $provider): void
    {
        if (in_array($provider, ['ollama', 'bedrock'])) {
            $this->aiProvider = $provider;

            // Reset model selection when switching
            if ($provider === 'bedrock' && empty($this->bedrockModel)) {
                $this->bedrockModel = 'haiku';
            }

            $this->announcement = $provider === 'bedrock'
                ? __('ollama.bedrock.switch_to_bedrock', [], 'ms')
                : __('ollama.bedrock.switch_to_ollama', [], 'ms');

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
 * @param array<string, mixed> $sources
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
        return \array_map(fn($msg) => [
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
            $summary .= "{$role}: " . mb_substr($message['content'], 0, 200) . "\n\n";
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
