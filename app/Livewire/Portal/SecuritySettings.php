<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class SecuritySettings extends Component
{
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public string $newPassword_confirmation = '';
    public int $passwordStrength = 0;

    protected function rules(): array
    {
        return [
            'currentPassword' => ['required'],
            'newPassword' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'confirmed',
                'same:newPasswordConfirmation',
            ],
            'newPasswordConfirmation' => ['required'],
        ];
    }

    public function updatedNewPassword(): void
    {
        $this->passwordStrength = $this->calculatePasswordStrength($this->newPassword);
    }

    protected function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        if (strlen($password) >= 8) { $score += 20; }
        if (preg_match('/[A-Z]/', $password)) { $score += 20; }
        if (preg_match('/[a-z]/', $password)) { $score += 20; }
        if (preg_match('/[0-9]/', $password)) { $score += 20; }
        if (preg_match('/[^A-Za-z0-9]/', $password)) { $score += 20; }
        return $score; // 0-100
    }

    public function changePassword(): void
    {
        $this->validate();
        $user = Auth::user();
        if (!$user) { return; }
        if (!Hash::check($this->currentPassword, (string) $user->password)) {
            $this->addError('currentPassword', 'Invalid current password');
            return;
        }
        $user->update(['password' => Hash::make($this->newPassword)]);
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->passwordStrength = 0;
    }

    public function updatedNewPasswordConfirmation(): void
    {
        // Mirror property for Laravel "confirmed" rule compatibility
        $this->newPassword_confirmation = $this->newPasswordConfirmation;
    }

    public function render()
    {
        return view('livewire.portal.security-settings');
    }
}
