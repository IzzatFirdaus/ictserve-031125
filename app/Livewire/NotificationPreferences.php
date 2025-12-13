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
     * Email frequency configuration per D12 §6.17
     * Options: immediate, daily_digest, weekly_digest, disabled
     */
    public string $emailFrequency = 'immediate';

    /**
     * Email digest time (for daily/weekly digests)
     */
    public string $digestTime = '09:00';

    /**
     * Email digest day (for weekly digest, 0=Sunday, 1=Monday, etc.)
     */
    public int $digestDay = 1;

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
            'email_frequency' => 'emailFrequency',
            'digest_time' => 'digestTime',
            'digest_day' => 'digestDay',
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
            'emailFrequency' => 'email_frequency',
            'digestTime' => 'digest_time',
            'digestDay' => 'digest_day',
            default => $property,
        };
    }

    /**
     * Update email frequency preference per D12 §6.17
     */
    public function updateEmailFrequency(string $frequency): void
    {
        $this->clearMessages();

        $validFrequencies = ['immediate', 'daily_digest', 'weekly_digest', 'disabled'];
        if (! \in_array($frequency, $validFrequencies, true)) {
            $this->errorMessage = __('portal.invalid_email_frequency');

            return;
        }

        try {
            $user = Auth::user();

            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'preference_key' => 'email_frequency',
                ],
                [
                    'preference_value' => $frequency,
                ]
            );

            $this->emailFrequency = $frequency;
            $this->lastSaved = now()->format('H:i:s');
            $this->successMessage = __('portal.email_frequency_updated');
            $this->dispatch('preference-saved');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.preference_update_failed');
            logger()->error('Email frequency update failed', [
                'user_id' => Auth::id(),
                'frequency' => $frequency,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update digest schedule (time and day)
     */
    public function updateDigestSchedule(): void
    {
        $this->clearMessages();

        try {
            $user = Auth::user();

            DB::transaction(function () use ($user) {
                UserNotificationPreference::updateOrCreate(
                    ['user_id' => $user->id, 'preference_key' => 'digest_time'],
                    ['preference_value' => $this->digestTime]
                );

                UserNotificationPreference::updateOrCreate(
                    ['user_id' => $user->id, 'preference_key' => 'digest_day'],
                    ['preference_value' => (string) $this->digestDay]
                );
            });

            $this->lastSaved = now()->format('H:i:s');
            $this->successMessage = __('portal.digest_schedule_updated');
            $this->dispatch('preference-saved');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.preference_update_failed');
            logger()->error('Digest schedule update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get available email frequency options per D12 §6.17
     *
     * @return array<string, string>
     */
    public function getEmailFrequencyOptions(): array
    {
        return [
            'immediate' => __('portal.email_frequency_immediate'),
            'daily_digest' => __('portal.email_frequency_daily'),
            'weekly_digest' => __('portal.email_frequency_weekly'),
            'disabled' => __('portal.email_frequency_disabled'),
        ];
    }

    /**
     * Get available days for weekly digest
     *
     * @return array<int, string>
     */
    public function getDigestDayOptions(): array
    {
        return [
            0 => __('portal.day_sunday'),
            1 => __('portal.day_monday'),
            2 => __('portal.day_tuesday'),
            3 => __('portal.day_wednesday'),
            4 => __('portal.day_thursday'),
            5 => __('portal.day_friday'),
            6 => __('portal.day_saturday'),
        ];
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
    public function render(): \Illuminate\View\View
    {
        return view('livewire.notification-preferences');
    }
}
