<?php

declare(strict_types=1);

// name: SecuritySettings
// description: Security settings component with password change, strength indicator, and session management
// author: dev-team@motac.gov.my
// trace: SRS-FR-005; D04 §3.3.3; D11 §6; Requirements 3.5
// last-updated: 2025-11-07

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SecuritySettings extends Component
{
    /**
     * Password fields
     */
    #[Validate('required|string|current_password')]
    public string $currentPassword = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $newPassword = '';

    #[Validate('required|string')]
    public string $newPasswordConfirmation = '';

    /**
     * Password strength score (0-100)
     */
    public int $passwordStrength = 0;

    /**
     * Password strength feedback
     */
    public string $strengthFeedback = '';

    /**
     * Success/error messages
     */
    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /**
     * Show/hide password visibility
     */
    public bool $showCurrentPassword = false;

    public bool $showNewPassword = false;

    /**
     * Last password change timestamp
     */
    public ?string $lastPasswordChange = null;

    /**
     * Active sessions count
     */
    public int $activeSessionsCount = 0;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->lastPasswordChange = $user->password_changed_at?->format('d/m/Y h:i A');
        // Note: Active sessions count requires session tracking implementation
        $this->activeSessionsCount = 1; // Placeholder
    }

    /**
     * Update password strength as user types
     */
    public function updatedNewPassword(): void
    {
        $this->calculatePasswordStrength($this->newPassword);
    }

    /**
     * Calculate password strength score
     */
    protected function calculatePasswordStrength(string $password): void
    {
        if (empty($password)) {
            $this->passwordStrength = 0;
            $this->strengthFeedback = '';

            return;
        }

        $score = 0;
        $feedback = [];

        // Length check (up to 40 points)
        $length = strlen($password);
        if ($length >= 8) {
            $score += min(40, ($length - 8) * 2 + 20);
        } else {
            $feedback[] = __('portal.password_too_short');
        }

        // Character variety checks (15 points each)
        if (preg_match('/[a-z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = __('portal.password_needs_lowercase');
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = __('portal.password_needs_uppercase');
        }

        if (preg_match('/[0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = __('portal.password_needs_number');
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = __('portal.password_needs_special');
        }

        $this->passwordStrength = min(100, $score);

        // Set feedback based on score
        if ($this->passwordStrength >= 80) {
            $this->strengthFeedback = __('portal.password_strength_strong');
        } elseif ($this->passwordStrength >= 60) {
            $this->strengthFeedback = __('portal.password_strength_good');
        } elseif ($this->passwordStrength >= 40) {
            $this->strengthFeedback = __('portal.password_strength_fair');
        } else {
            $this->strengthFeedback = implode(', ', $feedback);
        }
    }

    /**
     * Get password strength color
     */
    #[Computed]
    public function strengthColor(): string
    {
        if ($this->passwordStrength >= 80) {
            return 'bg-green-500';
        }
        if ($this->passwordStrength >= 60) {
            return 'bg-blue-500';
        }
        if ($this->passwordStrength >= 40) {
            return 'bg-yellow-500';
        }

        return 'bg-red-500';
    }

    /**
     * Get password strength text color
     */
    #[Computed]
    public function strengthTextColor(): string
    {
        if ($this->passwordStrength >= 80) {
            return 'text-green-600 dark:text-green-400';
        }
        if ($this->passwordStrength >= 60) {
            return 'text-blue-600 dark:text-blue-400';
        }
        if ($this->passwordStrength >= 40) {
            return 'text-yellow-600 dark:text-yellow-400';
        }

        return 'text-red-600 dark:text-red-400';
    }

    /**
     * Change password
     */
    public function changePassword(): void
    {
        $this->clearMessages();

        // Validate all fields
        $this->validate([
            'currentPassword' => ['required', 'string', 'current_password'],
            'newPassword' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        try {
            $user = Auth::user();
            /** @var \App\Models\User $user */

            // Update password
            $user->password = Hash::make($this->newPassword);
            $user->password_changed_at = now();
            $user->save();

            // Clear form
            $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'passwordStrength', 'strengthFeedback']);
            $this->lastPasswordChange = $user->password_changed_at->format('d/m/Y h:i A');

            $this->successMessage = __('portal.password_changed_successfully');
            $this->dispatch('password-changed');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.password_change_failed');
            logger()->error('Password change failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle current password visibility
     */
    public function toggleCurrentPasswordVisibility(): void
    {
        $this->showCurrentPassword = ! $this->showCurrentPassword;
    }

    /**
     * Toggle new password visibility
     */
    public function toggleNewPasswordVisibility(): void
    {
        $this->showNewPassword = ! $this->showNewPassword;
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
     * Render the component
     */
    public function render(): mixed
    {
        return view('livewire.security-settings');
    }
}
