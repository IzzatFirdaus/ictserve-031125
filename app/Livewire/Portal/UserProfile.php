<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserProfile extends Component
{
    public $name = '';
    public ?string $phone = null;

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
        $this->dispatch('profile-updated');
    }

    public function getProfileCompletenessProperty(): int
    {
        // Simple percentage: name + phone filled counts; email assumed always filled.
        $total = 3; // name, phone, email
        $score = 0;
        if ($this->name !== '') { $score++; }
        if (!empty($this->phone)) { $score++; }
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        if ($user?->email) { $score++; }
        return (int) floor(($score / $total) * 100);
    }

    public function render()
    {
        return view('livewire.portal.user-profile');
    }
}
