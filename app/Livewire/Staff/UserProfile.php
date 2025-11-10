<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * UserProfile Component
 *
 * Provides user profile management interface for authenticated staff members.
 * Allows editing of profile information, notification preferences, and password changes.
 *
 * @author Frontend Engineering Team
 *
 * @trace D03-FR-020 (User Profile Management)
 * @trace D04 §5.3 (Authenticated Portal Design)
 * @trace D12 §4.2 (Profile Management UI)
 *
 * @version 1.0
 *
 * @wcag WCAG 2.2 Level AA
 */
class UserProfile extends Component
{
    use OptimizedLivewireComponent;

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
            \Log::error('Profile update failed', [
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
            \Log::error('Notification preferences update failed', [
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
            \Log::error('Password update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Render component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.staff.user-profile')
            ->layout('layouts.portal');
    }
}
