{{--
/**
 * Component name: Unified Login Page (Volt)
 * Description: Single unified authentication page for all user roles with role-based redirect
 *
 * Implements Task 14.1 - Flexible Login Implementation:
 * - Accept full email (user@motac.gov.my) OR short username (user)
 * - Updated field labels and placeholders for flexible login
 * - Generic error messages (no user enumeration)
 *
 * Implements Task 4.0.1-4.0.4 from updated-frontend spec:
 * - Merged Admin and Staff login views into single interface
 * - Language switcher visible on login screen (via guest layout)
 * - Standardized styling with consistent field spacing and responsive behavior
 * - Role-based redirect after authentication (Admin → Filament, Staff → Portal)
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1 (Authentication), R10 (Authenticated Portal), R22 (Unified Authentication)
 * @trace D03 SRS-AUTH-001 (Flexible Login - Requirements 16.2, 16.3, 16.5)
 * @trace D04 §5.2 (Security)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @version 2.1.0
 * @task 4.0.1, 4.0.3, 4.0.4, 14.1
 */
--}}

<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     *
     * Task 4.0.4: Role-Based Redirect
     * - Admin/Superuser → Filament Admin Dashboard
     * - Staff/Approver → Portal Dashboard
     *
     * Task 14.1: Flexible Login
     * - LoginForm handles email OR username authentication
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();

        // Task 4.0.4: Detect user role and redirect to appropriate dashboard
        if ($user && ($user->hasAdminAccess() || $user->hasAnyRole(['admin', 'superuser']))) {
            $this->redirectIntended(default: route('filament.admin.pages.admin-dashboard', absolute: false), navigate: true);

            return;
        }

        // Default redirect for Staff and Approvers
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">
        {{ __('auth.login_title') }}
    </h1>

    <p class="text-center text-gray-600 dark:text-gray-400 mb-8">
        {{ __('auth.login_subtitle') }}
    </p>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        {{-- Email/Username Field (Task 14.1: Flexible Login) --}}
        <div>
            <x-input-label for="email" :value="__('auth.email_or_username')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input wire:model="form.email" id="email"
                class="block mt-2 w-full min-h-[48px] px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                type="text" name="email" required autofocus autocomplete="username"
                placeholder="{{ __('auth.email_or_username_placeholder') }}"
                aria-describedby="login-hint" />
            {{-- Hint text for flexible login (Task 14.1) --}}
            <p id="login-hint" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('auth.flexible_login_hint') }}
            </p>
            @error('form.email')
                <x-input-error :messages="$message" class="mt-2" />
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <x-input-label for="password" :value="__('auth.password')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input wire:model="form.password" id="password"
                class="block mt-2 w-full min-h-[48px] px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                type="password" name="password" required autocomplete="current-password"
                placeholder="{{ __('auth.password_placeholder') }}" />
            @error('form.password')
                <x-input-error :messages="$message" class="mt-2" />
            @enderror
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('auth.remember_me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 rounded"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('auth.forgot_password') }}
                </a>
            @endif
        </div>

        {{-- Submit Button --}}
        <div>
            <button type="submit"
                class="w-full min-h-[48px] px-6 py-3 text-base font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-lg transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('auth.login_button') }}</span>
                <span wire:loading class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ __('auth.logging_in') }}
                </span>
            </button>
        </div>
    </form>
</div>
