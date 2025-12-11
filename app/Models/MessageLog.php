<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Model MessageLog untuk jejak audit operasi AI
 *
 * Menyokong True Hybrid Architecture dengan nullable user_id FK
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $request_id X-Request-ID untuk kebolehkesanan
 * @property string $operation_type Jenis operasi AI
 * @property int|null $user_id Pengguna (nullable untuk tetamu)
 * @property string $sanitized_input Input yang disanitasi
 * @property string|null $response_summary Ringkasan respons
 * @property array|null $metadata Model, token, masa pemprosesan
 * @property string $hash SHA-256 hash untuk immutability
 * @property string|null $previous_hash Chain of custody
 * @property \Carbon\Carbon $processed_at Masa pemprosesan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MessageLog extends Model implements AuditableContract
{
    use HasFactory;
    use Auditable; // owen-it untuk compliance audit
    use LogsActivity; // spatie untuk operational logging

    /**
     * Nama jadual pangkalan data
     */
    protected $table = 'message_logs';

    /**
     * Atribut yang boleh diisi secara massal
     */
    protected $fillable = [
        'request_id',
        'operation_type',
        'user_id',
        'sanitized_input',
        'response_summary',
        'metadata',
        'hash',
        'previous_hash',
        'processed_at',
    ];

    /**
     * Casting atribut ke jenis data yang sesuai
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Atribut yang perlu dilog untuk activity logging (spatie)
     */
    protected static $logAttributes = [
        'request_id',
        'operation_type',
        'user_id',
        'hash',
        'processed_at',
    ];

    /**
     * Nama log untuk activity logging
     */
    protected static $logName = 'ai_message_log';

    /**
     * Log hanya perubahan atribut yang kotor
     */
    protected static $logOnlyDirty = true;

    /**
     * Hubungan dengan model User (nullable untuk True Hybrid Architecture)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Scope untuk menapis mengikut jenis operasi
     */
    public function scopeByOperationType($query, string $operationType)
    {
        return $query->where('operation_type', $operationType);
    }

    /**
     * Scope untuk menapis mengikut julat tarikh
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('processed_at', [$startDate, $endDate]);
    }

    /**
     * Accessor untuk mendapatkan nama pengguna atau 'Tetamu'
     */
    public function getUserDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Tetamu';
    }

    /**
     * Mutator untuk memastikan hash selalu dalam huruf kecil
     */
    public function setHashAttribute(string $value): void
    {
        $this->attributes['hash'] = strtolower($value);
    }

    /**
     * Mutator untuk memastikan previous_hash selalu dalam huruf kecil
     */
    public function setPreviousHashAttribute(?string $value): void
    {
        $this->attributes['previous_hash'] = $value ? strtolower($value) : null;
    }

    /**
     * Konfigurasi activity log untuk spatie
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(static::$logAttributes)
            ->logOnlyDirty()
            ->useLogName(static::$logName);
    }
}
