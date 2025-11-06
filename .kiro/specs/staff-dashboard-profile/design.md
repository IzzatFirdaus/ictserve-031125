# Authenticated Staff Dashboard and Profile - Design Document

## Document Metadata

**Version**: 1.0.0 (SemVer)  
**Last Updated**: 6 November 2025  
**Status**: Active - Design Phase  
**Classification**: Restricted - Internal MOTAC Staff Only  
**Traceability**: D03 (Software Requirements), D04 (Software Design), D11 (Technical Design)  
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, PDPA 2010

## Overview

The Authenticated Staff Dashboard and Profile system provides a comprehensive internal portal for MOTAC staff members to access personalized dashboards, manage submissions, update profiles, and perform role-based operations. This design document outlines the technical architecture, components, data models, and implementation strategies for the authenticated portal layer of the ICTServe hybrid architecture.

### Design Principles

1. **User-Centric Design**: Prioritize staff convenience with intuitive interfaces and self-service capabilities
2. **Role-Based Access**: Implement four-tier RBAC (Staff, Approver, Admin, Superuser) with progressive feature access
3. **Performance First**: Achieve Core Web Vitals targets through caching, lazy loading, and optimized queries
4. **Accessibility Compliance**: Maintain WCAG 2.2 Level AA standards across all portal interfaces
5. **Security by Design**: Implement comprehensive authentication, authorization, and audit logging
6. **Seamless Integration**: Ensure smooth transitions between guest forms, authenticated portal, and admin panel

### Architectural Context

The authenticated portal operates as the **middle layer** in ICTServe's three-tier architecture:

- **Guest Layer**: Public forms without authentication (existing)
- **Authenticated Portal Layer**: Staff dashboard and profile management (this design)
- **Admin Panel Layer**: Filament-based backend management (existing)

## Architecture

### System Architecture

```text
┌─────────────────────────────────────────────────────────────────┐
│                    ICTServe Hybrid Architecture                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐    ┌──────────────────┐    ┌──────────────┐  │
│  │ Guest Forms  │───▶│ Authenticated    │───▶│   Filament   │  │
│  │ (Public)     │    │ Portal (Staff)   │    │ Admin Panel  │  │
│  └──────────────┘    └──────────────────┘    └──────────────┘  │
│        │                      │                       │          │
│        │                      │                       │          │
│        └──────────────────────┴───────────────────────┘          │
│                              │                                   │
│                    ┌─────────▼─────────┐                        │
│                    │  Shared Services   │                        │
│                    │  - Authentication  │                        │
│                    │  - Authorization   │                        │
│                    │  - Notifications   │                        │
│                    │  - Audit Logging   │                        │
│                    └────────────────────┘                        │
└─────────────────────────────────────────────────────────────────┘
```

### Technology Stack

**Backend Framework**:

- Laravel 12.x (PHP 8.2+)
- Livewire 3.6+ for reactive components
- Laravel Breeze for authentication
- Spatie Laravel Permission for RBAC

**Frontend Stack**:

- Tailwind CSS 3.x for styling
- Alpine.js 3.x (bundled with Livewire)
- Vite 6.x for asset bundling

**Real-Time Features**:

- Laravel Echo 2.x for WebSocket client
- Laravel Reverb 1.6+ for WebSocket server
- Redis for caching and broadcasting

**Database**:

- MySQL 8.x (production)
- SQLite (development/testing)

### Application Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Portal/
│   │       ├── DashboardController.php
│   │       ├── SubmissionController.php
│   │       ├── ProfileController.php
│   │       ├── ApprovalController.php
│   │       └── NotificationController.php
│   ├── Middleware/
│   │   ├── EnsureStaffRole.php
│   │   ├── EnsureApproverRole.php
│   │   └── TrackPortalActivity.php
│   └── Requests/
│       └── Portal/
│           ├── UpdateProfileRequest.php
│           ├── ProcessApprovalRequest.php
│           └── ExportSubmissionsRequest.php
├── Livewire/
│   └── Portal/
│       ├── Dashboard/
│       │   ├── StatisticsCards.php
│       │   ├── RecentActivity.php
│       │   └── QuickActions.php
│       ├── Submissions/
│       │   ├── SubmissionList.php
│       │   ├── SubmissionDetail.php
│       │   └── SubmissionFilters.php
│       ├── Profile/
│       │   ├── ProfileForm.php
│       │   ├── NotificationPreferences.php
│       │   └── SecuritySettings.php
│       ├── Approvals/
│       │   ├── ApprovalQueue.php
│       │   └── ApprovalModal.php
│       └── Notifications/
│           ├── NotificationCenter.php
│           └── NotificationBell.php
├── Models/
│   ├── User.php (extended)
│   ├── UserNotificationPreference.php
│   ├── SavedSearch.php
│   ├── PortalActivity.php
│   └── InternalComment.php
├── Services/
│   ├── DashboardService.php
│   ├── SubmissionService.php
│   ├── NotificationService.php
│   ├── ExportService.php
│   └── GuestSubmissionClaimService.php
└── Policies/
    ├── SubmissionPolicy.php
    ├── ApprovalPolicy.php
    └── ProfilePolicy.php

resources/
└── views/
    └── portal/
        ├── layouts/
        │   ├── app.blade.php
        │   └── navigation.blade.php
        ├── dashboard/
        │   └── index.blade.php
        ├── submissions/
        │   ├── index.blade.php
        │   └── show.blade.php
        ├── profile/
        │   └── edit.blade.php
        ├── approvals/
        │   └── index.blade.php
        └── components/
            ├── statistics-card.blade.php
            ├── activity-item.blade.php
            ├── submission-table.blade.php
            └── notification-item.blade.php
