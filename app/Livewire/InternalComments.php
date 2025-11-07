<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\InternalComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InternalComments extends Component
{
    use WithPagination;

    // Required Props
    public string $submissionType; // 'helpdesk_ticket' or 'loan_application'

    public int $submissionId;

    // Component State
    public string $newCommentContent = '';

    public ?int $replyingToId = null;

    public ?int $editingCommentId = null;

    public string $editingContent = '';

    // Validation Rules
    protected array $rules = [
        'newCommentContent' => ['required', 'string', 'min:1', 'max:1000'],
        'editingContent' => ['required', 'string', 'min:1', 'max:1000'],
    ];

    /**
     * Mount component with required props
     */
    public function mount(string $submissionType, int $submissionId): void
    {
        $this->submissionType = $submissionType;
        $this->submissionId = $submissionId;
    }

    /**
     * Add new internal comment
     */
    public function addComment(): void
    {
        $this->validate(['newCommentContent' => $this->rules['newCommentContent']]);

        // Check max thread depth (3 levels)
        if ($this->replyingToId !== null) {
            $parentComment = InternalComment::find($this->replyingToId);
            $depth = $this->calculateDepth($parentComment);

            if ($depth >= 3) {
                session()->flash('comment-error', __('internal_comments.max_depth_reached'));

                return;
            }
        }

        InternalComment::create([
            'submission_type' => $this->submissionType,
            'submission_id' => $this->submissionId,
            'user_id' => Auth::id(),
            'parent_comment_id' => $this->replyingToId,
            'content' => $this->newCommentContent,
            'visibility' => 'internal', // Staff-only by default
        ]);

        $this->newCommentContent = '';
        $this->replyingToId = null;

        session()->flash('comment-success', __('internal_comments.added_success'));
        $this->dispatch('comment-added');
    }

    /**
     * Start replying to a comment
     */
    public function startReply(int $commentId): void
    {
        $this->replyingToId = $commentId;
        $this->dispatch('scroll-to-form');
    }

    /**
     * Cancel reply
     */
    public function cancelReply(): void
    {
        $this->replyingToId = null;
    }

    /**
     * Start editing a comment
     */
    public function startEdit(int $commentId): void
    {
        $comment = InternalComment::findOrFail($commentId);

        // Authorization: Only owner or Admin/Superuser can edit
        if ($comment->user_id !== Auth::id() && ! Auth::user()->hasAnyRole(['Admin', 'Superuser'])) {
            session()->flash('comment-error', __('internal_comments.unauthorized_edit'));

            return;
        }

        $this->editingCommentId = $commentId;
        $this->editingContent = $comment->content;
    }

    /**
     * Save edited comment
     */
    public function saveEdit(): void
    {
        $this->validate(['editingContent' => $this->rules['editingContent']]);

        $comment = InternalComment::findOrFail($this->editingCommentId);

        // Re-check authorization
        if ($comment->user_id !== Auth::id() && ! Auth::user()->hasAnyRole(['Admin', 'Superuser'])) {
            session()->flash('comment-error', __('internal_comments.unauthorized_edit'));

            return;
        }

        $comment->update(['content' => $this->editingContent]);

        $this->editingCommentId = null;
        $this->editingContent = '';

        session()->flash('comment-success', __('internal_comments.updated_success'));
    }

    /**
     * Cancel editing
     */
    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    /**
     * Delete comment
     */
    public function deleteComment(int $commentId): void
    {
        $comment = InternalComment::findOrFail($commentId);

        // Authorization: Only owner or Admin/Superuser can delete
        if ($comment->user_id !== Auth::id() && ! Auth::user()->hasAnyRole(['Admin', 'Superuser'])) {
            session()->flash('comment-error', __('internal_comments.unauthorized_delete'));

            return;
        }

        $comment->delete();

        session()->flash('comment-success', __('internal_comments.deleted_success'));
    }

    /**
     * Calculate comment thread depth
     */
    private function calculateDepth(?InternalComment $comment): int
    {
        $depth = 0;

        while ($comment !== null && $comment->parent_comment_id !== null) {
            $depth++;
            $comment = $comment->parent;
        }

        return $depth;
    }

    /**
     * Handle new comment from Echo broadcast.
     */
    public function handleEchoCommentPosted(array $event): void
    {
        // Check if this comment is for current submission
        if ($event['submission_type'] === $this->submissionType && $event['submission_id'] === $this->submissionId) {
            // Refresh comments list
            $this->resetPage();
            session()->flash('comment-info', __('internal_comments.new_comment_posted', [
                'user' => $event['comment']['user']['name'] ?? __('portal.unknown_user'),
            ]));
        }
    }

    /**
     * Get event listeners for Echo integration.
     *
     * @return array<string, string>
     */
    protected function getListeners(): array
    {
        return [
            'echo:comment-posted' => 'handleEchoCommentPosted',
            'comment-added' => '$refresh',
            'scroll-to-form' => '$refresh',
        ];
    }

    /**
     * Render component
     */
    public function render(): View
    {
        // Get top-level comments with nested replies
        $comments = InternalComment::with(['user', 'replies.user', 'replies.replies.user'])
            ->where('submission_type', $this->submissionType)
            ->where('submission_id', $this->submissionId)
            ->whereNull('parent_comment_id') // Only top-level comments
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.internal-comments', [
            'comments' => $comments,
        ]);
    }
}
