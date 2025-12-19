{{--
/**
 * Component name: Update Password Form
 * Description: Volt component for securely updating user password with current password verification
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-011.5 (Profile Management)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
            {{ __('profile.password_title') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('profile.password_description') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="__('profile.current_password')" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500" autocomplete="current-password" :placeholder="__('profile.current_password_placeholder')" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('profile.new_password')" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500" autocomplete="new-password" :placeholder="__('profile.new_password_placeholder')" />
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                {{ __('profile.password_requirements') }}
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('profile.confirm_password')" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="min-h-11 px-6 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500">{{ __('profile.update_password') }}</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                {{ __('profile.password_updated') }}
            </x-action-message>
        </div>
    </form>
</section>

