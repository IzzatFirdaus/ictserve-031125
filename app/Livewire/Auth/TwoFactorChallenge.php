<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallenge extends Component
{
    public string $code = '';
    public string $recoveryCode = '';
    public bool $usingRecoveryCode = false;

    public function mount()
    {
        if (! Auth::user()->two_factor_confirmed_at) {
            return redirect()->intended(route('dashboard'));
        }
    }

    public function confirm()
    {
        $user = Auth::user();

        if ($this->usingRecoveryCode) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            if (($key = array_search($this->recoveryCode, $recoveryCodes)) !== false) {
                unset($recoveryCodes[$key]);

                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
                ])->save();

                request()->session()->put('2fa_verified', true);

                return redirect()->intended(route('dashboard'));
            }

            throw ValidationException::withMessages([
                'recoveryCode' => __('The provided recovery code was invalid.'),
            ]);
        }

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->two_factor_secret, $this->code)) {
            request()->session()->put('2fa_verified', true);

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'code' => __('The provided two factor authentication code was invalid.'),
        ]);
    }

    public function toggleRecovery()
    {
        $this->usingRecoveryCode = ! $this->usingRecoveryCode;
        $this->code = '';
        $this->recoveryCode = '';
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }
}
