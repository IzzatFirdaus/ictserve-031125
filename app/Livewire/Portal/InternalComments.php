<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Events\CommentPosted;
use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\LoanApplication;
use App\Notifications\UserMentioned;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

/**
 * Internal comments widget component.
 * Supports threaded comments with @mentions for tickets and loan applications.
 *
 * Requirements: 7.1, 7.2, 7.3, 7.4, 7.5
 * Traceability: D03 SRS-FR-007, D04 §3.6
 */
class InternalComments extends Component
{
    public string $submissionType;

    public int $submissionId;

    public string $newComment = '';

    public ?int $replyingTo = null;

    public function mount(string $submissionType, int $submissionId): void
    {
        $this->submissionType = $submissionType;
        $this->submissionId = $submissionId;
    }

    public function addComment(): void
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        // Get commentable model
        $commentable = $this->getCommentable();

        // Create comment
        $comment = InternalComment::create([
            'user_id' => Auth::id(),
            'commentable_type' => get_class($commentable),
            'commentable_id' => $commentable->id,
            'parent_id' => $this->replyingTo,
            'comment' => $this->newComment,
        ]);

        // Parse and save mentions
        $mentionedUserIds = $this->parseMentions($this->newComment);
        if (! empty($mentionedUserIds)) {
            $comment->update(['mentions' => $mentionedUserIds]);

            // Send notifications to mentioned users
            $users = \App\Models\User::whereIn('id', $mentionedUserIds)->get();
            Notification::send($users, new UserMentioned($comment, Auth::user()));
        }

        // Broadcast the comment posted event
        event(new CommentPosted($comment));

        // Reset form
        $this->newComment = '';
        $this->replyingTo = null;
        $this->dispatch('comment-added');
    }

    public function replyToComment(int $commentId): void
    {
        $comment = InternalComment::find($commentId);

        // Check if comment can have replies (max depth 3)
        if ($comment && $comment->canHaveReplies()) {
            $this->replyingTo = $commentId;
        } else {
            $this->replyingTo = null;
        }
    }

    public function cancelReply(): void
    {
        $this->replyingTo = null;
    }

    protected function getCommentable()
    {
        if ($this->submissionType === 'ticket') {
            return HelpdeskTicket::findOrFail($this->submissionId);
        }

        return LoanApplication::findOrFail($this->submissionId);
    }

    protected function parseMentions(string $text): array
    {
        // Extract @mentions - supports @username and @"User Name"
        // Pattern matches:
        // 1. @"quoted name with spaces"
        // 2. @Name (captures until common English words or end of text)
        $pattern = '/@"([^"]+)"|@([^@\n]+?)(?=\s+(?:please|check|review|test|this|that|and|or|but|the|a|an|in|on|at|to|for|of|with|from)\b|\s*$|@)/i';
        preg_match_all($pattern, $text, $matches);

        // Combine quoted and unquoted mentions
        $usernames = array_filter(array_merge($matches[1], $matches[2]));

        // Trim whitespace from usernames
        $usernames = array_map(fn ($u) => trim($u), $usernames);

        if (empty($usernames)) {
            return [];
        }

        // Find users by exact name match
        return \App\Models\User::whereIn('name', $usernames)
            ->pluck('id')
            ->toArray();
    }

    public function render()
    {
        $commentable = $this->getCommentable();

        $comments = InternalComment::query()
            ->where('commentable_type', get_class($commentable))
            ->where('commentable_id', $commentable->id)
            ->whereNull('parent_id') // Top-level only
            ->with(['user', 'replies.user', 'replies.replies.user'])
            ->oldest()
            ->get();

        return view('livewire.portal.internal-comments', [
            'comments' => $comments,
        ]);
    }
}
