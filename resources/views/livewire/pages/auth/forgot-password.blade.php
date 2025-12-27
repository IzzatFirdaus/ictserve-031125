{{--
/**
 * Pages - Auth - Forgot Password Livewire View
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

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    {{-- Page Title (MyDS Typography - D13 §2.4) --}}
    <h1 class="text-3xl font-bold font-heading text-center text-slate-900 dark:text-white mb-6 theme-transition">
        {{ __('auth.forgot_password') }}
    </h1>

    <p
        class="text-center text-slate-600 dark:text-slate-300 mb-8 font-body leading-relaxed max-w-md mx-auto theme-transition">
        {{ __('auth.forgot_password_description') }}
    </p>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        {{-- Email Address (MyDS Form Components - D13 §2.7) --}}
        <div class="space-y-2">
            <x-input-label for="email" :value="__('auth.email')"
                class="text-slate-900 dark:text-white font-medium font-body theme-transition" />
            <x-text-input wire:model="email" id="email"
                class="block w-full min-h-11 px-4 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 font-body theme-transition"
                type="email" name="email" required aria-required="true" autofocus autocomplete="email"
                placeholder="{{ __('auth.email_placeholder') }}" aria-describedby="email-hint" />
            <p id="email-hint" class="text-sm text-slate-600 dark:text-slate-400 font-body theme-transition">
                {{ __('auth.email_hint_reset') }}
            </p>
            @error('email')
                <x-input-error :messages="$message" class="mt-1" />
            @enderror
        </div>

        {{-- Submit Button (MyDS Touch Targets - D13 §2.7) --}}
        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-slate-800 rounded-lg min-h-11 inline-flex items-center px-2 font-body theme-transition"
                href="{{ route('login') }}" wire:navigate>
                {{ __('auth.back_to_login') }}
            </a>

            <x-primary-button
                class="min-h-11 px-6 py-3 rounded-lg shadow-button hover:shadow-button-hover focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 font-medium font-body theme-transition">
                {{ __('auth.send_reset_link') }}
            </x-primary-button>
        </div>
    </form>
</div>
