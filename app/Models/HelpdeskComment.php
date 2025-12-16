<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $helpdesk_ticket_id
 * @property int|null $user_id
 * @property string|null $commenter_name
 * @property string|null $commenter_email
 * @property string $comment
 * @property bool $is_internal
 * @property bool $is_resolution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCommenterEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCommenterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereHelpdeskTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereIsResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class HelpdeskComment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\HelpdeskCommentFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'helpdesk_ticket_id',
        'user_id',
        'commenter_name',
        'commenter_email',
        'comment',
        'is_internal',
        'is_resolution',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_resolution' => 'boolean',
    ];

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'helpdesk_ticket_id',
                'user_id',
                'commenter_name',
                'commenter_email',
                'is_internal',
                'is_resolution',
            ])
            ->logOnlyDirty()
            ->useLogName('helpdesk_comment')
            ->setDescriptionForEvent(fn (string $eventName) => "Helpdesk comment {$eventName}");
    }

    public function helpdeskTicket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class);
    }

    /** @return BelongsTo<User, HelpdeskComment> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
