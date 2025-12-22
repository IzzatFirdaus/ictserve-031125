<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Activity Model for Spatie Activity Log
 * 
 * Represents user activity logs for operational dashboards and reports.
 * Part of the Dual Audit System complementing owen-it/laravel-auditing.
 *
 * @see D09 §4.7 Activity logging requirements
 * @see Requirements 19.2, 19.4
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $event
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property array<string, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|null $subject
 * @property-read Model|null $causer
 * @property-read string $causer_name
 * @property-read string $subject_name
 * @property-read \Illuminate\Database\Eloquent\Model|null $user
 * @method static Builder<static>|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|Activity dateRange(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate)
 * @method static Builder<static>|Activity forEvent(string $event)
 * @method static Builder<static>|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|Activity inLog(string ...$logNames)
 * @method static Builder<static>|Activity newModelQuery()
 * @method static Builder<static>|Activity newQuery()
 * @method static Builder<static>|Activity query()
 * @method static Builder<static>|Activity whereBatchUuid($value)
 * @method static Builder<static>|Activity whereCauserId($value)
 * @method static Builder<static>|Activity whereCauserType($value)
 * @method static Builder<static>|Activity whereCreatedAt($value)
 * @method static Builder<static>|Activity whereDescription($value)
 * @method static Builder<static>|Activity whereEvent($value)
 * @method static Builder<static>|Activity whereId($value)
 * @method static Builder<static>|Activity whereLogName($value)
 * @method static Builder<static>|Activity whereProperties($value)
 * @method static Builder<static>|Activity whereSubjectId($value)
 * @method static Builder<static>|Activity whereSubjectType($value)
 * @method static Builder<static>|Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'activity_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the subject of the activity.
     *
     * @return MorphTo<Model, Activity>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     *
     * @return MorphTo<Model, Activity>
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Alias for causer to align with audit model API.
     *
     * @return MorphTo<Model, Activity>
     */
    public function user(): MorphTo
    {
        return $this->causer();
    }

    /**
     * Scope for filtering by log name.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeInLog(Builder $query, string ...$logNames): Builder
    {
        return $query->whereIn('log_name', $logNames);
    }

    /**
     * Scope for filtering by causer.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    /**
     * Scope for filtering by subject.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }

    /**
     * Scope for filtering by event type.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for filtering by date range.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the causer name attribute.
     */
    public function getCauserNameAttribute(): string
    {
        if ($this->causer instanceof User) {
            return $this->causer->name;
        }

        return 'System';
    }

    /**
     * Get the subject name attribute.
     */
    public function getSubjectNameAttribute(): string
    {
        if ($this->subject) {
            return class_basename($this->subject_type).' #'.$this->subject_id;
        }

        return 'N/A';
    }

    /**
     * Log an activity.
     *
     * @param  array<string, mixed>  $properties
     */
    

/**
 * @param array<string, mixed> $properties
 */
public static function log(
        string $description,
        ?string $logName = 'default',
        ?string $event = null,
        ?Model $subject = null,
        ?Model $causer = null,
        array $properties = []
    ): self {
        $activity = new self;
        $activity->log_name = $logName;
        $activity->description = $description;
        $activity->event = $event;
        $activity->properties = $properties;

        if ($subject) {
            $activity->subject_type = $subject->getMorphClass();
            $activity->subject_id = $subject->getKey();
        }

        if ($causer) {
            $activity->causer_type = $causer->getMorphClass();
            $activity->causer_id = $causer->getKey();
        } else {
            $authUser = Auth::user();
            if ($authUser instanceof User) {
                $activity->causer_type = User::class;
                $activity->causer_id = $authUser->id;
            }
        }

        $activity->save();

        return $activity;
    }

    /**
     * Get activity statistics.
     *
     * @return array<string, mixed>
     */
    public static function getStatistics(): array
    {
        return [
            'total_records' => static::count(),
            'records_last_30_days' => static::where('created_at', '>=', now()->subDays(30))->count(),
            'by_log_name' => static::selectRaw('log_name, COUNT(*) as count')
                ->groupBy('log_name')
                ->pluck('count', 'log_name')
                ->toArray(),
            'oldest_record' => static::oldest('created_at')->first()?->created_at,
            'newest_record' => static::latest('created_at')->first()?->created_at,
        ];
    }
}
