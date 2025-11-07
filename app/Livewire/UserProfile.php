<?php

declare(strict_types=1);

// name: UserProfile
// description: User profile management component with completeness indicator, editable fields with validation, and read-only fields
// author: dev-team@motac.gov.my
// trace: SRS-FR-003; D04 §3.3; D11 §6; Requirements 3.1, 3.4
// last-updated: 2025-11-07

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;

    /**
     * Editable profile fields
     */
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public ?string $phone = null;

    #[Validate('nullable|string|max:20')]
    public ?string $mobile = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $bio = null;

    #[Validate('nullable|image|max:2048')]
    public $newAvatar = null;

    /**
     * Read-only fields (displayed but not editable)
     */
    public string $staffId = '';

    public string $division = '';

    public string $grade = '';

    public string $position = '';

    public string $role = '';

    public bool $isActive = false;

    public ?string $lastLoginAt = null;

    /**
     * Current avatar path
     */
    public ?string $currentAvatar = null;

    /**
     * Success/error messages
     */
    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        $user = Auth::user();

        // Editable fields
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->mobile = $user->mobile;
        $this->bio = $user->bio;
        $this->currentAvatar = $user->avatar;

        // Read-only fields
        $this->staffId = $user->staff_id ?? '-';
        $this->division = $user->division?->name ?? '-';
        $this->grade = $user->grade?->name ?? '-';
        $this->position = $user->position?->name ?? '-';
        $this->role = ucfirst($user->role ?? 'staff');
        $this->isActive = $user->is_active;
        $this->lastLoginAt = $user->last_login_at?->format('d/m/Y h:i A');
    }

    /**
     * Get profile completeness percentage
     */
    #[Computed]
    public function profileCompleteness(): int
    {
        return Auth::user()->profile_completeness ?? 0;
    }

    /**
     * Get missing profile fields
     */
    #[Computed]
    public function missingFields(): array
    {
        $user = Auth::user();
        $missing = [];

        if (empty($user->phone) && empty($user->mobile)) {
            $missing[] = __('portal.contact_number');
        }
        if (empty($user->bio)) {
            $missing[] = __('portal.bio');
        }
        if (empty($user->avatar)) {
            $missing[] = __('portal.profile_photo');
        }

        return $missing;
    }

    /**
     * Update profile
     */
    public function updateProfile(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        // Validate all fields
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'newAvatar' => ['nullable', 'image', 'max:2048'],
        ]);

        try {
            $user = Auth::user();
            /** @var \App\Models\User $user */

            // Update fields
            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone = $this->phone;
            $user->mobile = $this->mobile;
            $user->bio = $this->bio;

            // Handle avatar upload
            if ($this->newAvatar) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar
                $path = $this->newAvatar->store('avatars', 'public');
                $user->avatar = $path;
                $this->currentAvatar = $path;
                $this->newAvatar = null;
            }

            $user->save();

            $this->successMessage = __('portal.profile_updated_successfully');

            // Refresh profile completeness
            $this->dispatch('profile-updated');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.profile_update_failed');
            logger()->error('Profile update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(): void
    {
        try {
            $user = Auth::user();
            /** @var \App\Models\User $user */
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = null;
            $user->save();

            $this->currentAvatar = null;
            $this->successMessage = __('portal.avatar_removed_successfully');

            $this->dispatch('profile-updated');
        } catch (\Exception $e) {
            $this->errorMessage = __('portal.avatar_removal_failed');
        }
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
        return view('livewire.user-profile');
    }
}
