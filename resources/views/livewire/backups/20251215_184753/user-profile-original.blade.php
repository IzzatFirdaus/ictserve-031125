{{--
/**
 * Component name: User Profile Management (Volt)
 * Description: Profile management interface with editable and read-only fields
 * Implements Task 4.1.2: Create profile management interface
 *
 * @author Pasukan BPM MOTAC
 * @trace R10 (Authenticated Portal), R25 (Profile Data Management)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @version 1.0.0
 * @task 4.1.2
 */
--}}
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.portal')] class extends Component {
    // Editable fields
    public string $name = '';
    public string $phone = '';
    public string $mobile = '';
    public string $bio = '';

    // Password change fields
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Notification preferences
    public array $notification_preferences = [];

    // Language preference
    public string $language = 'ms';

    /**
     * Mount the component with user data
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->mobile = $user->mobile ?? '';
        $this->bio = $user->bio ?? '';
        $this->notification_preferences = $user->getNotificationPreferences();
        $this->language = 'ms';
    }

    /**
     * Update profile information (editable fields only)
     */
    public function updateProfile(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        Auth::user()->update($validated);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('profile.updated_successfully'),
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
            'require_password_change' => false,
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('profile.password_updated'),
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(): void
    {
        Auth::user()->setNotificationPreferences($this->notification_preferences);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('profile.preferences_updated'),
        ]);
    }

    /**
     * Update language preference
     */
    public function updateLanguage(): void
    {
        $this->language = 'ms';

        session(['locale' => $this->language]);
        cookie()->queue('locale', $this->language, 60 * 24 * 365);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('profile.language_updated'),
        ]);

        // Refresh page to apply language change
        $this->redirect(request()->header('Referer', route('portal.profile')));
    }

    /**
     * Request data correction for read-only field
     */
    public function requestCorrection(string $field): void
    {
        $user = Auth::user();
        $currentValue = match ($field) {
            'email' => $user->email,
            'staff_id' => $user->staff_id,
            'grade' => $user->grade?->name_ms ?? ($user->grade?->name_en ?? '-'),
            'department' => $user->division?->name_ms ?? ($user->division?->name_en ?? '-'),
            default => '-',
        };

        // Redirect to helpdesk form with pre-filled data
        $this->redirect(
            route('helpdesk.create', [
                'category' => 'profile_data_correction',
                'prefill_title' => __('profile.correction_request_title', ['field' => __("profile.{$field}")]),
                'prefill_description' => __('profile.correction_request_desc', [
                    'field' => __("profile.{$field}"),
                    'current_value' => $currentValue,
                ]),
            ]),
        );
    }
}; ?>

