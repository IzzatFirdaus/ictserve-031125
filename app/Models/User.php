<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed_at',
        'require_password_change',
        'role',
        'staff_id',
        'division_id',
        'grade',
        'grade_id',
        'position_id',
        'phone',
        'mobile',
        'bio',
        'avatar',
        'is_active',
        'last_login_at',
        'notification_preferences', // Enhanced for hybrid architecture
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<int, string> */
    protected $auditInclude = [
        'role',
        'name',
        'email',
        'staff_id',
        'division_id',
        'grade_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel cast type (not credentials - field name and cast definition)
            'password_changed_at' => 'datetime',
            'require_password_change' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'notification_preferences' => 'array', // Enhanced for hybrid architecture
        ];
    }

    // Four-role RBAC methods
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperuser(): bool
    {
        return $this->role === 'superuser';
    }

    public function canApprove(): bool
    {
        return $this->isApprover() || $this->isAdmin() || $this->isSuperuser();
    }

    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->isSuperuser();
    }

    /**
     * Determine if user can access Filament admin panel
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasAdminAccess();
    }

    // Relationships
    /** @return BelongsTo<Division, self> */
    public function division(): BelongsTo
    {
        /** @var BelongsTo<Division, self> $relation */
        $relation = $this->belongsTo(Division::class);

        return $relation;
    }

    /** @return BelongsTo<Grade, self> */
    public function grade(): BelongsTo
    {
        /** @var BelongsTo<Grade, self> $relation */
        $relation = $this->belongsTo(Grade::class);

        return $relation;
    }

    public function setGradeAttribute(null|int|string $value): void
    {
        // Don't override grade_id if it's already set and grade is null
        if (($value === null || $value === '') && isset($this->attributes['grade_id'])) {
            return;
        }

        if ($value === null || $value === '') {
            $this->attributes['grade_id'] = null;

            return;
        }

        $level = (int) $value;

        $grade = Grade::firstOrCreate(
            ['level' => $level],
            [
                'code' => "GRADE-{$level}",
                'name_ms' => "Gred {$level}",
                'name_en' => "Grade {$level}",
                'can_approve_loans' => $level >= config('app.min_approver_grade_level', 41),
            ],
        );

        $this->attributes['grade_id'] = $grade->id;
    }

    /** @return BelongsTo<Position, self> */
    public function position(): BelongsTo
    {
        /** @var BelongsTo<Position, self> $relation */
        $relation = $this->belongsTo(Position::class);

        return $relation;
    }

    /** @return HasMany<HelpdeskTicket, self> */
    public function helpdeskTickets(): HasMany
    {
        /** @var HasMany<HelpdeskTicket, self> $relation */
        $relation = $this->hasMany(HelpdeskTicket::class);

        return $relation;
    }

    /** @return HasMany<LoanApplication, self> */
    public function loanApplications(): HasMany
    {
        /** @var HasMany<LoanApplication, self> $relation */
        $relation = $this->hasMany(LoanApplication::class);

        return $relation;
    }

    /** @return HasMany<LoanApplication, self> */
    public function approvedLoanApplications(): HasMany
    {
        /** @var HasMany<LoanApplication, self> $relation */
        $relation = $this->hasMany(LoanApplication::class, 'approver_id');

        return $relation;
    }

    // Enhanced Helpdesk Relationships

    /**
     * Helpdesk comments created by this user
     */
    /** @return HasMany<HelpdeskComment, self> */
    public function helpdeskComments(): HasMany
    {
        /** @var HasMany<HelpdeskComment, self> $relation */
        $relation = $this->hasMany(HelpdeskComment::class, 'user_id');

        return $relation;
    }

    /**
     * Helpdesk tickets assigned to this user
     */
    /** @return HasMany<HelpdeskTicket, self> */
    public function assignedHelpdeskTickets(): HasMany
    {
        /** @var HasMany<HelpdeskTicket, self> $relation */
        $relation = $this->hasMany(HelpdeskTicket::class, 'assigned_to_user');

        return $relation;
    }

    // Notification Preference Methods

    /**
     * Check if user wants email notifications for a specific type
     */
    public function wantsEmailNotifications(string $type): bool
    {
        $preferences = $this->getNotificationPreferences();

        return $preferences[$type] ?? true; // Default to true if not set
    }

    // Query Scopes

    /**
     * Scope for users with Grade 41 and above (eligible approvers)
     */
    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeGrade41AndAbove(Builder $query): Builder
    {
        return $query->whereHas('grade', function (Builder $q): void {
            $q->where('level', '>=', 41);
        })->where('is_active', true);
    }

    /**
     * Scope for active users
     */
    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all notification preferences
     */
    /** @return array<string, bool> */
    public function getNotificationPreferences(): array
    {
        $preferences = $this->notification_preferences;

        if (! is_array($preferences)) {
            $preferences = [
                'ticket_updates' => true,
                'ticket_assignments' => true,
                'ticket_comments' => true,
                'sla_alerts' => true,
                'system_announcements' => true,
                'loan_updates' => true,
                'loan_approvals' => true,
                'loan_reminders' => true,
                'realtime_notifications' => true, // WebSocket/broadcast notifications
            ];
        }

        return array_map(
            static fn (bool|string|int $value): bool => (bool) $value,
            $preferences,
        );
    }

    /**
     * @param  array<string, bool>  $preferences
     */
    public function setNotificationPreferences(array $preferences): void
    {
        $normalized = array_map(
            static fn (bool|string|int $value): bool => (bool) $value,
            $preferences,
        );

        $this->update(['notification_preferences' => $normalized]);
    }

    /**
     * Update a specific notification preference
     */
    public function updateNotificationPreference(string $type, bool $enabled): void
    {
        $preferences = $this->getNotificationPreferences();
        $preferences[$type] = $enabled;
        $this->setNotificationPreferences($preferences);
    }

    /**
     * Enable all notifications
     */
    public function enableAllNotifications(): void
    {
        $preferences = $this->getNotificationPreferences();
        foreach ($preferences as $key => $value) {
            $preferences[$key] = true;
        }
        $this->setNotificationPreferences($preferences);
    }

    /**
     * Disable all notifications
     */
    public function disableAllNotifications(): void
    {
        $preferences = $this->getNotificationPreferences();
        foreach ($preferences as $key => $value) {
            $preferences[$key] = false;
        }
        $this->setNotificationPreferences($preferences);
    }

    // Portal-specific relationships

    /**
     * User's notification preference records
     */
    /** @return HasMany<UserNotificationPreference, self> */
    public function notificationPreferences(): HasMany
    {
        /** @var HasMany<UserNotificationPreference, self> $relation */
        $relation = $this->hasMany(UserNotificationPreference::class);

        return $relation;
    }

    /**
     * User's saved searches
     */
    /** @return HasMany<SavedSearch, self> */
    public function savedSearches(): HasMany
    {
        /** @var HasMany<SavedSearch, self> $relation */
        $relation = $this->hasMany(SavedSearch::class);

        return $relation;
    }

    /**
     * User's portal activities
     */
    /** @return HasMany<PortalActivity, self> */
    public function portalActivities(): HasMany
    {
        /** @var HasMany<PortalActivity, self> $relation */
        $relation = $this->hasMany(PortalActivity::class);

        return $relation;
    }

    /**
     * User's internal comments
     */
    /** @return HasMany<InternalComment, self> */
    public function internalComments(): HasMany
    {
        /** @var HasMany<InternalComment, self> $relation */
        $relation = $this->hasMany(InternalComment::class);

        return $relation;
    }

    /**
     * User's consent records for PDPA compliance
     */
    /** @return HasMany<UserConsent, self> */
    public function consents(): HasMany
    {
        /** @var HasMany<UserConsent, self> $relation */
        $relation = $this->hasMany(UserConsent::class);

        return $relation;
    }

    // Portal helper methods

    /**
     * Check if user meets grade requirement for approver role
     */
    public function meetsApproverGradeRequirement(): bool
    {
        $gradeLevel = null;

        // Try to get from loaded relationship first
        if ($this->relationLoaded('grade') && $this->grade !== null) {
            $gradeLevel = $this->grade->level;
        }
        // Query the relationship if grade_id exists but not loaded
        elseif ($this->grade_id !== null) {
            // Use the relationship query method instead of direct Grade query
            $gradeLevel = $this->grade()->value('level');
        }

        // Fallback to grade attribute if no relationship
        if ($gradeLevel === null) {
            $attributeGrade = $this->getAttribute('grade');

            if (is_numeric($attributeGrade)) {
                $gradeLevel = (int) $attributeGrade;
            }
        }

        return ($gradeLevel ?? 0) >= 41;
    }

    /**
     * Calculate profile completeness percentage
     */
    public function getProfileCompletenessAttribute(): int
    {
        $fields = [
            'name' => ! empty($this->name),
            'email' => ! empty($this->email),
            'phone' => ! empty($this->phone),
            'division_id' => ! empty($this->division_id),
            'grade_id' => ! empty($this->grade_id),
            'position_id' => ! empty($this->position_id),
            'notification_preferences' => ! empty($this->notification_preferences),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);

        return (int) (($completed / $total) * 100);
    }

    /**
     * Ensure name attribute returns a string even if an array is mistakenly assigned (e.g. localized data)
     */
    public function getNameAttribute(mixed $value): string
    {
        if (is_array($value)) {
            // Prefer English locale when present, otherwise use the first available value
            return $value['en'] ?? (string) (array_values($value)[0] ?? '');
        }

        return (string) ($value ?? '');
    }
}