```

## Components and Interfaces

### 1. Dashboard Components

#### 1.1 Statistics Cards Component
**Purpose**: Display real-time statistics for user's submissions and activities

**Livewire Component**: `App\Livewire\Portal\Dashboard\StatisticsCards`

**Properties**:

- `$openTicketsCount` - Count of open helpdesk tickets
- `$pendingLoansCount` - Count of pending loan applications
- `$overdueItemsCount` - Count of overdue asset returns
- `$availableAssetsCount` - Count of available assets

**Methods**:

- `mount()` - Initialize statistics with cached data
- `refreshStatistics()` - Fetch fresh statistics (called every 300 seconds)
- `getStatistics()` - Query database with optimized queries

**Caching Strategy**:

- Cache key: `portal.statistics.{user_id}`
- TTL: 5 minutes
- Invalidation: On submission status change, loan approval, asset return

**Design Rationale**: Real-time updates every 5 minutes balance freshness with performance. Caching prevents excessive database queries while maintaining acceptable data currency.

#### 1.2 Recent Activity Feed Component
**Purpose**: Display chronological list of user's recent actions

**Livewire Component**: `App\Livewire\Portal\Dashboard\RecentActivity`

**Properties**:

- `$activities` - Collection of recent 10 activities
- `$activityTypes` - Array of activity type filters

**Methods**:

- `mount()` - Load recent activities
- `loadMore()` - Fetch additional activities (pagination)
- `filterByType(string $type)` - Filter activities by type

**Activity Types**:

- Ticket submission
- Ticket status change
- Loan application
- Loan approval/rejection
- Asset return
- Comment added

**Design Rationale**: Limiting to 10 activities reduces initial load time. Lazy loading additional activities improves perceived performance.

#### 1.3 Quick Actions Component
**Purpose**: Provide one-click access to common tasks

**Livewire Component**: `App\Livewire\Portal\Dashboard\QuickActions`

**Actions**:

- Submit Helpdesk Ticket → `/portal/tickets/create`
- Request Asset Loan → `/portal/loans/create`
- View My Submissions → `/portal/submissions`
- Manage Profile → `/portal/profile`

**Role-Based Actions**:

- **Approver**: View Pending Approvals → `/portal/approvals`
- **Admin**: Access Admin Panel → `/admin`
- **Superuser**: System Configuration → `/admin/settings`

**Design Rationale**: Quick actions reduce navigation friction. Role-based visibility ensures users only see relevant actions.

### 2. Submission Management Components

#### 2.1 Submission List Component
**Purpose**: Display tabbed interface for helpdesk tickets and asset loans

**Livewire Component**: `App\Livewire\Portal\Submissions\SubmissionList`

**Properties**:

- `$activeTab` - Current tab ('tickets' or 'loans')
- `$submissions` - Paginated collection of submissions
- `$filters` - Array of active filters
- `$sortBy` - Current sort column
- `$sortDirection` - Sort direction ('asc' or 'desc')
- `$searchTerm` - Search query string

**Methods**:

- `mount()` - Initialize with default filters
- `switchTab(string $tab)` - Change active tab
- `applyFilters(array $filters)` - Apply filter criteria
- `sortBy(string $column)` - Toggle sort direction
- `search(string $term)` - Perform search with debouncing

**Filtering Capabilities**:

- Status (multi-select)
- Date range (from/to)
- Category (for tickets)
- Asset type (for loans)
- Priority

**Design Rationale**: Tabbed interface separates concerns while maintaining unified filtering logic. Debounced search (300ms) reduces server load.

#### 2.2 Submission Detail Component
**Purpose**: Display comprehensive submission information with timeline and comments

**Livewire Component**: `App\Livewire\Portal\Submissions\SubmissionDetail`

**Properties**:

- `$submission` - Submission model (HelpdeskTicket or LoanApplication)
- `$timeline` - Collection of activity events
- `$comments` - Collection of internal comments
- `$newComment` - New comment text

**Methods**:

- `mount($submissionId, $type)` - Load submission with relationships
- `addComment()` - Post new internal comment
- `replyToComment($commentId)` - Reply to existing comment
- `loadMoreTimeline()` - Lazy load additional timeline events
- `claimSubmission()` - Claim guest submission (if applicable)

**Timeline Events**:

- Submission created
- Status changed
- Assigned to division
- Comment added
- Approval granted/rejected
- Asset returned

**Design Rationale**: Eager loading relationships prevents N+1 queries. Lazy loading timeline events improves initial page load performance.

#### 2.3 Guest Submission Claiming
**Purpose**: Allow authenticated users to claim previous guest submissions

**Service**: `App\Services\GuestSubmissionClaimService`

**Methods**:

- `findClaimableSubmissions(User $user)` - Find submissions matching user email
- `claimSubmission(User $user, $submission)` - Link submission to user account
- `verifyOwnership(User $user, $submission)` - Verify email match

**Claiming Process**:

1. Query submissions where `email = user->email` AND `user_id IS NULL`
2. Display "Claim This Submission" button on matching records
3. On claim action, update `user_id` and log activity
4. Send confirmation email to user

**Design Rationale**: Email-based matching provides secure ownership verification without requiring additional authentication steps.

### 3. Profile Management Components

#### 3.1 Profile Form Component
**Purpose**: Allow users to update personal information and preferences

**Livewire Component**: `App\Livewire\Portal\Profile\ProfileForm`

**Properties**:

- `$name` - User's full name (editable)
- `$phone` - Contact phone number (editable)
- `$email` - Email address (read-only)
- `$staffId` - Staff ID (read-only)
- `$grade` - Staff grade (read-only)
- `$division` - Division name (read-only)
- `$profileCompleteness` - Percentage (0-100)

**Methods**:

- `mount()` - Load user profile
- `updateProfile()` - Save profile changes with validation
- `calculateCompleteness()` - Calculate profile completion percentage

**Validation Rules**:

- Name: required, string, max:255
- Phone: nullable, regex:/^[0-9]{10,15}$/

**Design Rationale**: Real-time validation with 300ms debouncing provides immediate feedback without excessive server requests. Read-only fields prevent unauthorized data modification.

#### 3.2 Notification Preferences Component
**Purpose**: Manage email notification settings

**Livewire Component**: `App\Livewire\Portal\Profile\NotificationPreferences`

**Properties**:

- `$ticketStatusUpdates` - Boolean toggle
- `$loanApprovalNotifications` - Boolean toggle
- `$overdueReminders` - Boolean toggle
- `$systemAnnouncements` - Boolean toggle

**Methods**:

- `mount()` - Load current preferences
- `updatePreference(string $key, bool $value)` - Save individual preference
- `updateAll(array $preferences)` - Bulk update preferences

**Storage**:

- Table: `user_notification_preferences`
- Columns: `user_id`, `preference_key`, `preference_value`, `updated_at`

**Design Rationale**: Granular control allows users to customize notification frequency. Immediate save provides instant feedback without requiring form submission.

#### 3.3 Security Settings Component
**Purpose**: Manage password and security preferences

**Livewire Component**: `App\Livewire\Portal\Profile\SecuritySettings`

**Properties**:

- `$currentPassword` - Current password input
- `$newPassword` - New password input
- `$newPasswordConfirmation` - Password confirmation
- `$passwordStrength` - Calculated strength (0-100)

**Methods**:

- `changePassword()` - Update user password
- `calculatePasswordStrength(string $password)` - Calculate strength score
- `validateCurrentPassword()` - Verify current password

**Password Requirements**:

- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character

**Design Rationale**: Real-time password strength indicator guides users toward secure passwords. Current password verification prevents unauthorized changes.

### 4. Approval Interface Components

#### 4.1 Approval Queue Component
**Purpose**: Display pending loan applications for Grade 41+ approvers

**Livewire Component**: `App\Livewire\Portal\Approvals\ApprovalQueue`

**Properties**:

- `$pendingApplications` - Paginated collection of pending loans
- `$selectedApplications` - Array of selected application IDs
- `$filters` - Array of active filters
- `$sortBy` - Current sort column

**Methods**:

- `mount()` - Load pending applications
- `selectApplication($id)` - Toggle application selection
- `selectAll()` - Select all visible applications
- `bulkApprove()` - Approve selected applications
- `bulkReject()` - Reject selected applications

**Authorization**:

- Middleware: `EnsureApproverRole`
- Policy: `ApprovalPolicy@viewAny`
- Grade check: `user->grade >= 41`

**Design Rationale**: Bulk operations improve efficiency for approvers handling multiple applications. Grade-based authorization ensures only qualified staff can approve loans.

#### 4.2 Approval Modal Component
**Purpose**: Display application details and approval actions

**Livewire Component**: `App\Livewire\Portal\Approvals\ApprovalModal`

**Properties**:

- `$application` - LoanApplication model
- `$action` - 'approve' or 'reject'
- `$remarks` - Optional approval/rejection remarks (max 500 chars)
- `$showConfirmation` - Boolean for confirmation modal

**Methods**:

- `mount($applicationId)` - Load application with relationships
- `approve()` - Process approval with remarks
- `reject()` - Process rejection with remarks
- `confirmAction()` - Show confirmation modal
- `processApproval()` - Execute approval workflow

**Approval Workflow**:

1. Validate approver authorization (grade >= 41)
2. Update application status
3. Record approval details (method: 'portal', remarks, timestamp)
4. Send email notification to applicant (within 60 seconds)
5. Log action in audit trail
6. Display success message

**Design Rationale**: Confirmation modal prevents accidental approvals/rejections. Remarks field allows approvers to provide context for decisions.

### 5. Notification Components

#### 5.1 Notification Bell Component
**Purpose**: Display unread notification count and provide access to notification center

**Livewire Component**: `App\Livewire\Portal\Notifications\NotificationBell`

**Properties**:

- `$unreadCount` - Count of unread notifications
- `$showDropdown` - Boolean for dropdown visibility

**Methods**:

- `mount()` - Load unread count
- `toggleDropdown()` - Show/hide notification dropdown
- `markAllAsRead()` - Mark all notifications as read

**Real-Time Updates**:

- Listen to: `NotificationCreated` event
- Channel: `private-user.{user_id}`
- Update: Increment `$unreadCount` and refresh dropdown

**Design Rationale**: Real-time updates via Laravel Echo ensure users receive immediate notification of important events without page refresh.

#### 5.2 Notification Center Component
**Purpose**: Display full notification history with filtering and actions

**Livewire Component**: `App\Livewire\Portal\Notifications\NotificationCenter`

**Properties**:

- `$notifications` - Paginated collection of notifications
- `$filter` - 'all', 'unread', or 'read'
- `$notificationTypes` - Array of notification type filters

**Methods**:

- `mount()` - Load notifications with default filter
- `filterBy(string $filter)` - Apply filter
- `markAsRead($notificationId)` - Mark single notification as read
- `deleteNotification($notificationId)` - Delete notification

**Notification Types**:

- `ticket_assigned` - Ticket assigned to user
- `ticket_resolved` - Ticket marked as resolved
- `loan_approved` - Loan application approved
- `loan_rejected` - Loan application rejected
- `asset_overdue` - Asset return overdue
- `sla_breach` - SLA breach alert

**Design Rationale**: Filtering capabilities help users focus on relevant notifications. Pagination prevents performance issues with large notification histories.

## Data Models

### 1. User Model Extensions

**Model**: `App\Models\User`

**New Relationships**:

```php
public function notificationPreferences(): HasMany
{
    return $this->hasMany(UserNotificationPreference::class);
}

