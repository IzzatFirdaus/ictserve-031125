{{--
/**
 * Pages - Auth - Confirm Password Livewire View
 *
 * Livewire view template with reactive properties
 *
 * @trace D03-FR-001.1
 * @trace D03-FR-022.1
 * @trace D04 §6.1
 * @trace D10 §7
 * @trace D12 §9
 * @trace D14 §8
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 * @updated 2025-11-03
 */
--}}
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required aria-required="true"', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        Ini adalah kawasan selamat aplikasi. Sila sahkan kata laluan anda sebelum meneruskan.
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Laluan" />

            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500"
                          type="password"
                          name="password"
                          required aria-required="true" autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="min-h-11 px-6 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 transition-colors">
                Sahkan
            </x-primary-button>
        </div>
    </form>
</div>
