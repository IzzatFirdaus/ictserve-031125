<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * UserProfile Component
 *
 * Provides user profile management interface for authenticated staff members.
 * Allows editing of profile information, notification preferences, and password changes.
 * Implements Task 4.1.2: Profile management with editable and read-only fields
 * Implements Task 4.1.4: Request Data Correction action for read-only fields
 *
 * @author Frontend Engineering Team
 *
 * @trace D03-FR-020 (User Profile Management)
 * @trace D04 §5.3 (Authenticated Portal Design)
 * @trace D12 §4.2 (Profile Management UI)
 * @trace R25 (Profile Data Management)
 *
 * @version 2.0
 *
 * @task 4.1.2, 4.1.4
 *
 * @wcag WCAG 2.2 Level AA
 */
class UserProfile extends Component
{
    use OptimizedLivewireComponent;
    use WithFileUploads;

    // Profile Information
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    // Read-only fields (displayed but not editable)
    public string $email = '';

    public string $staff_id = '';

    public ?string $grade = null;

    public ?string $division = null;

    public ?string $position = null;

    // Notification Preferences
    /** @var array<string, bool> */
    public array $notificationPreferences = [];

    // Email Frequency Preference (immediate, daily, weekly) per Requirement 17.5
    public string $emailFrequency = 'immediate';

    // In-app notification toggle per Requirement 17.5
    public bool $inAppNotifications = true;

    // Password Change
    #[Validate('required|string|current_password')]
    public string $current_password = '';

    #[Validate('required|string|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    // UI State
    public bool $profileUpdateSuccess = false;

    public bool $passwordUpdateSuccess = false;

    public string $profileError = '';

    public string $passwordError = '';

    // Profile Picture
    public $profilePicture = null;

    public ?string $currentProfilePicture = null;

    public bool $profilePictureUpdateSuccess = false;

    public string $profilePictureError = '';

