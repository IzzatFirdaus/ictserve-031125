<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Model AutoReplyTemplate untuk sistem AI Ollama
 * 
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply template management
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $name
 * @property string $template_content
 * @property array|null $variables
 * @property string $status
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\AutoReplyDraft> $drafts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read int|null $drafts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate draft()
 * @method static \Database\Factories\AutoReplyTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereTemplateContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate whereVariables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class AutoReplyTemplate extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Template statuses
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'template_content',
        'variables',
        'status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'variables' => 'array',
        ];
    }

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'template_content',
                'status',
                'created_by',
            ])
            ->logOnlyDirty()
            ->useLogName('auto_reply_template')
            ->setDescriptionForEvent(fn (string $eventName) => "Auto reply template {$eventName}");
    }

    /**
     * Hubungan dengan User yang mencipta template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hubungan dengan AutoReplyDraft
     */
    public function drafts(): HasMany
    {
        return $this->hasMany(AutoReplyDraft::class, 'template_id');
    }

    /**
     * Scope untuk template aktif
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope untuk template draft
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Semak sama ada template aktif
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Semak sama ada template dalam draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Semak sama ada template diarkibkan
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Proses template dengan pembolehubah
     */
    

/**
 * @param array<string, mixed> $variables
 */
public function processTemplate(array $variables = []): string
    {
        $content = $this->template_content;

        // Replace template variables dengan format {{variable_name}}
        foreach ($variables as $key => $value) {
            $content = str_replace('{{'.$key.'}}', (string) $value, $content);
        }

        return $content;
    }

    /**
     * Dapatkan senarai pembolehubah yang diperlukan dalam template
     */
    public function getRequiredVariables(): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $this->template_content, $matches);

        return array_unique($matches[1] ?? []);
    }

    /**
     * Sahkan sama ada semua pembolehubah yang diperlukan disediakan
     */
    

/**
 * @param array<string, mixed> $variables
 */
public function validateVariables(array $variables): bool
    {
        $required = $this->getRequiredVariables();

        foreach ($required as $variable) {
            if (! array_key_exists($variable, $variables)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Aktifkan template
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Arkibkan template
     */
    public function archive(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }
}
