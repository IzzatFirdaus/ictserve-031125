<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Asset Category Model
 *
 * Defines categories for ICT equipment with custom specification templates.
 *
 * @see D03-FR-018.2 Asset categorization system
 * @see D04 §2.2 Model relationships
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property array|null $specification_template
 * @property int $default_loan_duration_days
 * @property int $max_loan_duration_days
 * @property bool $requires_approval
 * @property bool $is_active
 * @property int $sort_order
 */
class AssetCategory extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'specification_template',
        'default_loan_duration_days',
        'max_loan_duration_days',
        'requires_approval',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'specification_template' => 'array',
        'default_loan_duration_days' => 'integer',
        'max_loan_duration_days' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    // Helper methods
    public function getAvailableAssetsCount(): int
    {
        return $this->assets()->where('status', 'available')->count();
    }

    public function getTotalAssetsCount(): int
    {
        return $this->assets()->count();
    }
}
