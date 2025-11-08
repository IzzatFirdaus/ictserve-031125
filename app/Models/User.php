<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
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
            'password' => 'hashed',
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

    // Relationships
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function setGradeAttribute(null|int|string $value): void
    {
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
                'can_approve_loans' => $level >= 41,
            ],
        );

        $this->attributes['grade_id'] = $grade->id;
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class);
    }

    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function approvedLoanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'approver_id');
    }

    // Enhanced Helpdesk Relationships

    /**
     * Helpdesk comments created by this user
     */
    public function helpdeskComments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class, 'user_id');
    }

    /**
     * Helpdesk tickets assigned to this user
     */
    public function assignedHelpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'assigned_to_user');
    }

    // Notification Preference Methods

    /**
     * Check if user wants email notifications for a specific type
     */
    public function wantsEmailNotifications(string $type): bool
    {
        $preferences = $this->notification_preferences ?? [];

        return $preferences[$type] ?? true; // Default to true if not set
    }

    /**
     * Update notification preference for a specific type
     */
    public function updateNotificationPreference(string $type, bool $enabled): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$type] = $enabled;
        $this->update(['notification_preferences' => $preferences]);
    }

    /**
     * Get all notification preferences
     */
    public function getNotificationPreferences(): array
    {
        return $this->notification_preferences ?? [
            'ticket_updates' => true,
            'ticket_assignments' => true,
            'ticket_comments' => true,
            'sla_alerts' => true,
            'system_announcements' => true,
            'loan_updates' => true,
            'loan_approvals' => true,
            'loan_reminders' => true,
        ];
    }

    /**
     * Set all notification preferences
     */
    public function setNotificationPreferences(array $preferences): void
    {
        $this->update(['notification_preferences' => $preferences]);
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
    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(UserNotificationPreference::class);
    }

    /**
     * User's saved searches
     */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * User's portal activities
     */
    public function portalActivities(): HasMany
    {
        return $this->hasMany(PortalActivity::class);
    }

    /**
     * User's internal comments
     */
    public function internalComments(): HasMany
    {
        return $this->hasMany(InternalComment::class);
    }

    /**
     * User's consent records for PDPA compliance
     */
    public function consents(): HasMany
    {
        return $this->hasMany(UserConsent::class);
    }

    // Portal helper methods

    /**
     * Check if user meets grade requirement for approver role
     */
    public function meetsApproverGradeRequirement(): bool
    {
        $gradeLevel = null;

        if ($this->relationLoaded('grade')) {
            $gradeLevel = $this->getRelation('grade')?->level;
        } elseif ($this->grade_id !== null) {
            $gradeLevel = $this->grade()->value('level');
        }

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
}