public function savedSearches(): HasMany
{
    return $this->hasMany(SavedSearch::class);
}

public function portalActivities(): HasMany
{
    return $this->hasMany(PortalActivity::class);
}

public function internalComments(): HasMany
{
    return $this->hasMany(InternalComment::class);
}

public function helpdeskTickets(): HasMany
{
    return $this->hasMany(HelpdeskTicket::class);
}

public function loanApplications(): HasMany
{
    return $this->hasMany(LoanApplication::class);
}
```

**New Methods**:

```php
public function isApprover(): bool
{
    return $this->grade >= 41;
}

public function canApprove(LoanApplication $application): bool
{
    return $this->isApprover() && $application->status === 'pending';
}

public function getProfileCompletenessAttribute(): int
{
    // Calculate profile completion percentage
}
```

### 2. UserNotificationPreference Model

**Model**: `App\Models\UserNotificationPreference`

**Table**: `user_notification_preferences`

**Schema**:

```php
Schema::create('user_notification_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('preference_key'); // e.g., 'ticket_status_updates'
    $table->boolean('preference_value')->default(true);
    $table->timestamps();
    
    $table->unique(['user_id', 'preference_key']);
    $table->index('user_id');
});
```

**Preference Keys**:

- `ticket_status_updates`
- `loan_approval_notifications`
- `overdue_reminders`
- `system_announcements`

**Design Rationale**: Separate table allows flexible addition of new preference types without schema changes. Unique constraint prevents duplicate preferences.

### 3. SavedSearch Model

**Model**: `App\Models\SavedSearch`

**Table**: `saved_searches`

**Schema**:

```php
Schema::create('saved_searches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('name', 50);
    $table->string('search_type'); // 'tickets' or 'loans'
    $table->json('filters'); // Stored filter criteria
    $table->timestamps();
    
    $table->index(['user_id', 'search_type']);
});
```

**Filter Structure** (JSON):

```json
{
    "status": ["pending", "in_progress"],
    "date_from": "2025-01-01",
    "date_to": "2025-12-31",
    "category": ["hardware", "software"],
    "priority": ["high", "critical"]
}
```

**Design Rationale**: JSON storage provides flexibility for varying filter combinations without rigid schema. Indexed user_id enables fast retrieval of user's saved searches.

### 4. PortalActivity Model

**Model**: `App\Models\PortalActivity`

**Table**: `portal_activities`

**Schema**:

```php
Schema::create('portal_activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('activity_type'); // e.g., 'ticket_submitted'
    $table->morphs('subject'); // Polymorphic relation to ticket/loan
    $table->json('metadata')->nullable(); // Additional activity data
    $table->timestamp('created_at');
    
    $table->index(['user_id', 'created_at']);
    $table->index(['subject_type', 'subject_id']);
});
```

**Activity Types**:

- `ticket_submitted`
- `ticket_status_changed`
- `loan_applied`
- `loan_approved`
- `loan_rejected`
- `asset_returned`
- `comment_added`
- `profile_updated`

**Design Rationale**: Polymorphic relationship allows tracking activities across different submission types. Metadata JSON field stores type-specific details without schema changes.

### 5. InternalComment Model

**Model**: `App\Models\InternalComment`

**Table**: `internal_comments`

**Schema**:

```php
Schema::create('internal_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->morphs('commentable'); // Polymorphic to ticket/loan
    $table->foreignId('parent_id')->nullable()->constrained('internal_comments')->onDelete('cascade');
    $table->text('comment');
    $table->json('mentions')->nullable(); // Array of mentioned user IDs
    $table->timestamps();
    
    $table->index(['commentable_type', 'commentable_id']);
    $table->index('parent_id');
});
```

**Relationships**:

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function commentable(): MorphTo
{
    return $this->morphTo();
}

public function parent(): BelongsTo
{
    return $this->belongsTo(InternalComment::class, 'parent_id');
}

public function replies(): HasMany
{
    return $this->hasMany(InternalComment::class, 'parent_id');
}
```

**Design Rationale**: Self-referencing parent_id enables threaded comments with maximum depth of 3 levels. Polymorphic relationship supports comments on both tickets and loans.

## Services and Business Logic

### 1. DashboardService

**Service**: `App\Services\DashboardService`

**Purpose**: Aggregate dashboard statistics and activity data

**Methods**:

```php
public function getStatistics(User $user): array
{
    return Cache::remember("portal.statistics.{$user->id}", 300, function () use ($user) {
        return [
            'open_tickets' => $this->getOpenTicketsCount($user),
            'pending_loans' => $this->getPendingLoansCount($user),
            'overdue_items' => $this->getOverdueItemsCount($user),
            'available_assets' => $this->getAvailableAssetsCount(),
        ];
    });
}

public function getRecentActivity(User $user, int $limit = 10): Collection
{
    return PortalActivity::where('user_id', $user->id)
        ->with('subject')
        ->latest()
        ->limit($limit)
        ->get();
}

public function getRoleSpecificWidgets(User $user): array
{
    $widgets = [];
    
    if ($user->isApprover()) {
        $widgets['pending_approvals'] = $this->getPendingApprovalsCount($user);
    }
    
    if ($user->hasRole('admin')) {
        $widgets['system_overview'] = $this->getSystemOverview();
    }
    
    return $widgets;
}
```

**Design Rationale**: Centralized service encapsulates dashboard logic and caching strategy. Role-based widget loading ensures efficient data retrieval.

### 2. SubmissionService

**Service**: `App\Services\SubmissionService`

**Purpose**: Handle submission queries, filtering, and export operations

**Methods**:

```php
public function getUserSubmissions(User $user, string $type, array $filters = []): LengthAwarePaginator
{
    $query = $type === 'tickets' 
        ? HelpdeskTicket::where('user_id', $user->id)
        : LoanApplication::where('user_id', $user->id);
    
    $query = $this->applyFilters($query, $filters);
    
    return $query->with($this->getEagerLoadRelations($type))
        ->paginate(25);
}

public function searchSubmissions(User $user, string $searchTerm): Collection
{
    $tickets = HelpdeskTicket::where('user_id', $user->id)
        ->where(function ($query) use ($searchTerm) {
            $query->where('ticket_number', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%");
        })
        ->limit(10)
        ->get();
    
    $loans = LoanApplication::where('user_id', $user->id)
        ->where(function ($query) use ($searchTerm) {
            $query->where('application_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('asset', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
        })
        ->limit(10)
        ->get();
    
    return $tickets->merge($loans);
}

protected function applyFilters($query, array $filters)
{
    if (isset($filters['status'])) {
        $query->whereIn('status', $filters['status']);
    }
    
    if (isset($filters['date_from'])) {
        $query->where('created_at', '>=', $filters['date_from']);
    }
    
    if (isset($filters['date_to'])) {
        $query->where('created_at', '<=', $filters['date_to']);
    }
    
    return $query;
}
```

**Design Rationale**: Unified service handles both ticket and loan queries with consistent filtering logic. Eager loading prevents N+1 queries.

