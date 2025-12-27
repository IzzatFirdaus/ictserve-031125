<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name = '';

    public ?string $phone = null;

    public bool $showSuccessMessage = false;

    /** @var TemporaryUploadedFile|null */
    public $profilePicture = null;

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['nullable', 'regex:/^[0-9]{10,11}$/'],
    ];

    public function mount(): void
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        $this->name = (string) $user->name;
        $this->phone = $user->phone;
    }

    public function updateProfile(): void
    {
        $data = $this->validate();
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        $user->update($data);
        // Auditable trait on User should create audit automatically.
        $this->showSuccessMessage = true;
        $this->dispatch('profile-updated');
    }

    public function updateProfilePicture(): void
    {
        $this->validate([
            'profilePicture' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif'],
        ]);

        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $this->profilePicture->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        $this->profilePicture = null;
        $this->showSuccessMessage = true;
        $this->dispatch('profile-updated');
    }

    public function removeProfilePicture(): void
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        $this->showSuccessMessage = true;
        $this->dispatch('profile-updated');
    }

    public function hideSuccessMessage(): void
    {
        $this->showSuccessMessage = false;
    }

    #[Computed]
    public function profileCompleteness(): int
    {
        // Simple percentage: name + phone filled counts; email assumed always filled.
        $total = 3; // name, phone, email
        $score = 0;
        if ($this->name !== '') {
            $score++;
        }
        if (! empty($this->phone)) {
            $score++;
        }
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        if ($user?->email) {
            $score++;
        }

        return (int) floor(($score / $total) * 100);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.portal.user-profile');
    }
}