<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('profile.title') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('profile.subtitle') }}
        </p>

        {{-- Profile Completeness --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">{{ __('profile.completeness') }}</span>
                <span
                    class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->profile_completeness }}%</span>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                <div class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ auth()->user()->profile_completeness }}%"></div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        {{-- Read-Only System Information --}}
        <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg theme-transition overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium font-heading text-gray-900 dark:text-white">
                    {{ __('profile.system_information') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('profile.system_information_desc') }}
                </p>
            </div>

            <div class="px-6 py-4 space-y-4">
                {{-- Email (Read-Only) --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('profile.email') }}
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="text-gray-900 dark:text-white">{{ auth()->user()->email }}</span>
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ __('profile.read_only') }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="requestCorrection('email')" type="button"
                        class="min-h-11 min-w-11 px-3 py-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline underline-offset-2 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 rounded-md transition-colors duration-200"
                        aria-label="{{ __('profile.request_correction_for', ['field' => __('profile.email')]) }}">
                        {{ __('profile.request_correction') }}
                    </button>
                </div>

                {{-- Staff ID (Read-Only) --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('profile.staff_id') }}
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="text-gray-900 dark:text-white">{{ auth()->user()->staff_id ?? '-' }}</span>
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ __('profile.read_only') }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="requestCorrection('staff_id')" type="button"
                        class="min-h-11 min-w-11 px-3 py-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline underline-offset-2 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 rounded-md transition-colors duration-200"
                        aria-label="{{ __('profile.request_correction_for', ['field' => __('profile.staff_id')]) }}">
                        {{ __('profile.request_correction') }}
                    </button>
                </div>

                {{-- Grade (Read-Only) --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('profile.grade') }}
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="text-gray-900 dark:text-white">
                                {{ auth()->user()->grade?->{'name_' . app()->getLocale()} ?? (auth()->user()->grade?->name_ms ?? '-') }}
                            </span>
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ __('profile.read_only') }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="requestCorrection('grade')" type="button"
                        class="min-h-11 min-w-11 px-3 py-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline underline-offset-2 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 rounded-md transition-colors duration-200"
                        aria-label="{{ __('profile.request_correction_for', ['field' => __('profile.grade')]) }}">
                        {{ __('profile.request_correction') }}
                    </button>
                </div>

                {{-- Department/Division (Read-Only) --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('profile.department') }}
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="text-gray-900 dark:text-white">
                                {{ auth()->user()->division?->{'name_' . app()->getLocale()} ?? (auth()->user()->division?->name_ms ?? '-') }}
                            </span>
                            <span
                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ __('profile.read_only') }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="requestCorrection('department')" type="button"
                        class="min-h-11 min-w-11 px-3 py-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline underline-offset-2 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 rounded-md transition-colors duration-200"
                        aria-label="{{ __('profile.request_correction_for', ['field' => __('profile.department')]) }}">
                        {{ __('profile.request_correction') }}
                    </button>
                </div>
            </div>
        </div>


        {{-- Editable Profile Information --}}
        <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg theme-transition overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium font-heading text-gray-900 dark:text-white">
                    {{ __('profile.personal_information') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('profile.personal_information_desc') }}
                </p>
            </div>

            <form wire:submit="updateProfile" class="px-6 py-4 space-y-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.name') }} <span class="text-danger-500" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-required="true" aria-describedby="name-error" @error('name') aria-invalid="true" @enderror
                        required>
                    @error('name')
                        <p id="name-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.phone') }}
                    </label>
                    <input type="tel" id="phone" wire:model="phone" placeholder="+60 3-XXXX XXXX"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-describedby="phone-hint phone-error">
                    <p id="phone-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('profile.phone_format_hint') ?: 'Format: +60 3-XXXX XXXX' }}</p>
                    @error('phone')
                        <p id="phone-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Mobile --}}
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.mobile') }}
                    </label>
                    <input type="tel" id="mobile" wire:model="mobile" placeholder="+60 1X-XXX XXXX"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-describedby="mobile-hint mobile-error">
                    <p id="mobile-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('profile.mobile_format_hint') ?: 'Format: +60 1X-XXX XXXX' }}</p>
                    @error('mobile')
                        <p id="mobile-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.bio') }}
                    </label>
                    <textarea id="bio" wire:model="bio" rows="3" maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-describedby="bio-hint bio-error" placeholder="{{ __('profile.bio_placeholder') }}"></textarea>
                    <p id="bio-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ strlen($bio ?? '') }}/500 {{ __('profile.characters') }}
                    </p>
                    @error('bio')
                        <p id="bio-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center min-h-11 px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-button text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 transition-colors duration-200"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove wire:target="updateProfile">{{ __('profile.save_changes') }}</span>
                        <span wire:loading wire:target="updateProfile" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('profile.saving') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Password Change --}}
        <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg theme-transition overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium font-heading text-gray-900 dark:text-white">
                    {{ __('profile.change_password') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('profile.change_password_desc') }}
                </p>
            </div>

            <form wire:submit="updatePassword" class="px-6 py-4 space-y-4">
                {{-- Current Password --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.current_password') }} <span class="text-danger-500"
                            aria-hidden="true">*</span>
                    </label>
                    <input type="password" id="current_password" wire:model="current_password"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-required="true" aria-describedby="current-password-error"
                        @error('current_password') aria-invalid="true" @enderror autocomplete="current-password"
                        required>
                    @error('current_password')
                        <p id="current-password-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400"
                            role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.new_password') }} <span class="text-danger-500" aria-hidden="true">*</span>
                    </label>
                    <input type="password" id="password" wire:model="password"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-required="true" aria-describedby="password-hint password-error"
                        @error('password') aria-invalid="true" @enderror autocomplete="new-password" required>
                    <p id="password-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('profile.password_requirements') ?: 'Minimum 8 characters with uppercase, lowercase, and number' }}
                    </p>
                    @error('password')
                        <p id="password-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.confirm_password') }} <span class="text-danger-500"
                            aria-hidden="true">*</span>
                    </label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation"
                        class="mt-1 block w-full min-h-11 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        aria-required="true" autocomplete="new-password" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center min-h-11 px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-button text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 transition-colors duration-200"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove
                            wire:target="updatePassword">{{ __('profile.update_password') }}</span>
                        <span wire:loading wire:target="updatePassword" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('profile.updating') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Language Preference --}}
        <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg theme-transition overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium font-heading text-gray-900 dark:text-white">
                    {{ __('profile.language_preference') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('profile.language_preference_desc') }}
                </p>
            </div>

            <div class="px-6 py-4">
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="language" value="ms"
                            class="form-radio text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Bahasa Melayu</span>
                    </label>
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="updateLanguage" type="button"
                        class="inline-flex items-center min-h-11 px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-button text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 transition-colors duration-200"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove
                            wire:target="updateLanguage">{{ __('profile.save_language') }}</span>
                        <span wire:loading wire:target="updateLanguage" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('profile.saving') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Notification Preferences --}}
        <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg theme-transition overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium font-heading text-gray-900 dark:text-white">
                    {{ __('profile.notification_preferences') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('profile.notification_preferences_desc') }}
                </p>
            </div>

            <div class="px-6 py-4 space-y-4">
                @foreach ([
        'ticket_updates' => __('profile.notif_ticket_updates'),
        'ticket_assignments' => __('profile.notif_ticket_assignments'),
        'ticket_comments' => __('profile.notif_ticket_comments'),
        'sla_alerts' => __('profile.notif_sla_alerts'),
        'loan_updates' => __('profile.notif_loan_updates'),
        'loan_approvals' => __('profile.notif_loan_approvals'),
        'loan_reminders' => __('profile.notif_loan_reminders'),
        'system_announcements' => __('profile.notif_system_announcements'),
    ] as $key => $label)
                    <label class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        <input type="checkbox" wire:model="notification_preferences.{{ $key }}"
                            class="form-checkbox h-5 w-5 text-primary-600 rounded focus:ring-primary-500">
                    </label>
                @endforeach

                <div class="pt-4 flex justify-end">
                    <button wire:click="updateNotificationPreferences" type="button"
                        class="inline-flex items-center min-h-11 px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-button text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 transition-colors duration-200"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove
                            wire:target="updateNotificationPreferences">{{ __('profile.save_preferences') }}</span>
                        <span wire:loading wire:target="updateNotificationPreferences"
                            class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('profile.saving') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