### 3. NotificationService

**Service**: `App\Services\NotificationService`

**Purpose**: Manage notification creation, delivery, and preferences

**Methods**:

```php
public function sendNotification(User $user, string $type, array $data): void
{
    // Check user preferences
    if (!$this->shouldSendNotification($user, $type)) {
        return;
    }
    
    // Create database notification
    $notification = $user->notifications()->create([
        'type' => $type,
        'data' => $data,
        'read_at' => null,
    ]);
    
    // Broadcast real-time notification
    broadcast(new NotificationCreated($user, $notification))->toOthers();
    
    // Send email if preference enabled
    if ($this->shouldSendEmail($user, $type)) {
        Mail::to($user)->queue(new NotificationEmail($notification));
    }
}

public function shouldSendNotification(User $user, string $type): bool
{
    $preferenceKey = $this->getPreferenceKey($type);
    
    return $user->notificationPreferences()
        ->where('preference_key', $preferenceKey)
        ->value('preference_value') ?? true;
}

protected function getPreferenceKey(string $type): string
{
    return match($type) {
        'ticket_assigned', 'ticket_resolved' => 'ticket_status_updates',
        'loan_approved', 'loan_rejected' => 'loan_approval_notifications',
        'asset_overdue' => 'overdue_reminders',
        'system_announcement' => 'system_announcements',
        default => 'general_notifications',
    };
}
```

**Design Rationale**: Preference checking before notification creation reduces unnecessary database writes. Queued emails prevent blocking operations.

### 4. ExportService

**Service**: `App\Services\ExportService`

**Purpose**: Generate CSV and PDF exports of submission history

**Methods**:

```php
public function exportSubmissions(User $user, string $format, array $filters = []): string
{
    $submissions = $this->getSubmissionsForExport($user, $filters);
    
    if (count($submissions) > 1000) {
        return $this->queueLargeExport($user, $format, $filters);
    }
    
    return $format === 'csv' 
        ? $this->generateCSV($submissions)
        : $this->generatePDF($submissions, $user);
}

protected function generateCSV(Collection $submissions): string
{
    $filename = 'submissions_' . now()->format('Y-m-d_His') . '.csv';
    $path = storage_path("app/exports/{$filename}");
    
    $file = fopen($path, 'w');
    
    // Write headers
    fputcsv($file, [
        'Submission Type',
        'Number',
        'Subject/Asset',
        'Status',
        'Date Submitted',
        'Last Updated',
    ]);
    
    // Write data
    foreach ($submissions as $submission) {
        fputcsv($file, $this->formatSubmissionRow($submission));
    }
    
    fclose($file);
    
    return $filename;
}

protected function generatePDF(Collection $submissions, User $user): string
{
    $pdf = PDF::loadView('exports.submissions', [
        'submissions' => $submissions,
        'user' => $user,
        'generated_at' => now(),
    ]);
    
    $filename = 'submissions_' . now()->format('Y-m-d_His') . '.pdf';
    $path = storage_path("app/exports/{$filename}");
    
    $pdf->save($path);
    
    return $filename;
}

protected function queueLargeExport(User $user, string $format, array $filters): string
{
    $jobId = Str::uuid();
    
    ExportSubmissionsJob::dispatch($user, $format, $filters, $jobId);
    
    return $jobId;
}
```

**Design Rationale**: Synchronous exports for small datasets (<1000 records) provide immediate downloads. Large exports queued to prevent timeout issues.

### 5. GuestSubmissionClaimService

**Service**: `App\Services\GuestSubmissionClaimService`

**Purpose**: Handle claiming of guest submissions by authenticated users

**Methods**:

```php
public function findClaimableSubmissions(User $user): Collection
{
    $tickets = HelpdeskTicket::where('email', $user->email)
        ->whereNull('user_id')
        ->get();
    
    $loans = LoanApplication::where('email', $user->email)
        ->whereNull('user_id')
        ->get();
    
    return $tickets->merge($loans);
}

public function claimSubmission(User $user, $submission): bool
{
    if (!$this->verifyOwnership($user, $submission)) {
        throw new UnauthorizedException('Email mismatch');
    }
    
    DB::transaction(function () use ($user, $submission) {
        $submission->update(['user_id' => $user->id]);
        
        PortalActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'submission_claimed',
            'subject_type' => get_class($submission),
            'subject_id' => $submission->id,
        ]);
        
        $this->sendClaimConfirmation($user, $submission);
    });
    
    return true;
}

protected function verifyOwnership(User $user, $submission): bool
{
    return $submission->email === $user->email;
}

protected function sendClaimConfirmation(User $user, $submission): void
{
    Mail::to($user)->send(new SubmissionClaimedMail($submission));
}
```

**Design Rationale**: Email verification ensures secure ownership transfer. Transaction wrapping ensures atomic claim operation with activity logging.

## Authorization and Security

### 1. Role-Based Access Control (RBAC)

**Roles**:

- `staff` - Basic portal access
- `approver` - Staff + approval capabilities (Grade 41+)
- `admin` - Approver + admin panel access
- `superuser` - Admin + system configuration

**Permission Structure**:

```php
// Staff permissions
'view_own_submissions'
'create_submissions'
'update_own_profile'
'view_notifications'

// Approver permissions (includes staff)
'view_pending_approvals'
'approve_loans'
'reject_loans'
'bulk_approve'

// Admin permissions (includes approver)
'access_admin_panel'
'view_all_submissions'
'manage_users'

// Superuser permissions (includes admin)
'configure_system'
'manage_roles'
'view_audit_logs'
```

**Implementation**:

```php
// Middleware
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/portal/dashboard', [DashboardController::class, 'index']);
    Route::get('/portal/submissions', [SubmissionController::class, 'index']);
    Route::get('/portal/profile', [ProfileController::class, 'edit']);
});

Route::middleware(['auth', 'role:approver'])->group(function () {
    Route::get('/portal/approvals', [ApprovalController::class, 'index']);
    Route::post('/portal/approvals/{id}/approve', [ApprovalController::class, 'approve']);
});

// Policy
class SubmissionPolicy
{
    public function view(User $user, $submission): bool
    {
        return $user->id === $submission->user_id 
            || $user->hasRole(['admin', 'superuser']);
    }
    
    public function update(User $user, $submission): bool
    {
        return $user->id === $submission->user_id 
            && in_array($submission->status, ['draft', 'pending']);
    }
}
```

**Design Rationale**: Hierarchical role structure simplifies permission management. Policies provide fine-grained authorization at model level.

### 2. Authentication and Session Management

**Authentication Flow**:

1. User logs in via Laravel Breeze login form
2. Credentials validated against `users` table
3. Session created with 30-minute inactivity timeout
4. User redirected to `/portal/dashboard`

**Session Configuration**:

```php
// config/session.php
'lifetime' => 30, // 30 minutes
'expire_on_close' => false,
'encrypt' => true,
'driver' => 'redis',
```

**Session Timeout Warning**:

```javascript
// resources/js/session-timeout.js
let inactivityTimer;
let warningTimer;

function resetTimers() {
    clearTimeout(inactivityTimer);
    clearTimeout(warningTimer);
    
    // Show warning 2 minutes before timeout
    warningTimer = setTimeout(showWarningModal, 28 * 60 * 1000);
    
    // Auto logout after 30 minutes
    inactivityTimer = setTimeout(logout, 30 * 60 * 1000);
}

function showWarningModal() {
    // Display modal with "Extend Session" button
    Livewire.dispatch('show-session-warning');
}

// Reset timers on user activity
document.addEventListener('mousemove', resetTimers);
document.addEventListener('keypress', resetTimers);
```

**Rate Limiting**:

```php
// bootstrap/app.php
RateLimiter::for('portal-login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

// Failed login tracking
if (Auth::attempt($credentials)) {
    $request->session()->regenerate();
    Cache::forget("login_attempts:{$request->ip()}");
} else {
    $attempts = Cache::increment("login_attempts:{$request->ip()}");
    
    if ($attempts >= 5) {
        Cache::put("login_lockout:{$request->ip()}", true, 900); // 15 minutes
    }
}
```

