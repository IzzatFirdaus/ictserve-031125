<?php

declare(strict_types=1);

namespace App\Livewire\Ollama;

use App\Models\GuestConversation;
use App\Services\RagService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Link Conversations Component
 *
 * Komponen untuk menghubungkan perbualan tetamu dengan akaun authenticated.
 * Mematuhi True Hybrid Architecture dan Account Linking feature.
 *
 * @version 3.6.0
 *
 * @see D00_SYSTEM_OVERVIEW.md (True Hybrid Architecture)
 *
 * @requirements 1.7, 18.1, 18.2, 18.3, 18.4, 18.5
 */
#[Layout('layouts.app')]
class LinkConversations extends Component
{
    /**
     * Email address to search for guest conversations
     */
    public string $searchEmail = '';

    /**
     * Session ID to search for specific conversation
     */
    public string $searchSessionId = '';

    /**
     * Show search results
     */
    public bool $showResults = false;

    /**
     * Search results
     */
    public array $searchResults = [];

    /**
     * Loading state
     */
    public bool $isLoading = false;

    /**
     * Success message
     */
    public ?string $successMessage = null;

    /**
     * Error message
     */
    public ?string $errorMessage = null;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Ensure user is authenticated
        if (! Auth::check()) {
            abort(403, 'Akses ditolak. Sila log masuk terlebih dahulu.');
        }

        // Pre-fill with user's email
        $this->searchEmail = Auth::user()->email;

        // Auto-search for conversations with user's email
        $this->searchConversations();
    }

    /**
     * Search for guest conversations
     */
    public function searchConversations(): void
    {
        $this->validate([
            'searchEmail' => ['nullable', 'email', 'max:255'],
            'searchSessionId' => ['nullable', 'string', 'max:255'],
        ], [
            'searchEmail.email' => __('validation.email', [], 'ms'),
            'searchEmail.max' => __('validation.max.string', ['max' => 255], 'ms'),
            'searchSessionId.max' => __('validation.max.string', ['max' => 255], 'ms'),
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $query = GuestConversation::query()
                ->whereNull('claimed_by_user_id')
                ->where('expires_at', '>', now());

            // Search by email
            if (! empty($this->searchEmail)) {
                $query->where('email', $this->searchEmail);
            }

            // Search by session ID
            if (! empty($this->searchSessionId)) {
                $query->where('session_id', 'like', "%{$this->searchSessionId}%");
            }

            $conversations = $query->orderBy('created_at', 'desc')->get();

            $this->searchResults = $conversations->map(function ($conversation) {
                $historyCount = \count($conversation->conversation_history ?? []);
                $lastActivity = $conversation->updated_at;

                return [
                    'id' => $conversation->id,
                    'session_id' => $conversation->session_id,
                    'email' => $conversation->email,
                    'message_count' => $historyCount,
                    'last_activity' => $lastActivity,
                    'expires_at' => $conversation->expires_at,
                    'preview' => $this->getConversationPreview($conversation->conversation_history ?? []),
                ];
            })->toArray();

            $this->showResults = true;
        } catch (\Exception $e) {
            Log::error('Error searching guest conversations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'search_email' => $this->searchEmail,
                'search_session_id' => $this->searchSessionId,
            ]);

            $this->errorMessage = __('ollama.link_conversations.search_error', [], 'ms');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Link guest conversation to current user
     */
    public function linkConversation(int $conversationId): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $conversation = GuestConversation::where('id', $conversationId)
                ->whereNull('claimed_by_user_id')
                ->first();

            if (! $conversation) {
                $this->errorMessage = __('ollama.link_conversations.conversation_not_found', [], 'ms');

                return;
            }

            // Use RagService to claim the conversation
            $ragService = app(RagService::class);
            $success = $ragService->claimGuestConversation(
                $conversation->session_id,
                Auth::id(),
                $conversation->email
            );

            if ($success) {
                $this->successMessage = __('ollama.link_conversations.linked_successfully', [], 'ms');

                // Remove from search results
                $this->searchResults = \array_filter(
                    $this->searchResults,
                    fn ($result) => $result['id'] !== $conversationId
                );

                // Dispatch success event
                $this->dispatch('conversation-linked', conversationId: $conversationId);
            } else {
                $this->errorMessage = __('ollama.link_conversations.link_failed', [], 'ms');
            }
        } catch (\Exception $e) {
            Log::error('Error linking guest conversation', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'conversation_id' => $conversationId,
            ]);

            $this->errorMessage = __('ollama.link_conversations.link_error', [], 'ms');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Link all conversations for current user's email
     */
    public function linkAllConversations(): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $conversations = GuestConversation::where('email', Auth::user()->email)
                ->whereNull('claimed_by_user_id')
                ->where('expires_at', '>', now())
                ->get();

            $linkedCount = 0;
            $ragService = app(RagService::class);

            foreach ($conversations as $conversation) {
                $success = $ragService->claimGuestConversation(
                    $conversation->session_id,
                    Auth::id(),
                    $conversation->email
                );

                if ($success) {
                    $linkedCount++;
                }
            }

            if ($linkedCount > 0) {
                $this->successMessage = __('ollama.link_conversations.bulk_linked_successfully', [
                    'count' => $linkedCount,
                ], 'ms');

                // Clear search results
                $this->searchResults = [];
                $this->showResults = false;

                // Dispatch success event
                $this->dispatch('conversations-bulk-linked', count: $linkedCount);
            } else {
                $this->errorMessage = __('ollama.link_conversations.no_conversations_to_link', [], 'ms');
            }
        } catch (\Exception $e) {
            Log::error('Error bulk linking guest conversations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
            ]);

            $this->errorMessage = __('ollama.link_conversations.bulk_link_error', [], 'ms');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Clear search results
     */
    public function clearResults(): void
    {
        $this->searchResults = [];
        $this->showResults = false;
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    /**
     * Reset search form
     */
    public function resetSearch(): void
    {
        $this->searchEmail = Auth::user()->email;
        $this->searchSessionId = '';
        $this->clearResults();
    }

    /**
     * Get conversation preview
     */
    

/**
 * @param array<string, mixed> $conversationHistory
 */
private function getConversationPreview(array $conversationHistory): string
    {
        if (empty($conversationHistory)) {
            return __('ollama.link_conversations.no_messages', [], 'ms');
        }

        $lastMessage = end($conversationHistory);
        $content = $lastMessage['query'] ?? $lastMessage['response'] ?? '';

        return mb_substr($content, 0, 100).(mb_strlen($content) > 100 ? '...' : '');
    }

    /**
     * Get user's already linked conversations count
     */
    #[Computed]
    public function linkedConversationsCount(): int
    {
        return GuestConversation::where('claimed_by_user_id', Auth::id())->count();
    }

    /**
     * Check if there are any available conversations to link
     */
    #[Computed]
    public function hasAvailableConversations(): bool
    {
        return GuestConversation::where('email', Auth::user()->email)
            ->whereNull('claimed_by_user_id')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Check if there are any search results
     */
    public function hasSearchResults(): bool
    {
        return ! empty($this->searchResults);
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.ollama.link-conversations');
    }
}
