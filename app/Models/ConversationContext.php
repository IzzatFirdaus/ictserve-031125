<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property array<array-key, mixed>|null $context_data
 * @property array<array-key, mixed>|null $personalization_data
 * @property \Illuminate\Support\Carbon|null $last_interaction
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ConversationContextFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereContextData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereLastInteraction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext wherePersonalizationData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext whereUserId($value)
 * @mixin \Eloquent
 */
class ConversationContext extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'context_data',
        'personalization_data',
        'last_interaction',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'context_data' => 'array',
            'personalization_data' => 'array',
            'last_interaction' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