**Design Rationale**: Redis-backed sessions provide fast access and easy scaling. Session warning prevents unexpected logouts during active use.

### 3. CSRF Protection

**Implementation**:

- All POST/PUT/DELETE requests require CSRF token
- Livewire automatically includes CSRF token in requests
- Token rotation on authentication state changes

```blade
<!-- Blade forms -->
<form method="POST" action="/portal/profile">
    @csrf
    <!-- form fields -->
</form>

<!-- Livewire components (automatic) -->
<livewire:portal.profile.profile-form />
```

**Design Rationale**: Laravel's built-in CSRF protection prevents cross-site request forgery attacks. Livewire integration simplifies token management.

## Real-Time Features

### 1. Laravel Echo Configuration

**Broadcasting Setup**:

```php
// config/broadcasting.php
'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST', '127.0.0.1'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
        ],
    ],
],
```

**Client Configuration**:

```javascript
// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
```

### 2. Real-Time Notifications

**Event**: `App\Events\NotificationCreated`

```php
class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public function __construct(
        public User $user,
        public DatabaseNotification $notification
    ) {}
    
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->user->id}"),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'notification.created';
    }
    
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(),
        ];
    }
}
```

**Livewire Listener**:

```php
// App\Livewire\Portal\Notifications\NotificationBell
class NotificationBell extends Component
{
    public int $unreadCount = 0;
    
    protected $listeners = ['echo:notification.created' => 'handleNewNotification'];
    
    public function mount(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }
    
    public function handleNewNotification($event): void
    {
        $this->unreadCount++;
        $this->dispatch('notification-received', $event);
    }
    
    public function getListeners(): array
    {
        return [
            "echo-private:user.{$this->userId},notification.created" => 'handleNewNotification',
        ];
    }
}
```

**Design Rationale**: Private channels ensure users only receive their own notifications. Event broadcasting provides real-time updates without polling.

### 3. Dashboard Statistics Updates

**Livewire Polling**:

```php
// App\Livewire\Portal\Dashboard\StatisticsCards
class StatisticsCards extends Component
{
    use OptimizedLivewireComponent;
    
    public array $statistics = [];
    
    public function mount(): void
    {
        $this->refreshStatistics();
    }
    
    public function refreshStatistics(): void
    {
        $this->statistics = app(DashboardService::class)
            ->getStatistics(auth()->user());
    }
    
    public function render(): View
    {
        return view('livewire.portal.dashboard.statistics-cards');
    }
}
```

```blade
<!-- resources/views/livewire/portal/dashboard/statistics-cards.blade.php -->
<div wire:poll.300s="refreshStatistics">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Statistics cards -->
    </div>
</div>
```

**Design Rationale**: 300-second polling interval balances data freshness with server load. Livewire polling provides automatic updates without custom JavaScript.

## Performance Optimization

### 1. Caching Strategy

**Cache Layers**:

**Level 1: Dashboard Statistics** (5-minute TTL)

```php
Cache::remember("portal.statistics.{$userId}", 300, function () {
    return [
        'open_tickets' => HelpdeskTicket::where('user_id', $userId)
            ->whereIn('status', ['submitted', 'assigned', 'in_progress'])
            ->count(),
        // ... other statistics
    ];
});
```

**Level 2: User Profile Data** (10-minute TTL)

```php
Cache::remember("portal.profile.{$userId}", 600, function () use ($user) {
    return [
        'user' => $user->load('division', 'notificationPreferences'),
        'completeness' => $user->profile_completeness,
    ];
});
```

**Level 3: Submission Lists** (Session-based)

```php
// Cache filtered submission results for current session
session()->put("submissions.{$filterHash}", $submissions, 300);
```

**Cache Invalidation**:

```php
// On submission status change
Cache::forget("portal.statistics.{$userId}");

// On profile update
Cache::forget("portal.profile.{$userId}");

// On new submission
Cache::tags(['submissions', "user.{$userId}"])->flush();
```

**Design Rationale**: Multi-layer caching reduces database load while maintaining data freshness. Tag-based invalidation ensures cache consistency.

### 2. Query Optimization

**Eager Loading Strategy**:

```php
// Submission list with relationships
HelpdeskTicket::where('user_id', $userId)
    ->with([
        'division:id,name',
        'category:id,name',
        'assignedUser:id,name',
        'latestComment' => function ($query) {
            $query->latest()->limit(1);
        }
    ])
    ->select(['id', 'ticket_number', 'subject', 'status', 'priority', 'created_at'])
    ->paginate(25);

// Submission detail with full relationships
HelpdeskTicket::with([
    'user:id,name,email',
    'division',
    'category',
    'attachments',
    'comments.user:id,name',
    'activities.user:id,name',
])
->findOrFail($id);
```

**Index Strategy**:

```php
// Database indexes for common queries
Schema::table('helpdesk_tickets', function (Blueprint $table) {
    $table->index(['user_id', 'status']);
    $table->index(['user_id', 'created_at']);
    $table->index('ticket_number');
});

Schema::table('loan_applications', function (Blueprint $table) {
    $table->index(['user_id', 'status']);
    $table->index(['user_id', 'created_at']);
    $table->index('application_number');
});

Schema::table('portal_activities', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index(['subject_type', 'subject_id']);
});
```

**Design Rationale**: Selective eager loading prevents N+1 queries while minimizing memory usage. Composite indexes optimize common query patterns.

### 3. Asset Optimization

**Vite Configuration**:

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/portal.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs', 'axios'],
                    'livewire': ['livewire'],
                },
            },
        },
    },
});
```

**Image Optimization**:

- Convert images to WebP format
- Implement lazy loading for images below fold
- Use responsive images with srcset
- Compress images to <100KB

**Design Rationale**: Code splitting reduces initial bundle size. Lazy loading improves perceived performance.

## Accessibility Implementation

### 1. WCAG 2.2 Level AA Compliance

**Color Palette** (Compliant Contrast Ratios):

```css
/* Primary colors */
--color-primary: #0056b3;      /* 6.8:1 contrast ratio */
--color-success: #198754;      /* 4.9:1 contrast ratio */
--color-warning: #ff8c00;      /* 4.5:1 contrast ratio */
--color-danger: #b50c0c;       /* 8.2:1 contrast ratio */

/* Text colors */
--color-text-primary: #1a1a1a;    /* 15.3:1 on white */
--color-text-secondary: #4a4a4a;  /* 9.7:1 on white */
--color-text-muted: #6b6b6b;      /* 6.5:1 on white */

/* Background colors */
--color-bg-primary: #ffffff;
--color-bg-secondary: #f8f9fa;
--color-bg-tertiary: #e9ecef;
```

**Focus Indicators**:

```css
/* Global focus styles */
*:focus {
    outline: 3px solid var(--color-primary);
    outline-offset: 2px;
}

/* Interactive elements */
button:focus,
a:focus,
input:focus,
select:focus,
textarea:focus {
    outline: 4px solid var(--color-primary);
    outline-offset: 2px;
    box-shadow: 0 0 0 4px rgba(0, 86, 179, 0.1);
}

/* Skip to content link */
.skip-to-content:focus {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 9999;
    padding: 1rem 2rem;
    background: var(--color-primary);
    color: white;
}
```

**Touch Targets**:

```css
/* Minimum 44x44px touch targets */
button,
a.button,
input[type="checkbox"],
input[type="radio"] {
    min-width: 44px;
    min-height: 44px;
    padding: 0.75rem 1.5rem;
}

/* Mobile-specific adjustments */
@media (max-width: 767px) {
    .touch-target {
        min-width: 48px;
        min-height: 48px;
    }
}
```

**Design Rationale**: Compliant color palette ensures readability for users with visual impairments. Prominent focus indicators aid keyboard navigation.

### 2. Semantic HTML and ARIA

**Navigation Structure**:

```blade
<nav aria-label="Main navigation" class="portal-nav">
    <ul role="menubar">
        <li role="none">
            <a href="/portal/dashboard" 
               role="menuitem" 
               aria-current="{{ request()->is('portal/dashboard') ? 'page' : 'false' }}">
                Dashboard
            </a>
        </li>
        <!-- Additional menu items -->
    </ul>
