<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $module
 * @property string $frequency
 * @property \Illuminate\Support\Carbon $schedule_time
 * @property int|null $schedule_day_of_week
 * @property int|null $schedule_day_of_month
 * @property array<array-key, mixed> $recipients
 * @property array<array-key, mixed>|null $filters
 * @property string $format
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_run_at
 * @property \Illuminate\Support\Carbon|null $next_run_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $frequency_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule due()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereNextRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleDayOfMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\ReportScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'module',
        'frequency',
        'schedule_time',
        'schedule_day_of_week',
        'schedule_day_of_month',
        'recipients',
        'filters',
        'format',
        'is_active',
        'last_run_at',
        'next_run_at',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'filters' => 'array',
            'schedule_time' => 'datetime:H:i:s',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Calculate next run time based on frequency and schedule settings
     */
    public function calculateNextRunTime(): Carbon
    {
        $now = now();
        $scheduleTime = Carbon::parse($this->schedule_time);

        return match ($this->frequency) {
            'daily' => $now->copy()
                ->setTime($scheduleTime->hour, $scheduleTime->minute, $scheduleTime->second)
                ->addDay(),

            'weekly' => $now->copy()
                ->next($this->schedule_day_of_week)
                ->setTime($scheduleTime->hour, $scheduleTime->minute, $scheduleTime->second),

            'monthly' => $now->copy()
                ->addMonth()
                ->day($this->schedule_day_of_month)
                ->setTime($scheduleTime->hour, $scheduleTime->minute, $scheduleTime->second),
        };
    }

    /**
     * Check if schedule is due for execution
     */
    public function isDue(): bool
    {
        return $this->is_active &&
               $this->next_run_at &&
               $this->next_run_at->isPast();
    }

    /**
     * Mark schedule as executed and calculate next run time
     */
    public function markAsExecuted(): void
    {
        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRunTime(),
        ]);
    }

    /**
     * Get human-readable frequency description
     */
    public function getFrequencyDescriptionAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => "Daily at {$this->schedule_time->format('H:i')}",
            'weekly' => 'Weekly on '.Carbon::create()->dayOfWeek($this->schedule_day_of_week)->format('l')." at {$this->schedule_time->format('H:i')}",
            'monthly' => "Monthly on day {$this->schedule_day_of_month} at {$this->schedule_time->format('H:i')}",
        };
    }

    /**
     * Scope for active schedules
     *
     * @param  \Illuminate\Database\Eloquent\Builder<ReportSchedule>  $query
     * @return \Illuminate\Database\Eloquent\Builder<ReportSchedule>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for due schedules
     *
     * @param  \Illuminate\Database\Eloquent\Builder<ReportSchedule>  $query
     * @return \Illuminate\Database\Eloquent\Builder<ReportSchedule>
     */
    public function scopeDue($query)
    {
        return $query->active()
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now());
    }
}
