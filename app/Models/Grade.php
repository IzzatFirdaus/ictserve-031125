<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property int $level
 * @property bool $can_approve_loans
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCanApproveLoans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class Grade extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\GradeFactory> */
    use HasFactory;

    use LogsActivity;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name_ms',
        'name_en',
        'level',
        'can_approve_loans',
    ];

    protected $casts = [
        'level' => 'integer',
        'can_approve_loans' => 'boolean',
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
                'code',
                'name_ms',
                'name_en',
                'level',
                'can_approve_loans',
            ])
            ->logOnlyDirty()
            ->useLogName('grade')
            ->setDescriptionForEvent(fn (string $eventName) => "Grade {$eventName}");
    }

    // Relationships
    /** @return HasMany<User, Grade> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<LoanApplication, Grade> */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    // Helper methods
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ms' ? $this->name_ms : $this->name_en;
    }

    public function isApprover(): bool
    {
        return $this->can_approve_loans;
    }
}