</nav>
```

**Form Accessibility**:

```blade
<form wire:submit.prevent="updateProfile">
    <div class="form-group">
        <label for="name" class="required">
            Full Name
            <span class="sr-only">(required)</span>
        </label>
        <input 
            type="text" 
            id="name" 
            wire:model.live.debounce.300ms="name"
            aria-required="true"
            aria-describedby="name-error"
            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
        />
        @error('name')
            <span id="name-error" class="error" role="alert">{{ $message }}</span>
        @enderror
    </div>
</form>
```

**Live Regions for Dynamic Content**:

```blade
<!-- Notification updates -->
<div aria-live="polite" aria-atomic="true" class="sr-only">
    @if($unreadCount > 0)
        You have {{ $unreadCount }} unread notifications
    @endif
</div>

<!-- Form submission feedback -->
<div wire:loading wire:target="updateProfile" 
     aria-live="assertive" 
     aria-atomic="true">
    Saving profile changes...
</div>
```

**Design Rationale**: Semantic HTML provides structure for assistive technologies. ARIA attributes enhance dynamic content accessibility.

### 3. Keyboard Navigation

**Tab Order Management**:

```blade
<!-- Skip to content link -->
<a href="#main-content" class="skip-to-content" tabindex="1">
    Skip to main content
</a>

<!-- Main navigation -->
<nav tabindex="2">
    <!-- Navigation items -->
</nav>

<!-- Main content -->
<main id="main-content" tabindex="-1">
    <!-- Page content -->
</main>
```

**Keyboard Shortcuts**:

```javascript
// Global keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Alt + D: Dashboard
    if (e.altKey && e.key === 'd') {
        e.preventDefault();
        window.location.href = '/portal/dashboard';
    }
    
    // Alt + S: Submissions
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        window.location.href = '/portal/submissions';
    }
    
    // Alt + P: Profile
    if (e.altKey && e.key === 'p') {
        e.preventDefault();
        window.location.href = '/portal/profile';
    }
    
    // Escape: Close modals
    if (e.key === 'Escape') {
        Livewire.dispatch('close-modal');
    }
});
```

**Design Rationale**: Logical tab order improves navigation efficiency. Keyboard shortcuts provide power users with quick access to common features.

## Responsive Design

### 1. Breakpoint Strategy

**Breakpoints**:

```css
/* Mobile: 320px - 767px */
@media (max-width: 767px) {
    /* Mobile-first styles */
}

/* Tablet: 768px - 1024px */
@media (min-width: 768px) and (max-width: 1024px) {
    /* Tablet-specific styles */
}

/* Desktop: 1280px+ */
@media (min-width: 1280px) {
    /* Desktop-specific styles */
}
```

**Layout Adaptation**:

```blade
<!-- Dashboard grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
    <!-- Statistics cards -->
</div>

<!-- Submission table -->
<div class="overflow-x-auto">
    <table class="min-w-full">
        <!-- Table content -->
    </table>
</div>

<!-- Mobile navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t">
    <div class="flex justify-around">
        <!-- Bottom navigation items -->
    </div>
</nav>
```

### 2. Mobile-Specific Features

**Hamburger Menu**:

```blade
<!-- Mobile menu toggle -->
<button 
    @click="mobileMenuOpen = !mobileMenuOpen"
    class="lg:hidden"
    aria-label="Toggle menu"
    aria-expanded="false"
>
    <svg class="w-6 h-6"><!-- Hamburger icon --></svg>
</button>

<!-- Slide-out menu -->
<div 
    x-show="mobileMenuOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg lg:hidden"
>
    <!-- Menu content -->
</div>
```

**Touch Gestures**:

```javascript
// Swipe to open/close menu
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
});

document.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
        // Swipe left - close menu
        Alpine.store('menu').close();
    }
    if (touchEndX > touchStartX + 50) {
        // Swipe right - open menu
        Alpine.store('menu').open();
    }
}
```

**Pull-to-Refresh**:

```javascript
// Pull-to-refresh for dashboard
let startY = 0;
let currentY = 0;
let pulling = false;

document.addEventListener('touchstart', (e) => {
    if (window.scrollY === 0) {
        startY = e.touches[0].pageY;
        pulling = true;
    }
});

document.addEventListener('touchmove', (e) => {
    if (pulling) {
        currentY = e.touches[0].pageY;
        const pullDistance = currentY - startY;
        
        if (pullDistance > 80) {
            Livewire.dispatch('refresh-dashboard');
            pulling = false;
        }
    }
});
```

**Design Rationale**: Mobile-first approach ensures optimal experience on all devices. Touch gestures provide intuitive mobile navigation.

## Internationalization (i18n)

### 1. Language Support

**Supported Languages**:

- Bahasa Melayu (primary) - `ms`
- English (secondary) - `en`

**Language Persistence**:

```php
// Middleware: SetLocale
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: Session > Cookie > Browser > Default
        $locale = session('locale') 
            ?? $request->cookie('locale')
            ?? $request->getPreferredLanguage(['ms', 'en'])
            ?? 'ms';
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}

// Language switcher
public function switchLanguage(string $locale): void
{
    if (!in_array($locale, ['ms', 'en'])) {
        return;
    }
    
    session(['locale' => $locale]);
    cookie()->queue('locale', $locale, 525600); // 1 year
    
    app()->setLocale($locale);
}
```

**Translation Files**:

```text
lang/
├── en/
│   ├── portal.php
│   ├── dashboard.php
│   ├── submissions.php
│   ├── profile.php
│   └── notifications.php
└── ms/
    ├── portal.php
    ├── dashboard.php
    ├── submissions.php
    ├── profile.php
    └── notifications.php
```

**Translation Usage**:

```blade
<!-- Blade templates -->
<h1>{{ __('portal.dashboard.title') }}</h1>
<p>{{ __('portal.dashboard.welcome', ['name' => $user->name]) }}</p>

<!-- Livewire components -->
<button wire:click="submit">
    {{ __('portal.actions.submit') }}
</button>
```

**Design Rationale**: Session and cookie persistence ensures language preference survives across sessions. No database storage maintains compatibility with guest forms.

## Error Handling

### 1. User-Friendly Error Messages

**Error Display Strategy**:

```php
// Exception Handler
public function render($request, Throwable $exception)
{
    if ($exception instanceof AuthorizationException) {
        return response()->view('errors.403', [
            'message' => __('portal.errors.unauthorized'),
            'suggestion' => __('portal.errors.contact_admin'),
        ], 403);
    }
    
    if ($exception instanceof ModelNotFoundException) {
        return response()->view('errors.404', [
            'message' => __('portal.errors.not_found'),
            'suggestion' => __('portal.errors.check_url'),
        ], 404);
    }
    
    return parent::render($request, $exception);
}
```

**Livewire Error Handling**:

```php
// Component error handling
public function updateProfile(): void
{
    try {
        $this->validate();
        
        $this->user->update([
            'name' => $this->name,
            'phone' => $this->phone,
        ]);
        
        $this->dispatch('profile-updated');
        session()->flash('success', __('portal.profile.updated'));
        
    } catch (ValidationException $e) {
        $this->addError('validation', __('portal.errors.validation_failed'));
        
    } catch (\Exception $e) {
        Log::error('Profile update failed', [
            'user_id' => $this->user->id,
            'error' => $e->getMessage(),
        ]);
        
        $this->addError('general', __('portal.errors.update_failed'));
    }
}
```

**Error Templates**:

```blade
<!-- resources/views/errors/403.blade.php -->
<div class="error-container">
    <h1>{{ __('portal.errors.403_title') }}</h1>
    <p>{{ $message }}</p>
    <p class="suggestion">{{ $suggestion }}</p>
    
    <div class="actions">
        <a href="{{ route('portal.dashboard') }}" class="btn btn-primary">
            {{ __('portal.actions.back_to_dashboard') }}
        </a>
        <button onclick="contactSupport()" class="btn btn-secondary">
            {{ __('portal.actions.contact_support') }}
        </button>
    </div>
