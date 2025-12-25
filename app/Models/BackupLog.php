<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Backup Log Model
 *
 * PKS Business Continuity (Requirement 29) - Backup Audit Trail
 *
 * Records all backup operations for compliance tracking and disaster recovery.
 *
 * @property int $id
 * @property string $backup_id
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $size_bytes
 * @property int|null $file_count
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.1, 29.3
 */
class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_id',
        'type',
        'status',
        'started_at',
        'completed_at',
        'verified_at',
        'size_bytes',
        'file_count',
        'error_message',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Scope: Successful backups
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereIn('status', ['completed', 'verified']);
    }

    /**
     * Scope: Failed backups
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: By type
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get human-readable size
     */
    public function getFormattedSizeAttribute(): string
    {
        if (! $this->size_bytes) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->size_bytes;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get duration in human-readable format
     */
    public function getDurationAttribute(): ?string
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        $seconds = $this->completed_at->diffInSeconds($this->started_at);

        if ($seconds < 60) {
            return $seconds.' saat';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return $minutes.' minit '.$remainingSeconds.' saat';
    }

    /**
     * Get backup type labels in Bahasa Melayu
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            'full' => 'Sandaran Penuh',
            'incremental' => 'Sandaran Tambahan',
            'database' => 'Pangkalan Data',
            'files' => 'Fail',
            'config' => 'Konfigurasi',
        ];
    }

    /**
     * Get backup status labels in Bahasa Melayu
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'failed' => 'Gagal',
            'verified' => 'Disahkan',
        ];
    }
}
