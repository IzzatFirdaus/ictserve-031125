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
 * @property string|null $bedrock_model_used
 * @property numeric|null $bedrock_cost
 * @property array<array-key, mixed>|null $web_sources_used
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $user_display_name
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog byDateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog byOperationType(string $operationType)
 * @method static \Database\Factories\MessageLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereBedrockCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereBedrockModelUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereOperationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog wherePreviousHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereResponseSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereSanitizedInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog whereWebSourcesUsed($value)
 * @mixin \Eloquent
 */
class MessageLog extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory; // owen-it untuk compliance audit
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
        'bedrock_model_used',
        'bedrock_cost',
        'web_sources_used',
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
            'web_sources_used' => 'array',
            'bedrock_cost' => 'decimal:6',
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
        return $this->user?->exists ? $this->user->name : 'Tetamu';
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
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly([
                'request_id',
                'operation_type',
                'user_id',
                'hash',
                'processed_at',
            ])
            ->logOnlyDirty()
            ->useLogName('ai_message_log')
            ->setDescriptionForEvent(fn (string $eventName) => "AI message log {$eventName}");
    }
}