</div>
```

**Design Rationale**: User-friendly error messages provide clear explanations and actionable next steps. Logging ensures technical details are captured for debugging.

### 2. Validation Error Display

**Form Validation**:

```blade
<div class="form-group">
    <label for="phone">{{ __('portal.profile.phone') }}</label>
    <input 
        type="text" 
        id="phone" 
        wire:model.live.debounce.300ms="phone"
        class="@error('phone') border-danger @enderror"
    />
    @error('phone')
        <span class="error-message" role="alert">
            <svg class="icon"><!-- Error icon --></svg>
            {{ $message }}
        </span>
    @enderror
</div>
```

**Inline Validation Feedback**:

```php
// Real-time validation
public function updatedPhone($value): void
{
    $this->validateOnly('phone', [
        'phone' => ['nullable', 'regex:/^[0-9]{10,15}$/'],
    ]);
}
```

**Design Rationale**: Real-time validation provides immediate feedback. Visual error indicators improve error visibility.

## Testing Strategy

### 1. Unit Tests

**Model Tests**:

```php
// tests/Unit/Models/UserTest.php
class UserTest extends TestCase
{
    public function test_is_approver_returns_true_for_grade_41_and_above(): void
    {
        $user = User::factory()->create(['grade' => 41]);
        
        $this->assertTrue($user->isApprover());
    }
    
    public function test_profile_completeness_calculation(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'phone' => '0123456789',
        ]);
        
        $this->assertEquals(100, $user->profile_completeness);
    }
}
```

**Service Tests**:

```php
// tests/Unit/Services/DashboardServiceTest.php
class DashboardServiceTest extends TestCase
{
    public function test_get_statistics_returns_correct_counts(): void
    {
        $user = User::factory()->create();
        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);
        
        $service = new DashboardService();
        $statistics = $service->getStatistics($user);
        
        $this->assertEquals(3, $statistics['open_tickets']);
    }
}
```

### 2. Feature Tests

**Dashboard Tests**:

```php
// tests/Feature/Portal/DashboardTest.php
class DashboardTest extends TestCase
{
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/portal/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }
    
    public function test_dashboard_displays_statistics_cards(): void
    {
        $user = User::factory()->create();
        HelpdeskTicket::factory()->count(2)->create(['user_id' => $user->id]);
        
        Livewire::actingAs($user)
            ->test(StatisticsCards::class)
            ->assertSee('My Open Tickets')
            ->assertSee('2');
    }
}
```

**Submission Tests**:

```php
// tests/Feature/Portal/SubmissionTest.php
class SubmissionTest extends TestCase
{
    public function test_user_can_view_own_submissions(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->get("/portal/submissions/{$ticket->id}");
        
        $response->assertStatus(200);
        $response->assertSee($ticket->ticket_number);
    }
    
    public function test_user_cannot_view_others_submissions(): void
    {
        $user = User::factory()->create();
        $otherTicket = HelpdeskTicket::factory()->create();
        
        $response = $this->actingAs($user)
            ->get("/portal/submissions/{$otherTicket->id}");
        
        $response->assertStatus(403);
    }
}
```

### 3. Livewire Component Tests

**Component Interaction Tests**:

```php
// tests/Feature/Livewire/ProfileFormTest.php
class ProfileFormTest extends TestCase
{
    public function test_profile_form_updates_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        
        Livewire::actingAs($user)
            ->test(ProfileForm::class)
            ->set('name', 'New Name')
            ->set('phone', '0123456789')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertDispatched('profile-updated');
        
        $this->assertEquals('New Name', $user->fresh()->name);
    }
    
    public function test_profile_form_validates_phone_format(): void
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(ProfileForm::class)
            ->set('phone', 'invalid')
            ->call('updateProfile')
            ->assertHasErrors(['phone']);
    }
}
```

**Approval Tests**:

```php
// tests/Feature/Livewire/ApprovalQueueTest.php
class ApprovalQueueTest extends TestCase
{
    public function test_approver_can_approve_loan_application(): void
    {
        $approver = User::factory()->create(['grade' => 41]);
        $application = LoanApplication::factory()->create(['status' => 'pending']);
        
        Livewire::actingAs($approver)
            ->test(ApprovalModal::class, ['applicationId' => $application->id])
            ->set('action', 'approve')
            ->set('remarks', 'Approved for testing')
            ->call('processApproval')
            ->assertHasNoErrors();
        
        $this->assertEquals('approved', $application->fresh()->status);
    }
}
```

### 4. Browser Tests (Playwright)

**E2E Dashboard Test**:

```typescript
// tests/e2e/portal/dashboard.spec.ts
import { test, expect } from '@playwright/test';

test('authenticated user can navigate dashboard', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'staff@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Verify dashboard
    await expect(page).toHaveURL('/portal/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
    
    // Verify statistics cards
    await expect(page.locator('[data-testid="open-tickets"]')).toBeVisible();
    await expect(page.locator('[data-testid="pending-loans"]')).toBeVisible();
    
    // Test quick actions
    await page.click('[data-testid="submit-ticket-btn"]');
    await expect(page).toHaveURL('/portal/tickets/create');
});
```

**Design Rationale**: Comprehensive test coverage ensures reliability. Browser tests validate end-to-end user flows.

## Deployment and Migration

### 1. Database Migrations

**Migration Order**:

1. `create_user_notification_preferences_table`
2. `create_saved_searches_table`
3. `create_portal_activities_table`
4. `create_internal_comments_table`
5. `add_portal_fields_to_users_table`

**Example Migration**:

```php
// database/migrations/2025_11_06_000001_create_user_notification_preferences_table.php
public function up(): void
{
    Schema::create('user_notification_preferences', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('preference_key');
        $table->boolean('preference_value')->default(true);
        $table->timestamps();
        
        $table->unique(['user_id', 'preference_key']);
        $table->index('user_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('user_notification_preferences');
}
```

### 2. Seeder for Default Data

**NotificationPreferenceSeeder**:

```php
// database/seeders/NotificationPreferenceSeeder.php
class NotificationPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            'ticket_status_updates',
            'loan_approval_notifications',
            'overdue_reminders',
            'system_announcements',
        ];
        
        User::chunk(100, function ($users) use ($preferences) {
            foreach ($users as $user) {
                foreach ($preferences as $preference) {
                    UserNotificationPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'preference_key' => $preference,
                    ], [
                        'preference_value' => true,
                    ]);
                }
            }
        });
    }
}
```

### 3. Deployment Checklist

**Pre-Deployment**:

- [ ] Run all tests: `php artisan test`
- [ ] Check code style: `vendor/bin/pint`
- [ ] Run static analysis: `vendor/bin/phpstan analyse`
- [ ] Build frontend assets: `npm run build`
- [ ] Review migration files
- [ ] Backup production database

**Deployment Steps**:

1. Enable maintenance mode: `php artisan down`
2. Pull latest code: `git pull origin main`
3. Install dependencies: `composer install --no-dev --optimize-autoloader`
4. Run migrations: `php artisan migrate --force`
5. Run seeders: `php artisan db:seed --class=NotificationPreferenceSeeder`
6. Clear caches: `php artisan optimize:clear`
7. Cache config: `php artisan config:cache`
8. Cache routes: `php artisan route:cache`
9. Restart queue workers: `php artisan queue:restart`
10. Disable maintenance mode: `php artisan up`

**Post-Deployment**:

- [ ] Verify dashboard loads correctly
- [ ] Test submission viewing
- [ ] Test profile updates
- [ ] Test approval workflow (if applicable)
- [ ] Monitor error logs
- [ ] Check performance metrics

**Design Rationale**: Structured deployment process minimizes downtime and ensures data integrity. Rollback plan available via migration down methods.

## Monitoring and Maintenance

### 1. Performance Monitoring

**Core Web Vitals Tracking**:

```javascript
// resources/js/performance-monitoring.js
import { onCLS, onFID, onLCP, onTTFB } from 'web-vitals';

