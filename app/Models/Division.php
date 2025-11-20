<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Division extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
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
