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
 * @property string $filename
 * @property string $original_filename
 * @property string $mime_type
 * @property int $file_size
 * @property string $file_path
 * @property string $disk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereHelpdeskTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class HelpdeskAttachment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\HelpdeskAttachmentFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'helpdesk_ticket_id',
        'user_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        'disk',
    ];

    protected $casts = [
        'file_size' => 'integer',
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
                'filename',
                'original_filename',
                'file_size',
            ])
            ->logOnlyDirty()
            ->useLogName('helpdesk_attachment')
            ->setDescriptionForEvent(fn (string $eventName) => "Helpdesk attachment {$eventName}");
    }

    public function helpdeskTicket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class);
    }

    /** @return BelongsTo<User, HelpdeskAttachment> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