function sendToAnalytics(metric) {
    fetch('/api/analytics/web-vitals', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: metric.name,
            value: metric.value,
            rating: metric.rating,
            page: window.location.pathname,
        }),
    });
}

onCLS(sendToAnalytics);
onFID(sendToAnalytics);
onLCP(sendToAnalytics);
onTTFB(sendToAnalytics);
```

**Performance Targets**:

- LCP (Largest Contentful Paint): <2.5s
- FID (First Input Delay): <100ms
- CLS (Cumulative Layout Shift): <0.1
- TTFB (Time to First Byte): <600ms

### 2. Error Monitoring

**Laravel Logging**:

```php
// config/logging.php
'channels' => [
    'portal' => [
        'driver' => 'daily',
        'path' => storage_path('logs/portal.log'),
        'level' => 'info',
        'days' => 14,
    ],
],

// Usage in components
Log::channel('portal')->info('User accessed dashboard', [
    'user_id' => auth()->id(),
    'timestamp' => now(),
]);
```

**Exception Tracking**:

```php
// app/Exceptions/Handler.php
public function report(Throwable $exception): void
{
    if ($this->shouldReport($exception)) {
        Log::channel('portal')->error('Exception occurred', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
    
    parent::report($exception);
}
```

### 3. Audit Logging

**Portal Activity Logging**:

```php
// Middleware: TrackPortalActivity
class TrackPortalActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (auth()->check()) {
            PortalActivity::create([
                'user_id' => auth()->id(),
                'activity_type' => $this->getActivityType($request),
                'subject_type' => $this->getSubjectType($request),
                'subject_id' => $this->getSubjectId($request),
                'metadata' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ],
            ]);
        }
        
        return $response;
    }
}
```

**Design Rationale**: Comprehensive monitoring enables proactive issue detection. Audit logging ensures compliance with 7-year retention requirements.

## Security Considerations

### 1. Data Protection (PDPA 2010 Compliance)

**Personal Data Handling**:

```php
// Encrypted fields in User model
protected $casts = [
    'phone' => 'encrypted',
];

// Data retention policy
class PurgeOldActivitiesCommand extends Command
{
    public function handle(): void
    {
        $retentionDate = now()->subYears(7);
        
        PortalActivity::where('created_at', '<', $retentionDate)->delete();
        
        $this->info('Old activities purged successfully');
    }
}
```

**Data Subject Rights**:

```php
// User data export
public function exportUserData(User $user): array
{
    return [
        'profile' => $user->only(['name', 'email', 'phone', 'staff_id', 'grade']),
        'tickets' => $user->helpdeskTickets()->get(),
        'loans' => $user->loanApplications()->get(),
        'activities' => $user->portalActivities()->get(),
        'preferences' => $user->notificationPreferences()->get(),
    ];
}

// User data deletion
public function deleteUserData(User $user): void
{
    DB::transaction(function () use ($user) {
        $user->notificationPreferences()->delete();
        $user->savedSearches()->delete();
        $user->portalActivities()->delete();
        $user->internalComments()->delete();
        
        // Anonymize submissions instead of deleting
        $user->helpdeskTickets()->update([
            'user_id' => null,
            'email' => 'deleted@example.com',
        ]);
        
        $user->delete();
    });
}
```

### 2. SQL Injection Prevention

**Parameterized Queries**:

```php
// Always use Eloquent or query builder
HelpdeskTicket::where('user_id', $userId)
    ->where('status', $status)
    ->get();

// Never use raw queries with user input
// BAD: DB::select("SELECT * FROM tickets WHERE user_id = {$userId}");

// If raw queries necessary, use bindings
DB::select('SELECT * FROM tickets WHERE user_id = ?', [$userId]);
```

### 3. XSS Prevention

**Output Escaping**:

```blade
<!-- Blade automatically escapes -->
<p>{{ $user->name }}</p>

<!-- Raw output only for trusted content -->
<div>{!! $trustedHtml !!}</div>

<!-- JavaScript context -->
<script>
    const userName = @json($user->name);
</script>
```

**Content Security Policy**:

```php
// Middleware: AddSecurityHeaders
public function handle(Request $request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('Content-Security-Policy', 
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
        "style-src 'self' 'unsafe-inline'; " .
        "img-src 'self' data: https:; " .
        "font-src 'self' data:;"
    );
    
    return $response;
}
```

**Design Rationale**: Multiple security layers provide defense in depth. Automated escaping prevents most XSS vulnerabilities.

## Future Enhancements

### 1. Planned Features (Phase 2)

**Advanced Analytics Dashboard**:

- Personal productivity metrics
- Submission trend analysis
- Comparative statistics with department averages
- Customizable dashboard widgets

**Enhanced Collaboration**:

- Real-time chat with assigned admins
- Video call integration for complex issues
- Collaborative document editing
- Team submission boards

**Mobile Application**:

- Native iOS and Android apps
- Push notifications
- Offline mode with sync
- Biometric authentication

### 2. Technical Improvements

**Performance Optimizations**:

- Implement Redis Cluster for horizontal scaling
- Add CDN for static assets
- Implement HTTP/2 Server Push
- Add service worker for offline capability

**Enhanced Security**:

- Two-factor authentication (2FA)
- Biometric authentication support
- Advanced threat detection
- Automated security scanning

**AI/ML Integration**:

- Intelligent ticket categorization
- Predictive asset availability
- Automated response suggestions
- Anomaly detection in submission patterns

### 3. Integration Opportunities

**External Systems**:

- Integration with MOTAC HR system for staff data sync
- Integration with asset management system
- Integration with email system for seamless communication
- Integration with calendar system for loan scheduling

**API Development**:

- RESTful API for mobile apps
- GraphQL API for flexible data queries
- Webhook support for external integrations
- OAuth 2.0 for third-party authentication

**Design Rationale**: Phased approach allows incremental feature delivery. Future enhancements align with user feedback and organizational needs.

## Conclusion

This design document provides a comprehensive blueprint for implementing the Authenticated Staff Dashboard and Profile system within the ICTServe platform. The design emphasizes:

1. **User Experience**: Intuitive interfaces with role-based feature access
2. **Performance**: Optimized queries, caching strategies, and Core Web Vitals compliance
3. **Accessibility**: WCAG 2.2 Level AA compliance throughout
4. **Security**: Multi-layered security with PDPA 2010 compliance
5. **Maintainability**: Clean architecture with separation of concerns
6. **Scalability**: Designed for growth with caching and optimization strategies

### Key Design Decisions

**Architecture**:

- Laravel 12 MVC architecture with Livewire 3 for reactive components
- Service layer for business logic separation
- Policy-based authorization for fine-grained access control

**Data Management**:

- Polymorphic relationships for flexible submission handling
- JSON fields for extensible metadata storage
- Comprehensive indexing for query performance

**User Interface**:

- Component-based design with reusable Blade components
- Mobile-first responsive design
- Real-time updates via Laravel Echo

**Performance**:

- Multi-layer caching strategy (Redis)
- Eager loading to prevent N+1 queries
- Asset optimization with Vite

**Security**:

- Role-based access control with four distinct roles
- Session management with timeout warnings
- Comprehensive audit logging

### Implementation Priorities

**Phase 1 (MVP)**:

1. Dashboard with statistics and recent activity
2. Submission history viewing
3. Basic profile management
4. Notification system

**Phase 2 (Enhanced Features)**:
5. Approval interface for Grade 41+ officers
6. Internal comments and collaboration
7. Advanced search and filtering
8. Export functionality

**Phase 3 (Advanced Features)**:
9. Guest submission claiming
10. Activity timeline
11. Mobile optimizations
12. Help and onboarding system

### Success Metrics

- Dashboard load time: <2.5s (LCP)
- User satisfaction: >85% positive feedback
- Adoption rate: >90% of staff using portal within 3 months
- Support ticket reduction: 30% decrease in basic inquiries
- Accessibility compliance: 100% WCAG 2.2 AA conformance

---

**Document Approval**:

- Technical Lead: _________________
- Project Manager: _________________
- Security Officer: _________________
- Date: _________________
