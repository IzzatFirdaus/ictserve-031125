<?php

declare(strict_types=1);

// name: NotificationPreferences
// description: Granular notification preference management with toggle controls for 6 notification types
// author: dev-team@motac.gov.my
// trace: SRS-FR-004; D04 §3.3.2; D11 §6; Requirements 3.2
// last-updated: 2025-11-07

namespace App\Livewire;

use App\Models\UserNotificationPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationPreferences extends Component
{
    /**
     * Notification preference states
     */
    public bool $ticketStatusUpdates = true;

    public bool $loanApprovalNotifications = true;

    public bool $overdueReminders = true;

    public bool $systemAnnouncements = true;

    public bool $ticketAssignments = true;

    public bool $commentReplies = true;

    /**
     * Success/error messages
     */
    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /**
     * Last saved timestamp
     */
    public ?string $lastSaved = null;

    /**
     * Mount the component and load user preferences
     */
    public function mount(): void
    {
        $this->loadPreferences();
    }

    /**
     * Load user notification preferences from database
     */
    protected function loadPreferences(): void
    {
        $user = Auth::user();
        $preferences = UserNotificationPreference::forUser($user->id)->get();

        // Map preferences to component properties
        foreach ($preferences as $preference) {
            $propertyName = $this->mapKeyToProperty($preference->preference_key);
            if (property_exists($this, $propertyName)) {
                $this->{$propertyName} = $preference->preference_value;
            }
        }
    }

    /**
     * Map preference key to component property name
     */
    protected function mapKeyToProperty(string $key): string
    {
        return match ($key) {
            'ticket_status_updates' => 'ticketStatusUpdates',
            'loan_approval_notifications' => 'loanApprovalNotifications',
            'overdue_reminders' => 'overdueReminders',
            'system_announcements' => 'systemAnnouncements',
            'ticket_assignments' => 'ticketAssignments',
            'comment_replies' => 'commentReplies',
            default => $key,
        };
    }

    /**
     * Map component property to preference key
     */
    protected function mapPropertyToKey(string $property): string
    {
        return match ($property) {
            'ticketStatusUpdates' => 'ticket_status_updates',
            'loanApprovalNotifications' => 'loan_approval_notifications',
            'overdueReminders' => 'overdue_reminders',
            'systemAnnouncements' => 'system_announcements',
            'ticketAssignments' => 'ticket_assignments',
            'commentReplies' => 'comment_replies',
            default => $property,
        };
    }

    /**
     * Update individual preference (called on toggle change)
     */
    public function updatePreference(string $property, bool $value): void
    {
        $this->clearMessages();

        try {
            $user = Auth::user();
            $key = $this->mapPropertyToKey($property);

            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'preference_key' => $key,
                ],
                [
                    'preference_value' => $value,
                ]
            );

            $this->lastSaved = now()->format('H:i:s');
            $this->successMessage = __('portal.preference_updated');

            // Auto-clear success message after 3 seconds
            $this->dispatch('preference-saved');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.preference_update_failed');
            logger()->error('Notification preference update failed', [
                'user_id' => Auth::id(),
                'property' => $property,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Save all preferences at once
     */
    public function saveAll(): void
    {
        $this->clearMessages();

        try {
            $user = Auth::user();
            $preferences = [
                'ticket_status_updates' => $this->ticketStatusUpdates,
                'loan_approval_notifications' => $this->loanApprovalNotifications,
                'overdue_reminders' => $this->overdueReminders,
                'system_announcements' => $this->systemAnnouncements,
                'ticket_assignments' => $this->ticketAssignments,
                'comment_replies' => $this->commentReplies,
            ];

            DB::transaction(function () use ($user, $preferences) {
                foreach ($preferences as $key => $value) {
                    UserNotificationPreference::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'preference_key' => $key,
                        ],
                        [
                            'preference_value' => $value,
                        ]
                    );
                }
            });

            $this->lastSaved = now()->format('H:i:s');
            $this->successMessage = __('portal.all_preferences_saved');
            $this->dispatch('preferences-saved');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.preferences_save_failed');
            logger()->error('Bulk notification preferences save failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Enable all notifications
     */
    public function enableAll(): void
    {
        $this->ticketStatusUpdates = true;
        $this->loanApprovalNotifications = true;
        $this->overdueReminders = true;
        $this->systemAnnouncements = true;
        $this->ticketAssignments = true;
        $this->commentReplies = true;

        $this->saveAll();
    }

    /**
     * Disable all notifications
     */
    public function disableAll(): void
    {
        $this->ticketStatusUpdates = false;
        $this->loanApprovalNotifications = false;
        $this->overdueReminders = false;
        $this->systemAnnouncements = false;
        $this->ticketAssignments = false;
        $this->commentReplies = false;

        $this->saveAll();
    }

    /**
     * Reset to defaults (all enabled except system announcements)
     */
    public function resetToDefaults(): void
    {
        $this->ticketStatusUpdates = true;
        $this->loanApprovalNotifications = true;
        $this->overdueReminders = true;
        $this->systemAnnouncements = false;
        $this->ticketAssignments = true;
        $this->commentReplies = true;

        $this->saveAll();
    }

    /**
     * Clear messages
     */
    public function clearMessages(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    /**
     * Get count of enabled preferences
     */
    #[Computed]
    public function enabledCount(): int
    {
        return collect([
            $this->ticketStatusUpdates,
            $this->loanApprovalNotifications,
            $this->overdueReminders,
            $this->systemAnnouncements,
            $this->ticketAssignments,
            $this->commentReplies,
        ])->filter(fn ($value) => $value === true)->count();
    }

    /**
     * Render the component
     */
    public function render(): mixed
    {
        return view('livewire.notification-preferences');
    }
}
