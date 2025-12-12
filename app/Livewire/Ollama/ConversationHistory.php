<?php

declare(strict_types=1);

namespace App\Livewire\Ollama;

use App\Models\GuestConversation;
use App\Models\MessageLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Conversation History Component
 *
 * Komponen untuk melihat dan menguruskan sejarah perbualan AI.
 * Mematuhi True Hybrid Architecture dan PDPA 2010.
 *
 * @version 3.6.0
 *
 * @see D00_SYSTEM_OVERVIEW.md (True Hybrid Architecture)
 * @see D09_DATABASE_DOCUMENTATION.md (Dual Audit System)
 *
 * @requirements 1.7, 4.1, 4.4, 6.4
 */
#[Layout('layouts.app')]
class ConversationHistory extends Component
{
    use WithPagination;

    /**
     * Search query for filtering conversations
     */
    public string $search = '';

    /**
     * Filter by operation type
     */
    public string $operationType = 'all';

    /**
     * Date range filter
     */
    public string $dateRange = '30d';

    /**
     * Selected conversation for detailed view
     */
    public ?int $selectedConversationId = null;

    /**
     * Show export options
     */
    public bool $showExportOptions = false;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Ensure user is authenticated
        if (! Auth::check()) {
            abort(403, 'Akses ditolak. Sila log masuk terlebih dahulu.');
        }
    }

    /**
     * Update search filter
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update operation type filter
     */
    public function updatedOperationType(): void
    {
        $this->resetPage();
    }

    /**
     * Update date range filter
     */
    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    /**
     * View conversation details
     */
    public function viewConversation(int $conversationId): void
    {
        $this->selectedConversationId = $conversationId;
    }

    /**
     * Close conversation details
     */
    public function closeConversationDetails(): void
    {
        $this->selectedConversationId = null;
    }

    /**
     * Delete conversation (PDPA compliance)
     */
    public function deleteConversation(int $conversationId): void
    {
        $conversation = MessageLog::where('id', $conversationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($conversation) {
            $conversation->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ollama.conversation.deleted_successfully', [], 'ms'),
            ]);

            if ($this->selectedConversationId === $conversationId) {
                $this->selectedConversationId = null;
            }
        }
    }

    /**
     * Export conversation data
     */
    public function exportConversations(string $format = 'json'): void
    {
        $this->showExportOptions = false;

        // Redirect to data export controller
        $this->dispatch('redirect', url: route('staff.data-rights.export', ['format' => $format]));
    }

    /**
     * Toggle export options
     */
    public function toggleExportOptions(): void
    {
        $this->showExportOptions = ! $this->showExportOptions;
    }

    /**
     * Clear all conversations (with confirmation)
     */
    public function clearAllConversations(): void
    {
        $this->dispatch('confirm', [
            'title' => __('ollama.conversation.confirm_clear_all_title', [], 'ms'),
            'message' => __('ollama.conversation.confirm_clear_all_message', [], 'ms'),
            'confirmText' => __('ollama.conversation.confirm_clear_all_button', [], 'ms'),
            'cancelText' => __('common.cancel', [], 'ms'),
            'action' => 'performClearAll',
        ]);
    }

    /**
     * Perform clear all conversations
     */
    public function performClearAll(): void
    {
        MessageLog::where('user_id', Auth::id())->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('ollama.conversation.all_cleared_successfully', [], 'ms'),
        ]);

        $this->selectedConversationId = null;
        $this->resetPage();
    }

    /**
     * Get filtered conversations
     */
    #[Computed]
    public function conversations()
    {
        $query = MessageLog::where('user_id', Auth::id());

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('sanitized_input', 'like', "%{$this->search}%")
                    ->orWhere('response_summary', 'like', "%{$this->search}%");
            });
        }

        // Apply operation type filter
        if ($this->operationType !== 'all') {
            $query->where('operation_type', $this->operationType);
        }

        // Apply date range filter
        $days = match ($this->dateRange) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            'all' => null,
            default => 30,
        };

        if ($days) {
            $query->where('processed_at', '>=', now()->subDays($days));
        }

        return $query->orderBy('processed_at', 'desc')->paginate(15);
    }

    /**
     * Get conversation statistics
     */
    #[Computed]
    public function conversationStats(): array
    {
        $userId = Auth::id();

        $totalConversations = MessageLog::where('user_id', $userId)->count();

        $byType = MessageLog::where('user_id', $userId)
            ->selectRaw('operation_type, COUNT(*) as count')
            ->groupBy('operation_type')
            ->pluck('count', 'operation_type')
            ->toArray();

        $last30Days = MessageLog::where('user_id', $userId)
            ->where('processed_at', '>=', now()->subDays(30))
            ->count();

        return [
            'total_conversations' => $totalConversations,
            'by_type' => $byType,
            'last_30_days' => $last30Days,
        ];
    }

    /**
     * Get selected conversation details
     */
    #[Computed]
    public function selectedConversation(): ?MessageLog
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        return MessageLog::where('id', $this->selectedConversationId)
            ->where('user_id', Auth::id())
            ->first();
    }

    /**
     * Get guest conversations that can be claimed
     */
    #[Computed]
    public function claimableConversations()
    {
        if (! Auth::check()) {
            return collect();
        }

        return GuestConversation::where('email', Auth::user()->email)
            ->whereNull('claimed_by_user_id')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Claim guest conversation
     */
    public function claimGuestConversation(int $conversationId): void
    {
        $conversation = GuestConversation::where('id', $conversationId)
            ->where('email', Auth::user()->email)
            ->whereNull('claimed_by_user_id')
            ->first();

        if ($conversation) {
            $conversation->update([
                'claimed_by_user_id' => Auth::id(),
                'claimed_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ollama.conversation.claimed_successfully', [], 'ms'),
            ]);
        }
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.ollama.conversation-history');
    }
}
