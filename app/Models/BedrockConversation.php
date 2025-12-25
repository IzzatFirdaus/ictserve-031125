<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $title
 * @property array<array-key, mixed> $messages
 * @property string $model
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 *
 * @property int|null $user_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereUserId($value)
 *
 * @property int $total_tokens
 *
 * @method static \Database\Factories\BedrockConversationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation whereTotalTokens($value)
 *
 * @mixin \Eloquent
 */
class BedrockConversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'messages', 'model', 'total_tokens'];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
        ];
    }
}
