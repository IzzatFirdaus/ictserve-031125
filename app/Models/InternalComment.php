<?php

declare(strict_types=1);

// name: InternalComment
// description: Internal staff-only comments on submissions with threading support
// author: dev-team@motac.gov.my
// trace: SRS-FR-002; D04 §4.2; D11 §7; Requirements 7.1, 7.2, 7.3
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InternalComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'comment',
        'mentions',
    ];

    protected function casts(): array
    {
        return [
            'mentions' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the commentable model (ticket or loan)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the parent comment (for threading)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(InternalComment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(InternalComment::class, 'parent_id');
    }

    /**
     * Scope: Get top-level comments (no parent)
     */
    public function scopeTopLevel(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get comments with mentions for a specific user
     */
    public function scopeWithMentionFor(\Illuminate\Database\Eloquent\Builder $query, int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereJsonContains('mentions', $userId);
    }

    /**
     * Check if comment mentions a specific user
     */
    public function mentionsUser(int $userId): bool
    {
        $mentions = $this->mentions ?? [];

        return in_array($userId, $mentions, true);
    }

    /**
     * Get the depth of this comment in the thread (0 = top-level, max 3)
     */
    public function getDepth(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id !== null && $depth < 3) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
    }

    /**
     * Check if this comment can have replies (max depth 3)
     */
    public function canHaveReplies(): bool
    {
        return $this->getDepth() < 3;
    }

    /**
     * Parse @mentions from comment text and extract user IDs
     * Format: @username or @"User Name"
     */
    public function parseMentions(): array
    {
        $pattern = '/@"([^"]+)"|@(\w+)/';
        preg_match_all($pattern, $this->comment, $matches);

        $usernames = array_filter(array_merge($matches[1], $matches[2]));

        if (empty($usernames)) {
            return [];
        }

        // Find users by name or email
        $users = User::whereIn('name', $usernames)
            ->orWhereIn('email', array_map(fn ($u) => $u.'@motac.gov.my', $usernames))
            ->pluck('id')
            ->toArray();

        return $users;
    }

    /**
     * Get formatted comment with clickable mentions
     */
    public function getFormattedCommentAttribute(): string
    {
        $comment = $this->comment;
        $mentions = $this->mentions ?? [];

        if (empty($mentions)) {
            return $comment;
        }

        $users = User::whereIn('id', $mentions)->get();

        foreach ($users as $user) {
            $pattern = '/@"?'.preg_quote($user->name, '/').'"?/i';
            $replacement = '<a href="/portal/users/'.$user->id.'" class="mention">@'.$user->name.'</a>';
            $comment = preg_replace($pattern, $replacement, $comment);
        }

        return $comment;
    }

    /**
     * Get all nested replies recursively (for display)
     */
    public function getNestedReplies(): \Illuminate\Support\Collection
    {
        return $this->replies()
            ->with(['user', 'replies'])
            ->oldest()
            ->get()
            ->map(function ($reply) {
                $reply->nested_replies = $reply->getNestedReplies();

                return $reply;
            });
    }
}
