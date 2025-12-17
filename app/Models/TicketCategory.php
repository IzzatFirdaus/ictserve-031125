<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $name_ms
 * @property string $name_en
 * @property string|null $description_ms
 * @property string|null $description_en
 * @property int|null $parent_id
 * @property int $sla_response_hours
 * @property int $sla_resolution_hours
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TicketCategory> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read TicketCategory|null $parent
 * @method static \Database\Factories\TicketCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescriptionMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereSlaResolutionHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereSlaResponseHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class TicketCategory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\TicketCategoryFactory> */
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'name_ms',
        'name_en',
        'description_ms',
        'description_en',
        'parent_id',
        'sla_response_hours',
        'sla_resolution_hours',
        'is_active',
    ];

    protected $casts = [
        'sla_response_hours' => 'integer',
        'sla_resolution_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'parent_id');
    }

    /** @return HasMany<TicketCategory, TicketCategory> */
    public function children(): HasMany
    {
        return $this->hasMany(TicketCategory::class, 'parent_id');
    }

    /** @return HasMany<HelpdeskTicket, TicketCategory> */
    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'category_id');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale() === 'ms' ? 'name_ms' : 'name_en';

        return $this->attributes[$locale] ?? $this->getAttribute($locale) ?? 'Unknown';
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
                'code',
                'name_ms',
                'name_en',
                'sla_response_hours',
                'sla_resolution_hours',
                'is_active',
            ])
            ->logOnlyDirty()
            ->useLogName('ticket_category')
            ->setDescriptionForEvent(fn (string $eventName) => "Ticket category {$eventName}");
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name_ms'] = $value;
        $this->attributes['name_en'] = $value;
    }
}
