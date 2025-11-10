<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSchedule extends Model
{
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
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for due schedules
     */
    public function scopeDue($query)
    {
        return $query->active()
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now());
    }
}
