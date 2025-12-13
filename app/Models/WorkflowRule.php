<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowRule extends Model implements Auditable
{
    use AuditableTrait, HasFactory;

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
}
