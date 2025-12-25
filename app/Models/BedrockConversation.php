<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BedrockConversation Model - PKS 5.2.1 Compliant
 *
 * Stores AI chat conversations with mandatory user_id linkage
 * per PKS 5.2.1 accountability requirements.
 *
 * @property int $id
 * @property int $user_id MANDATORY - NOT NULL per PKS 5.2.1
 * @property string|null $title
 * @property array<array-key, mixed> $messages
 * @property string $model
 * @property int $total_tokens
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereTotalTokens($value)
 * @method static \Database\Factories\BedrockConversationFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class BedrockConversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',      // MANDATORY - NOT NULL per PKS 5.2.1
        'title',
        'messages',
        'model',
        'total_tokens',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'messages' => 'array',
            'total_tokens' => 'integer',
        ];
    }

    /**
     * Get the user that owns the conversation.
     * PKS 5.2.1 - Mandatory user relationship for accountability.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get conversations for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the message count for this conversation.
     */
    public function getMessageCountAttribute(): int
    {
        return count($this->messages ?? []);
    }

    /**
     * Check if conversation belongs to a specific user.
     * PKS 5.2.1 - Verify ownership for accountability.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }
}
