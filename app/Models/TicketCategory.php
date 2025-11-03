<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TicketCategory extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'code',
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

    public function children(): HasMany
    {
        return $this->hasMany(TicketCategory::class, 'parent_id');
    }

    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'category_id');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ms' ? $this->name_ms : $this->name_en;
    }
}
