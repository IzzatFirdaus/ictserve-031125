<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $code
 * @property string $name_ms
 * @property string $name_en
 * @property string|null $description_ms
 * @property string|null $description_en
 * @property int|null $parent_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Division> $children
 * @property-read int|null $children_count
 * @property-read string|null $description
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read Division|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescriptionMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class Division extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name_ms',
        'name_en',
        'description_ms',
        'description_en',
        'parent_id',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
                'code',
                'name_ms',
                'name_en',
                'parent_id',
                'is_active',
            ])
            ->logOnlyDirty()
            ->useLogName('division')
            ->setDescriptionForEvent(fn (string $eventName) => "Division {$eventName}");
    }

    // Relationships
    /** @return BelongsTo<Division, Division> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    /** @return HasMany<Division, Division> */
    public function children(): HasMany
    {
        return $this->hasMany(Division::class, 'parent_id');
    }

    /** @return HasMany<User, Division> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<HelpdeskTicket, Division> */
    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class);
    }

    /** @return HasMany<Asset, Division> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /** @return HasMany<LoanApplication, Division> */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name_ms'] = $value;
        $this->attributes['name_en'] = $value;
    }

    // Helper methods
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ms' ? $this->name_ms : $this->name_en;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ms' ? $this->description_ms : $this->description_en;
    }
}
