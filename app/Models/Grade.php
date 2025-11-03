<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Grade extends Model implements Auditable
{
    use HasFactory;
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

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

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
