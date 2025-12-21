<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Widget Registry Model
 *
 * Stores widget registration information including configuration,
 * categorization, and access control metadata.
 *
 * @property int $id
 * @property string $widget_class
 * @property string $category
 * @property int $sort_order
 * @property bool $is_active
 * @property array $configuration
 * @property array $roles
 * @property int $refresh_rate
 * @property int $cache_ttl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D09 Database Documentation - Dual Audit System
 *
 * @version 3.6.1
 */
class WidgetRegistry extends Model implements Auditable
{
    use HasFactory;
    use LogsActivity;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'widget_registries';

    protected $fillable = [
        'widget_class',
        'category',
        'sort_order',
        'is_active',
        'configuration',
        'roles',
        'refresh_rate',
        'cache_ttl',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
        'roles' => 'array',
        'sort_order' => 'integer',
        'refresh_rate' => 'integer',
        'cache_ttl' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 1,
        'refresh_rate' => 300, // 5 minutes default
        'cache_ttl' => 600,    // 10 minutes default
    ];

    /**
     * Get the activity log options for audit trail
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'widget_class',
                'category',
                'sort_order',
                'is_active',
                'configuration',
                'roles',
                'refresh_rate',
                'cache_ttl',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Widget registered in registry',
                'updated' => 'Widget configuration updated',
                'deleted' => 'Widget deregistered from registry',
                default => "Widget registry {$eventName}",
            });
    }

    /**
     * Scope: Active widgets only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Widgets by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Widgets accessible by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->whereJsonContains('roles', $role);
    }

    /**
     * Scope: Ordered by category and sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('widget_class');
    }

    /**
     * Check if widget is accessible by user role
     */
    public function isAccessibleByRole(string $role): bool
    {
        return \in_array($role, $this->roles ?? [], true);
    }

    /**
     * Get widget configuration value
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->configuration, $key, $default);
    }

    /**
     * Set widget configuration value
     */
    public function setConfigValue(string $key, mixed $value): void
    {
        $config = $this->configuration ?? [];
        data_set($config, $key, $value);
        $this->configuration = $config;
    }

    /**
     * Check if widget should be cached
     */
    public function shouldCache(): bool
    {
        return $this->cache_ttl > 0;
    }

    /**
     * Get cache key for this widget
     */
    public function getCacheKey(string $suffix = ''): string
    {
        $key = "widget.{$this->widget_class}";

        if ($suffix) {
            $key .= ".{$suffix}";
        }

        return $key;
    }

    /**
     * Check if widget needs refresh based on refresh rate
     */
    public function needsRefresh(): bool
    {
        if (! $this->updated_at) {
            return true;
        }

        return $this->updated_at->addSeconds($this->refresh_rate)->isPast();
    }

    /**
     * Get widget display name from class
     */
    public function getDisplayName(): string
    {
        $className = class_basename($this->widget_class);

        // Convert PascalCase to readable format
        return preg_replace('/(?<!^)[A-Z]/', ' $0', $className) ?? $className;
    }

    /**
     * Validate widget class exists and is valid
     */
    public function validateWidgetClass(): bool
    {
        if (! class_exists($this->widget_class)) {
            return false;
        }

        $reflection = new \ReflectionClass($this->widget_class);

        // Check if it extends a valid Filament widget base class
        $validBaseClasses = [
            'Filament\Widgets\Widget',
            'Filament\Widgets\StatsOverviewWidget',
            'Filament\Widgets\ChartWidget',
            'Filament\Widgets\TableWidget',
        ];

        foreach ($validBaseClasses as $baseClass) {
            if ($reflection->isSubclassOf($baseClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get widget signature for duplicate detection
     */
    public function getSignature(): string
    {
        return md5(serialize([
            'class' => $this->widget_class,
            'category' => $this->category,
            'sort_order' => $this->sort_order,
        ]));
    }
}
