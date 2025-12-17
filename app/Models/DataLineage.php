<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Model DataLineage untuk penjejakan lineage data AI
 * 
 * Merekod transformasi data untuk pematuhan PDPA 2010
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $lineage_id ID unik untuk lineage tracking
 * @property string $source_type Jenis sumber data
 * @property int $source_id ID sumber
 * @property string $transformation_type Jenis transformasi
 * @property array $transformation_metadata Metadata transformasi
 * @property string $destination_type Jenis destinasi
 * @property int|null $destination_id ID destinasi
 * @property \Carbon\Carbon $processed_at Masa pemprosesan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $transformation_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage byDestination(string $destinationType, ?int $destinationId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage bySource(string $sourceType, int $sourceId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage bySourceType(string $sourceType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage byTransformationType(string $transformationType)
 * @method static \Database\Factories\DataLineageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereDestinationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereLineageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereTransformationMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereTransformationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DataLineage extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory; // owen-it untuk compliance audit
    use LogsActivity; // spatie untuk operational logging

    /**
     * Nama jadual pangkalan data
     */
    protected $table = 'data_lineage';

    /**
     * Atribut yang boleh diisi secara massal
     */
    protected $fillable = [
        'lineage_id',
        'source_type',
        'source_id',
        'transformation_type',
        'transformation_metadata',
        'destination_type',
        'destination_id',
        'processed_at',
    ];

    /**
     * Casting atribut ke jenis data yang sesuai
     */
    protected function casts(): array
    {
        return [
            'transformation_metadata' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Atribut yang perlu dilog untuk activity logging (spatie)
     */
    protected static $logAttributes = [
        'lineage_id',
        'source_type',
        'source_id',
        'transformation_type',
        'destination_type',
        'destination_id',
    ];

    /**
     * Nama log untuk activity logging
     */
    protected static $logName = 'ai_data_lineage';

    /**
     * Log hanya perubahan atribut yang kotor
     */
    protected static $logOnlyDirty = true;

    /**
     * Scope untuk menapis mengikut jenis sumber
     */
    public function scopeBySourceType($query, string $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    /**
     * Scope untuk menapis mengikut jenis transformasi
     */
    public function scopeByTransformationType($query, string $transformationType)
    {
        return $query->where('transformation_type', $transformationType);
    }

    /**
     * Scope untuk menapis mengikut sumber tertentu
     */
    public function scopeBySource($query, string $sourceType, int $sourceId)
    {
        return $query->where('source_type', $sourceType)
            ->where('source_id', $sourceId);
    }

    /**
     * Scope untuk menapis mengikut destinasi tertentu
     */
    public function scopeByDestination($query, string $destinationType, ?int $destinationId = null)
    {
        $query = $query->where('destination_type', $destinationType);

        if ($destinationId !== null) {
            $query->where('destination_id', $destinationId);
        }

        return $query;
    }

    /**
     * Accessor untuk mendapatkan penerangan transformasi yang mudah dibaca
     */
    public function getTransformationDescriptionAttribute(): string
    {
        return sprintf(
            '%s (%s:%d) → %s melalui %s',
            ucfirst($this->source_type),
            $this->source_type,
            $this->source_id,
            ucfirst($this->destination_type),
            $this->transformation_type
        );
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
                'lineage_id',
                'source_type',
                'source_id',
                'transformation_type',
                'destination_type',
                'destination_id',
            ])
            ->logOnlyDirty()
            ->useLogName('ai_data_lineage')
            ->setDescriptionForEvent(fn (string $eventName) => "AI data lineage {$eventName}");
    }
}
