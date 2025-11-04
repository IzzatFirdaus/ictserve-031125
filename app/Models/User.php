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
        'role',
        'staff_id',
        'division_id',
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
}
