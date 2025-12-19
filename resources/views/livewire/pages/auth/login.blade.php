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
     * Form submission state
     */
    public bool $isSubmitting = false;

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
        // Set submitting state
        $this->isSubmitting = true;

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
    {{-- Page Title (MyDS Typography - D13 §2.4) --}}
    <h1 class="text-3xl font-bold font-heading text-center text-slate-900 dark:text-white mb-6">
        {{ __('auth.login_title') }}
    </h1>

    <p class="text-center text-slate-600 dark:text-slate-400 mb-8 font-body leading-relaxed max-w-md mx-auto">
        {{ __('auth.login_subtitle') }}
    </p>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('sso_fallback'))
        <div class="mb-4 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-900" role="alert">
            <p class="font-semibold">{{ __('auth.google_sso_unavailable') }}</p>
            <p class="mt-1">{{ __('auth.sso_fallback_available') }}</p>
        </div>
    @endif

    <div class="space-y-6 mb-8">
        <x-auth.google-button />
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 font-body">
            <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
            <span class="uppercase tracking-wide font-medium px-3 bg-white dark:bg-slate-800 text-xs">{{ __('auth.or_separator') }}</span>
            <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
        </div>
    </div>

    <form wire:submit="login" class="space-y-6">
        {{-- Email/Username Field (MyDS Form Components - D13 §2.7) --}}
        <div class="space-y-2">
            <x-input-label for="email" :value="__('auth.email_or_username')" class="text-slate-900 dark:text-white font-medium font-body" />
            <x-text-input wire:model="form.email" id="email"
                class="block w-full min-h-11 px-4 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 font-body transition-colors duration-200"
                type="text" name="email" required autofocus autocomplete="username"
                placeholder="{{ __('auth.email_or_username_placeholder') }}"
                aria-describedby="login-hint" />
            {{-- Hint text for flexible login (Task 14.1) --}}
            <p id="login-hint" class="text-sm text-slate-600 dark:text-slate-400 font-body">
                {{ __('auth.flexible_login_hint') }}
            </p>
            @error('form.email')
                <x-input-error :messages="$message" class="mt-1" />
            @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-2">
            <x-input-label for="password" :value="__('auth.password')" class="text-slate-900 dark:text-white font-medium font-body" />
            <x-text-input wire:model="form.password" id="password"
                class="block w-full min-h-11 px-4 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 font-body transition-colors duration-200"
                type="password" name="password" required autocomplete="current-password"
                placeholder="{{ __('auth.password_placeholder') }}" />
            @error('form.password')
                <x-input-error :messages="$message" class="mt-1" />
            @enderror
        </div>

        {{-- Remember Me & Forgot Password (MyDS Touch Targets - D13 §2.7) --}}
        <div class="flex items-center justify-between pt-2">
            <label for="remember" class="inline-flex items-center cursor-pointer min-h-11 py-2 group">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-600 text-primary-600 shadow-sm focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-slate-800 transition-colors duration-200"
                    name="remember">
                <span class="ms-3 text-sm text-slate-600 dark:text-slate-400 font-body group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors duration-200">{{ __('auth.remember_me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-slate-800 rounded-lg min-h-11 inline-flex items-center px-2 font-body transition-colors duration-200"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('auth.forgot_password') }}
                </a>
            @endif
        </div>

        {{-- Submit Button (MyDS Button - D13 §2.7) --}}
        {{-- Loading state uses hidden class as default to prevent FOUC before Livewire hydrates --}}
        <div class="pt-4">
            <button type="submit"
                class="w-full min-h-11 px-6 py-3 text-base font-medium font-body text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 rounded-lg shadow-button transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary-600"
                wire:loading.attr="disabled"
                wire:target="login">
                {{-- Default state: visible, hidden during loading --}}
                <span wire:loading.remove wire:target="login" class="flex items-center justify-center">
                    {{ __('auth.login_button') }}
                </span>
                {{-- Loading state: hidden by default (prevents FOUC), shown during loading --}}
                <span wire:loading.flex wire:target="login" class="hidden items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" aria-hidden="true">
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
