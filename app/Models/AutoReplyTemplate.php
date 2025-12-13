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
 */
class AutoReplyTemplate extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

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
     * Hubungan dengan User yang mencipta template
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hubungan dengan AutoReplyDraft
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drafts(): HasMany
    {
        return $this->hasMany(AutoReplyDraft::class, 'template_id');
    }

    /**
     * Scope untuk template aktif
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope untuk template draft
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Semak sama ada template aktif
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Semak sama ada template dalam draft
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Semak sama ada template diarkibkan
     *
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Proses template dengan pembolehubah
     *
     * @param array $variables
     * @return string
     */
    public function processTemplate(array $variables = []): string
    {
        $content = $this->template_content;

        // Replace template variables dengan format {{variable_name}}
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', (string) $value, $content);
        }

        return $content;
    }

    /**
     * Dapatkan senarai pembolehubah yang diperlukan dalam template
     *
     * @return array
     */
    public function getRequiredVariables(): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $this->template_content, $matches);

        return array_unique($matches[1] ?? []);
    }

    /**
     * Sahkan sama ada semua pembolehubah yang diperlukan disediakan
     *
     * @param array $variables
     * @return bool
     */
    public function validateVariables(array $variables): bool
    {
        $required = $this->getRequiredVariables();

        foreach ($required as $variable) {
            if (!array_key_exists($variable, $variables)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Aktifkan template
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Arkibkan template
     *
     * @return bool
     */
    public function archive(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }
}
