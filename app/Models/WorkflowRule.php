<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $name
 * @property string $module
 * @property string|null $description
 * @property array<array-key, mixed> $conditions
 * @property array<array-key, mixed> $actions
 * @property bool $is_active
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule byPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule forModule(string $module)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereActions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class WorkflowRule extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'name',
        'module',
        'description',
        'conditions',
        'actions',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Scope for active workflow rules
     *
     * @param  \Illuminate\Database\Eloquent\Builder<WorkflowRule>  $query
     * @return \Illuminate\Database\Eloquent\Builder<WorkflowRule>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by module
     *
     * @param  \Illuminate\Database\Eloquent\Builder<WorkflowRule>  $query
     * @return \Illuminate\Database\Eloquent\Builder<WorkflowRule>
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for ordering by priority
     *
     * @param  \Illuminate\Database\Eloquent\Builder<WorkflowRule>  $query
     * @return \Illuminate\Database\Eloquent\Builder<WorkflowRule>
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
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
                'name',
                'module',
                'conditions',
                'actions',
                'is_active',
                'priority',
            ])
            ->logOnlyDirty()
            ->useLogName('workflow')
            ->setDescriptionForEvent(fn (string $eventName) => "Workflow rule {$eventName}");
    }
}
