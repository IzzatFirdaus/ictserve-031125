<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Models\UserNotificationPreference;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public bool $ticketStatusUpdates = false;
    public bool $loanApprovalNotifications = false;
    public bool $overdueReminders = false;
    public bool $systemAnnouncements = false;

    protected array $keysMap = [
        'ticket_status_updates' => 'ticketStatusUpdates',
        'loan_approval_notifications' => 'loanApprovalNotifications',
        'overdue_reminders' => 'overdueReminders',
        'system_announcements' => 'systemAnnouncements',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) { return; }
        $prefs = UserNotificationPreference::where('user_id', $user->id)->get();
        foreach ($prefs as $pref) {
            $prop = $this->keysMap[$pref->preference_key] ?? null;
            if ($prop) {
                $this->{$prop} = (bool) $pref->preference_value;
            }
        }
    }

    public function updatePreference(string $key, bool $value): void
    {
        $user = Auth::user();
        if (!$user) { return; }
        UserNotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'preference_key' => $key,
            ],
            [
                'preference_value' => $value,
            ]
        );
        $prop = $this->keysMap[$key] ?? null;
        if ($prop) {
            $this->{$prop} = $value;
        }
    }

    public function updateAll(array $preferences): void
    {
        foreach ($preferences as $key => $value) {
            if (isset($this->keysMap[$key])) {
                $this->updatePreference($key, (bool) $value);
            }
        }
    }

    public function render()
    {
        return view('livewire.portal.notification-preferences');
    }
}
