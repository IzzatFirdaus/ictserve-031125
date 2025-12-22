<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $profile_picture
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $two_factor_secret
 * @property array<array-key, mixed>|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $two_factor_backup_codes
 * @property int $two_factor_enabled
 * @property string|null $two_factor_enabled_at
 * @property string $role
 * @property string|null $google_id Google OAuth user ID for SSO
 * @property string|null $google_token Encrypted Google OAuth access token
 * @property string|null $google_refresh_token Encrypted Google OAuth refresh token
 * @property string $locale DEPRECATED v3.6.0: Always ms. Retained for potential future use.
 * @property string|null $staff_id
 * @property string|null $staff_number MOTAC staff number for identification
 * @property string|null $division_code Division code for organizational structure
 * @property int|null $division_id
 * @property int|null $grade_id
 * @property int|null $position_id
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $bio
 * @property string|null $avatar
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip IP address of last login for audit
 * @property int $guest_submissions_linked Count of guest submissions linked to this account
 * @property \Illuminate\Support\Carbon|null $password_changed_at
 * @property bool $require_password_change
 * @property int $has_completed_tour
 * @property array<array-key, mixed>|null $notification_preferences User notification preferences for email alerts
 * @property string $theme_preference User theme preference: light|dark|system
 * @property array<array-key, mixed>|null $saved_filters Saved filter combinations for tables
 * @property array<array-key, mixed>|null $dashboard_layout Dashboard widget arrangement preferences
 * @property bool $onboarding_completed Whether user has completed onboarding tour
 * @property string|null $anonymized_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $approvedLoanApplications
 * @property-read int|null $approved_loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $assignedHelpdeskTickets
 * @property-read int|null $assigned_helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $assignedTickets
 * @property-read int|null $assigned_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserConsent> $consents
 * @property-read int|null $consents_count
 * @property-read \App\Models\Division|null $division
 * @property-read int $profile_completeness
 * @property \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $helpdeskComments
 * @property-read int|null $helpdesk_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNotificationPreference> $notificationPreferences
 * @property-read int|null $notification_preferences_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $portalActivities
 * @property-read int|null $portal_activities_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SavedSearch> $savedSearches
 * @property-read int|null $saved_searches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SsoAuditLog> $ssoAuditLogs
 * @property-read int|null $sso_audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User grade41AndAbove()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User whereAnonymizedAt($value)
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereBio($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDashboardLayout($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereDivisionCode($value)
 * @method static Builder<static>|User whereDivisionId($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereGoogleId($value)
 * @method static Builder<static>|User whereGoogleRefreshToken($value)
 * @method static Builder<static>|User whereGoogleToken($value)
 * @method static Builder<static>|User whereGradeId($value)
 * @method static Builder<static>|User whereGuestSubmissionsLinked($value)
 * @method static Builder<static>|User whereHasCompletedTour($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsActive($value)
 * @method static Builder<static>|User whereLastLoginAt($value)
 * @method static Builder<static>|User whereLastLoginIp($value)
 * @method static Builder<static>|User whereLocale($value)
 * @method static Builder<static>|User whereMobile($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User whereNotificationPreferences($value)
 * @method static Builder<static>|User whereOnboardingCompleted($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePasswordChangedAt($value)
 * @method static Builder<static>|User wherePhone($value)
 * @method static Builder<static>|User wherePositionId($value)
 * @method static Builder<static>|User whereProfilePicture($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRequirePasswordChange($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereSavedFilters($value)
 * @method static Builder<static>|User whereStaffId($value)
 * @method static Builder<static>|User whereStaffNumber($value)
 * @method static Builder<static>|User whereThemePreference($value)
 * @method static Builder<static>|User whereTwoFactorBackupCodes($value)
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static Builder<static>|User whereTwoFactorEnabled($value)
 * @method static Builder<static>|User whereTwoFactorEnabledAt($value)
 * @method static Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 * @method static Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements Auditable, FilamentUser, MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'password_changed_at',
        'require_password_change',
        'role',
        'locale', // v3.5.0 True Hybrid - language preference
        'staff_id',
        'staff_number', // v3.5.0 True Hybrid - MOTAC staff number
        'division_id',
        'division_code', // v3.5.0 True Hybrid - division code
        'grade',
        'grade_id',
        'position_id',
        'phone',
        'mobile',
        'bio',
        'avatar',
        'profile_picture',
        'is_active',
        'last_login_at',
        'last_login_ip', // v3.5.0 True Hybrid - audit trail
        'guest_submissions_linked', // v3.5.0 True Hybrid - account linking counter
        'notification_preferences', // Enhanced for hybrid architecture
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        // Google OAuth SSO fields (v3.5.0)
        'google_id',
        'google_token',
        'google_refresh_token',
        // UI Preferences (v3.5.0 Phase 9)
        'theme_preference',
        'saved_filters',
        'dashboard_layout',
        'onboarding_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'role',
                'name',
                'email',
                'staff_number',
                'division_code',
                'grade',
                'is_active',
                'last_login_at',
            ])
            ->logOnlyDirty()
            ->useLogName('auth')
            ->setDescriptionForEvent(fn (string $eventName) => "User {$eventName}");
    }

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
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_secret' => 'encrypted',
            // v3.5.0 True Hybrid fields
            'guest_submissions_linked' => 'integer',
            'google_token' => 'encrypted',
            'google_refresh_token' => 'encrypted',
            // UI Preferences (v3.5.0 Phase 9)
            'theme_preference' => 'string',
            'saved_filters' => 'array',
            'dashboard_layout' => 'array',
            'onboarding_completed' => 'boolean',
        ];
    }

    // Four-role RBAC methods
    public function isStaff(): bool
    {
        return $this->role === 'staff' || $this->hasRole('staff');
    }

    public function isApprover(): bool
    {
        return $this->role === 'approver' || $this->hasRole('approver');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isSuperuser(): bool
    {
        return $this->role === 'superuser' || $this->hasRole('superuser');
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

    /**
     * Alias for assignedHelpdeskTickets() for consistency with v3.5.0 spec
     */
    /** @return HasMany<HelpdeskTicket, self> */
    public function assignedTickets(): HasMany
    {
        return $this->assignedHelpdeskTickets();
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
    

/**
 * @param array<string, mixed> $preferences
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

    // v3.5.0 True Hybrid Architecture Methods

    /**
     * Get SSO audit logs for this user.
     *
     * @return HasMany<SsoAuditLog, self>
     */
    public function ssoAuditLogs(): HasMany
    {
        /** @var HasMany<SsoAuditLog, self> $relation */
        $relation = $this->hasMany(SsoAuditLog::class);

        return $relation;
    }

    /**
     * Check if user has linked their Google account
     */
    public function isGoogleLinked(): bool
    {
        return ! empty($this->google_id);
    }

    /**
     * Extract username from email (user@motac.gov.my → user)
     */
    public static function extractUsernameFromEmail(string $email): string
    {
        $parts = explode('@', $email);

        return $parts[0] ?? '';
    }

    /**
     * Get user's preferred locale
     *
     * @deprecated v3.6.0 Always returns 'ms' - Bahasa Melayu only interface
     */
    public function getPreferredLocale(): string
    {
        return 'ms';
    }

    /**
     * Get locale attribute
     *
     * @deprecated v3.6.0 Always returns 'ms' regardless of database value
     */
    public function getLocaleAttribute(): string
    {
        return 'ms';
    }

    /**
     * Set locale attribute
     *
     * @deprecated v3.6.0 No-op - locale is always 'ms'
     */
    public function setLocaleAttribute(mixed $value): void
    {
        // v3.6.0: No-op - locale is always 'ms'
        // Database value is not updated to preserve historical data
    }

    /**
     * Increment guest submissions linked counter
     */
    public function incrementGuestSubmissionsLinked(int $count = 1): void
    {
        $this->increment('guest_submissions_linked', $count);
    }
}
