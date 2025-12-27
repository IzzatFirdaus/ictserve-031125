<?php

declare(strict_types=1);

namespace App\Livewire\Ollama;

use App\Services\OllamaAccessibilityService;
use App\Services\RagService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * FAQ Bot Widget Component - Floating Chat Bot
 *
 * Komponen widget terapung untuk FAQ Bot AI yang boleh digunakan di mana-mana halaman.
 * Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.1.
 * Sokongan True Hybrid Architecture (guest + authenticated).
 *
 * Cloud Hybrid AI Architecture:
 * - Uses Ollama (Local) by default for FAQ queries
 * - Supports data sovereignty compliance (PKS 4.2)
 *
 * @version 3.6.1
 *
 * @see D18_AI_CHATBOT_OLLAMA_BEDROCK.md (Cloud Hybrid AI Architecture)
 * @see D12_UI_UX_DESIGN_GUIDE.md
 * @see D15_LANGUAGE_MS_EN.md (Bahasa Melayu sahaja)
 *
 * @requirements 1.1, 1.4, 5.1, 5.2, 5.6, 5.7 (D00-D18 v3.6.1)
 *
 * @trace D18-§2.4 (Integration Context), D18-§3.1 (System Architecture)
 */
class FaqBotWidget extends Component
{
    /**
     * Widget visibility state
     */
    public bool $isOpen = false;

    /**
     * Widget minimized state
     */
    public bool $isMinimized = false;

    /**
     * User query input
     */
    public string $query = '';

    /**
     * Conversation messages
     *
     * @var array<int, array{role: string, content: string, timestamp: string}>
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
     * Widget position (bottom-right by default)
     */
    public string $position = 'bottom-right';

    /**
     * Maximum conversation turns to keep
     */
    private const MAX_CONVERSATION_TURNS = 3;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Load conversation history from session if available
        $this->messages = session('ollama_widget_messages', []);

        // Initialize with welcome message if no conversation exists
        if (empty($this->messages)) {
            $this->addMessage('assistant', __('ollama.widget.welcome_message', [], 'ms'));
        }
    }

    /**
     * Toggle widget visibility
     */
    public function toggleWidget(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->isMinimized = false;
            $this->announcement = __('ollama.accessibility.widget_opened', [], 'ms');
        } else {
            $this->announcement = __('ollama.accessibility.widget_closed', [], 'ms');
        }

        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Minimize widget
     */
    public function minimizeWidget(): void
    {
        $this->isMinimized = true;
        $this->announcement = __('ollama.accessibility.widget_minimized', [], 'ms');
        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Restore widget from minimized state
     */
    public function restoreWidget(): void
    {
        $this->isMinimized = false;
        $this->announcement = __('ollama.accessibility.widget_restored', [], 'ms');
        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Close widget
     */
    public function closeWidget(): void
    {
        $this->isOpen = false;
        $this->isMinimized = false;
        $this->announcement = __('ollama.accessibility.widget_closed', [], 'ms');
        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Submit query to FAQ Bot
     */
    public function submitQuery(): void
    {
        $this->validate([
            'query' => ['required', 'string', 'max:500'],
        ], [
            'query.required' => __('ollama.accessibility.error_empty_query', [], 'ms'),
            'query.max' => __('ollama.accessibility.error_query_too_long', [], 'ms'),
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->announcement = __('ollama.accessibility.sr_loading', [], 'ms');

        // Dispatch browser event for screen reader
        $this->dispatch('announce', message: $this->announcement);

        try {
            // Add user message to history
            $this->addMessage('user', $this->query);

            // Get AI response using RagService
            $ragService = app(RagService::class);
            $response = $ragService->processQuery(
                $this->query,
                session()->getId(),
                Auth::id(),
                Auth::user()?->email
            );

            if ($response['success']) {
                // Add AI response to history
                $this->addMessage('assistant', $response['answer']);
            } else {
                $this->errorMessage = $response['error'] ?? __('ollama.errors.general_error', [], 'ms');
                $this->announcement = __('ollama.accessibility.sr_error_occurred', [], 'ms');

                return;
            }

            // Update announcement for screen reader
            $preview = mb_substr($response['answer'], 0, 100);
            $this->announcement = __('ollama.accessibility.sr_response_received', ['preview' => $preview], 'ms');

            // Clear input
            $this->query = '';

            // Save to session
            $this->saveConversationToSession();
        } catch (\Exception $e) {
            Log::error('FAQ Bot Widget error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'component' => 'FaqBotWidget',
            ]);

            $this->errorMessage = __('ollama.accessibility.error_server', [], 'ms');
            $this->announcement = __('ollama.accessibility.sr_error_occurred', [], 'ms');
        } finally {
            $this->isLoading = false;
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
        session()->forget('ollama_widget_messages');

        // Add welcome message back
        $this->addMessage('assistant', __('ollama.widget.welcome_message', [], 'ms'));
        $this->saveConversationToSession();

        $this->announcement = __('ollama.accessibility.conversation_cleared', [], 'ms');
        $this->dispatch('announce', message: $this->announcement);
    }

    /**
     * Open full FAQ Bot page
     */
    public function openFullBot(): void
    {
        // Save current conversation to transfer to full bot
        session(['ollama_faq_messages' => $this->messages]);

        // Dispatch browser event to redirect
        $this->dispatch('redirect', url: route('ai.faq'));
    }

    /**
     * Add message to conversation history
     */
    private function addMessage(string $role, string $content): void
    {
        $this->messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last N turns for widget (smaller than full bot)
        if (\count($this->messages) > self::MAX_CONVERSATION_TURNS * 2) {
            $this->messages = \array_slice($this->messages, -self::MAX_CONVERSATION_TURNS * 2);
        }
    }

    /**
     * Save conversation to session
     */
    private function saveConversationToSession(): void
    {
        session(['ollama_widget_messages' => $this->messages]);
    }

    /**
     * Get accessibility service
     */
    #[Computed]
    public function accessibilityService(): OllamaAccessibilityService
    {
        return app(OllamaAccessibilityService::class);
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
        return view('livewire.ollama.faq-bot-widget');
    }
}
