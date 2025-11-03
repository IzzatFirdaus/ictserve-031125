<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Position extends Model implements Auditable
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
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ms' ? $this->name_ms : $this->name_en;
    }
}
