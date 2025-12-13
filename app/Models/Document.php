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
 * Model Document untuk sistem AI Ollama
 *
 * Per Requirements 2.1, 2.2, 4.1: Document management dengan True Hybrid Architecture
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $filename
 * @property array|null $metadata
 * @property int|null $uploaded_by
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $uploader
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\DocumentChunk> $chunks
 */
class Document extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes;

    /**
     * Document processing statuses
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'filename',
        'metadata',
        'uploaded_by',
        'status',
        'processing_model',
        'bedrock_analysis',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'bedrock_analysis' => 'array',
        ];
    }

    /**
     * Hubungan dengan User yang memuat naik dokumen
     * True Hybrid Architecture: nullable untuk sokongan guest/authenticated
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Hubungan dengan DocumentChunk
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }

    /**
     * Scope untuk dokumen dengan status tertentu
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk dokumen yang telah selesai diproses
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope untuk dokumen yang gagal diproses
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Semak sama ada dokumen sedang diproses
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Semak sama ada dokumen telah selesai diproses
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Semak sama ada dokumen gagal diproses
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Dapatkan saiz fail dalam format yang boleh dibaca
     */
    public function getFileSizeAttribute(): ?string
    {
        $size = $this->metadata['size'] ?? null;

        if (! $size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[$power];
    }

    /**
     * Dapatkan jenis fail
     */
    public function getFileTypeAttribute(): ?string
    {
        return $this->metadata['mime_type'] ?? pathinfo($this->filename, PATHINFO_EXTENSION);
    }
}
