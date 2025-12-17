{{--
/**
 * Component name: Register Page (Volt)
 * Description: WCAG 2.2 AA compliant bilingual registration form with @motac.gov.my validation
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.2 (User Registration)
 * @trace D04 §5.2 (Security)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace Requirements 15.1, 15.2 (Self-Registration)
 * @version 2.0.0
 * @created 2025-11-03
 * @updated 2025-12-02 - Task 13.1: Enhanced with email domain validation, password strength indicator
 */
--}}

<?php

use App\Contracts\RegistrationServiceInterface;
use App\Exceptions\InvalidEmailDomainException;
use App\Rules\MotacEmailDomain;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate(['required', 'string', 'email', 'max:255', 'unique:users,email', new MotacEmailDomain()])]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Password strength score (0-4)
     */
    public int $passwordStrength = 0;

    /**
     * Email domain validation status
     */
    public bool $emailDomainValid = false;

    /**
     * Form submission state
     */
    public bool $isSubmitting = false;

    /**
     * Password requirement checks
     */
    public array $passwordChecks = [
        'length' => false,
        'uppercase' => false,
        'lowercase' => false,
        'number' => false,
        'special' => false,
    ];

    /**
     * Update password strength when password changes
     */
    public function updatedPassword(string $value): void
    {
        $this->calculatePasswordStrength($value);
    }

    /**
     * Validate email domain when email changes
     */
    public function updatedEmail(string $value): void
    {
        $this->validateEmailDomain($value);
    }

    /**
     * Calculate password strength and update checks
     */
    private function calculatePasswordStrength(string $password): void
    {
        $this->passwordChecks = [
            'length' => strlen($password) >= 8,
            'uppercase' => (bool) preg_match('/[A-Z]/', $password),
            'lowercase' => (bool) preg_match('/[a-z]/', $password),
            'number' => (bool) preg_match('/[0-9]/', $password),
            'special' => (bool) preg_match('/[^A-Za-z0-9]/', $password),
        ];

        $this->passwordStrength = array_sum(array_map('intval', $this->passwordChecks));
    }

    /**
     * Validate email domain is @motac.gov.my
     */
    private function validateEmailDomain(string $email): void
    {
        $email = strtolower(trim($email));
        $this->emailDomainValid = str_ends_with($email, '@motac.gov.my');
    }

    /**
     * Get password strength label
     */
    public function getPasswordStrengthLabel(): string
    {
        return match ($this->passwordStrength) {
            0, 1 => __('auth.password_weak'),
            2 => __('auth.password_fair'),
            3 => __('auth.password_good'),
            4, 5 => __('auth.password_strong'),
            default => __('auth.password_weak'),
        };
    }

    /**
     * Get password strength color class
     */
    public function getPasswordStrengthColor(): string
    {
        return match ($this->passwordStrength) {
            0, 1 => 'bg-red-500',
            2 => 'bg-yellow-500',
            3 => 'bg-blue-500',
            4, 5 => 'bg-green-500',
            default => 'bg-gray-300',
        };
    }

    /**
     * Handle registration request using RegistrationService
     */
    public function register(RegistrationServiceInterface $registrationService): void
    {
        // Set submitting state
        $this->isSubmitting = true;

        // Validate form inputs
        $this->validate();

        // Additional email domain validation
        if (!$this->emailDomainValid) {
            $this->addError('email', __('auth.email_domain_error'));
            $this->isSubmitting = false;
            return;
        }

        try {
            // Use RegistrationService for registration
            $registrationService->register([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            // Redirect to email verification page
            $this->redirect(route('verification.notice'), navigate: true);

        } catch (InvalidEmailDomainException $e) {
            $this->addError('email', __('auth.email_domain_error'));
            $this->isSubmitting = false;
            Log::warning('Registration attempt with invalid email domain', [
                'email' => $this->email,
                'ip' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            $this->addError('email', __('auth.registration_failed'));
            $this->isSubmitting = false;
            Log::error('Registration failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}; ?>

<div>
    {{-- Page Header (MyDS Typography - D13 §2.4) --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold font-heading text-gray-900 dark:text-white mb-4">
            {{ __('auth.register_title') }}
        </h1>
        <p class="text-base text-gray-600 dark:text-gray-400 font-body leading-relaxed max-w-md mx-auto">
            {{ __('auth.register_subtitle') }}
        </p>
    </div>

    <form wire:submit="register" aria-label="{{ __('auth.register_title') }}">
        {{-- Name Field (MyDS Form Components - D13 §2.7) --}}
        <div class="space-y-2">
            <x-input-label for="name" :value="__('auth.name')" class="text-gray-900 dark:text-white font-medium font-body" />
            <x-text-input
                wire:model.live.debounce.300ms="name"
                id="name"
                class="block w-full min-h-11 px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-body transition-colors duration-200"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('auth.name_placeholder') }}"
                aria-describedby="name-error"
            />
            @error('name')
                <x-input-error :messages="$message" class="mt-1" id="name-error" />
            @enderror
        </div>

        {{-- Email Field with Domain Hint --}}
        <div class="mt-6 space-y-2">
            <x-input-label for="email" :value="__('auth.email')" class="text-gray-900 dark:text-white font-medium font-body" />
            <x-text-input
                wire:model.live.debounce.300ms="email"
                id="email"
                class="block w-full min-h-11 px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-body transition-colors duration-200"
                type="email"
                name="email"
                required
                autocomplete="username"
                placeholder="{{ __('auth.email_placeholder') }}"
                aria-describedby="email-hint email-error"
            />
            {{-- Email Domain Hint --}}
            <p id="email-hint" class="mt-2 text-sm font-body {{ $emailDomainValid ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                @if($email && $emailDomainValid)
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('auth.email_domain_hint') }}
                    </span>
                @elseif($email && !$emailDomainValid)
                    <span class="flex items-center gap-2 text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        {{ __('auth.email_domain_error') }}
                    </span>
                @else
                    {{ __('auth.email_domain_hint') }}
                @endif
            </p>
            @error('email')
                <x-input-error :messages="$message" class="mt-1" id="email-error" />
            @enderror
        </div>

        {{-- Password Field with Strength Indicator --}}
        <div class="mt-6 space-y-2">
            <x-input-label for="password" :value="__('common.password')" class="text-gray-900 dark:text-white font-medium font-body" />
            <x-text-input
                wire:model.live.debounce.150ms="password"
                id="password"
                class="block w-full min-h-11 px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-body transition-colors duration-200"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('auth.password_placeholder') }}"
                aria-describedby="password-requirements password-strength password-error"
            />

            {{-- Password Strength Indicator (MyDS Progress - D13 §2.7) --}}
            @if($password)
                <div class="mt-3" id="password-strength" role="status" aria-live="polite">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400 font-body">{{ __('auth.password_strength') }}:</span>
                        <span class="text-sm font-medium font-body {{ $passwordStrength >= 4 ? 'text-green-600 dark:text-green-400' : ($passwordStrength >= 3 ? 'text-blue-600 dark:text-blue-400' : ($passwordStrength >= 2 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400')) }}">
                            {{ $this->getPasswordStrengthLabel() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2" role="progressbar" aria-valuenow="{{ $passwordStrength * 20 }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="{{ $this->getPasswordStrengthColor() }} h-2 rounded-full transition-all duration-300" style="width: {{ $passwordStrength * 20 }}%"></div>
                    </div>
                </div>

                {{-- Password Requirements Checklist (MyDS Card - D13 §2.7) --}}
                <div id="password-requirements" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-medium font-body text-gray-700 dark:text-gray-300 mb-3">{{ __('auth.password_requirements') }}:</p>
                    <ul class="space-y-2 text-sm font-body">
                        <li class="flex items-center gap-2 {{ $passwordChecks['length'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @if($passwordChecks['length'])
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ __('auth.password_min_length') }}
                        </li>
                        <li class="flex items-center gap-2 {{ $passwordChecks['uppercase'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @if($passwordChecks['uppercase'])
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ __('auth.password_uppercase') }}
                        </li>
                        <li class="flex items-center gap-2 {{ $passwordChecks['lowercase'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @if($passwordChecks['lowercase'])
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ __('auth.password_lowercase') }}
                        </li>
                        <li class="flex items-center gap-2 {{ $passwordChecks['number'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @if($passwordChecks['number'])
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ __('auth.password_number') }}
                        </li>
                        <li class="flex items-center gap-2 {{ $passwordChecks['special'] ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @if($passwordChecks['special'])
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ __('auth.password_special') }}
                        </li>
                    </ul>
                </div>
            @endif

            @error('password')
                <x-input-error :messages="$message" class="mt-1" id="password-error" />
            @enderror
        </div>

        {{-- Confirm Password Field --}}
        <div class="mt-6 space-y-2">
            <x-input-label for="password_confirmation" :value="__('auth.confirm_password')" class="text-gray-900 dark:text-white font-medium font-body" />
            <x-text-input
                wire:model.live.debounce.300ms="password_confirmation"
                id="password_confirmation"
                class="block w-full min-h-11 px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-body transition-colors duration-200"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="{{ __('auth.confirm_password_placeholder') }}"
                aria-describedby="password-confirmation-error"
            />
            {{-- Password Match Indicator --}}
            @if($password_confirmation)
                <p class="mt-2 text-sm font-body {{ $password === $password_confirmation ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    @if($password === $password_confirmation)
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('validation.confirmed', ['attribute' => __('common.password')]) }}
                        </span>
                    @else
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"></svg>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            {{ __('validation.same', ['attribute' => __('common.password'), 'other' => __('auth.confirm_password')]) }}
                        </span>
                    @endif
                </p>
            @endif
            @error('password_confirmation')
                <x-input-error :messages="$message" class="mt-1" id="password-confirmation-error" />
            @enderror
        </div>

        {{-- Form Actions (MyDS Touch Targets - D13 §2.7) --}}
        <div class="flex items-center justify-between mt-8 pt-4">
            <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 min-h-11 inline-flex items-center px-2 font-body transition-colors duration-200"
               href="{{ route('login') }}"
               wire:navigate>
                {{ __('auth.already_registered') }}
            </a>

            <button type="submit"
                class="min-h-11 px-6 py-3 text-base font-medium font-body text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-md shadow-button transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary-600"
                @if($isSubmitting) disabled @endif>
                @if($isSubmitting)
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('common.processing') }}
                    </span>
                @else
                    {{ __('auth.register_button') }}
                @endif
            </button>
        </div>
    </form>
</div>