    /**
     * Mount component and load user data
     */
    public function mount(): void
    {
        // Check authentication
        if (! Auth::check()) {
            throw new \Illuminate\Auth\AuthenticationException;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Load editable fields
        $this->name = $user->name;
        $this->phone = $user->phone ?? '';

        // Load read-only fields
        $this->email = $user->email;
        $this->staff_id = $user->staff_id ?? 'N/A';

        // Load relationships - use accessor methods which handle locale
        $this->grade = $user->grade ? (string) $user->grade->name : 'N/A';
        $this->division = $user->division ? (string) $user->division->name : 'N/A';
        $this->position = $user->position ? (string) $user->position->name : 'N/A';

        // Load notification preferences
        $this->notificationPreferences = $user->getNotificationPreferences();

        // Load email frequency preference (default: immediate) per Requirement 17.5
        $emailFreq = $this->notificationPreferences['email_frequency'] ?? 'immediate';
        $this->emailFrequency = is_string($emailFreq) ? $emailFreq : 'immediate';

        // Load in-app notification toggle per Requirement 17.5
        $inAppPref = $this->notificationPreferences['realtime_notifications'] ?? true;
        $this->inAppNotifications = (bool) $inAppPref;

        // Load current profile picture
        $this->currentProfilePicture = $user->profile_picture;
    }

    /**
     * Update user profile information
     */
    public function updateProfile(): void
    {
        $this->profileUpdateSuccess = false;
        $this->profileError = '';

        try {
            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?: null, // Convert empty string to null
            ]);

            $this->profileUpdateSuccess = true;

            // Announce success to screen readers
            $this->dispatch('profile-updated', message: __('profile.update_success'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire handles them
            throw $e;
        } catch (\Exception $e) {
            $this->profileError = __('profile.update_error');
            Log::error('Profile update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(): void
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->setNotificationPreferences($this->notificationPreferences);

            // Announce success to screen readers
            $this->dispatch('preferences-updated', message: __('profile.preferences_updated'));
        } catch (\Exception $e) {
            Log::error('Notification preferences update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update email frequency preference
     * Implements Requirement 17.5: email frequency (immediate, daily digest, weekly digest)
     *
     * @trace D03 SRS-ADM-006, D16
     */
    public function updateEmailFrequency(): void
    {
        try {
            // Validate email frequency value
            if (! in_array($this->emailFrequency, ['immediate', 'daily', 'weekly'], true)) {
                $this->emailFrequency = 'immediate';
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $preferences = $user->getNotificationPreferences();
            $preferences['email_frequency'] = $this->emailFrequency;
            $user->setNotificationPreferences($preferences);

            // Announce success to screen readers
            $this->dispatch('preferences-updated', message: __('profile.email_frequency_updated'));
        } catch (\Exception $e) {
            Log::error('Email frequency update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update in-app notification toggle
     * Implements Requirement 17.5: in-app notification toggle
     *
     * @trace D03 SRS-ADM-006, D16
     */
    public function updateInAppNotifications(): void
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $preferences = $user->getNotificationPreferences();
            $preferences['realtime_notifications'] = $this->inAppNotifications;
            $user->setNotificationPreferences($preferences);

            // Announce success to screen readers
            $this->dispatch('preferences-updated', message: __('profile.inapp_notifications_updated'));
        } catch (\Exception $e) {
            Log::error('In-app notifications update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Request data correction for read-only field
     * Creates a Helpdesk ticket with "Profile Data Correction" category
     *
     * @task 4.1.4
     */
    public function requestCorrection(string $field): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get current value for the field
        $currentValue = match ($field) {
            'email' => $user->email,
            'staff_id' => $user->staff_id ?? 'N/A',
            'grade' => $this->grade ?? 'N/A',
            'department' => $this->division ?? 'N/A',
            'division' => $this->division ?? 'N/A',
            'position' => $this->position ?? 'N/A',
            default => 'N/A',
        };

        // Redirect to helpdesk form with pre-filled data
        $this->redirect(route('helpdesk.create', [
            'category' => 'profile_data_correction',
            'prefill_title' => __('profile.correction_request_title', ['field' => __("profile.{$field}")]),
            'prefill_description' => __('profile.correction_request_desc', [
                'field' => __("profile.{$field}"),
                'current_value' => $currentValue,
            ]),
        ]));
    }

    /**
     * Upload and update profile picture
     */
    public function updateProfilePicture(): void
    {
        $this->profilePictureUpdateSuccess = false;
        $this->profilePictureError = '';

        try {
            $this->validate([
                'profilePicture' => 'required|image|max:2048|mimes:jpeg,jpg,png,webp',
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            /** @var TemporaryUploadedFile $file */
            $file = $this->profilePicture;
            $path = $file->store('profile-pictures', 'public');

            // Update user profile_picture
            $user->update(['profile_picture' => $path]);

            // Update current profile picture
            $this->currentProfilePicture = $path;

            // Reset upload field
            $this->reset('profilePicture');

            $this->profilePictureUpdateSuccess = true;

            // Announce success to screen readers
            $this->dispatch('profile-picture-updated', message: __('profile.picture_updated'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire handles them
            throw $e;
        } catch (\Exception $e) {
            $this->profilePictureError = __('profile.picture_error');
            Log::error('Profile picture update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove profile picture
     */
    public function removeProfilePicture(): void
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Delete profile picture file if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Update user to remove profile_picture
            $user->update(['profile_picture' => null]);

            // Update current profile picture
            $this->currentProfilePicture = null;

            $this->profilePictureUpdateSuccess = true;

            // Announce success to screen readers
            $this->dispatch('profile-picture-removed', message: __('profile.picture_removed'));
        } catch (\Exception $e) {
            $this->profilePictureError = __('profile.picture_remove_error');
            Log::error('Profile picture removal failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(): void
    {
        $this->passwordUpdateSuccess = false;
        $this->passwordError = '';

        try {
            $validated = $this->validate([
                'current_password' => 'required|string|current_password',
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Clear password fields
            $this->reset(['current_password', 'password', 'password_confirmation']);

            $this->passwordUpdateSuccess = true;

            // Announce success to screen readers
            $this->dispatch('password-updated', message: __('profile.password_updated'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire handles them
            throw $e;
        } catch (\Exception $e) {
            $this->passwordError = __('profile.password_error');
            Log::error('Password update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Render component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        $view = view('livewire.staff.user-profile');
        assert($view instanceof View);

        return $view->layout('layouts.portal');
    }
}
