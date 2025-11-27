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

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.portal')] class extends Component
{
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
        $this->language = session('locale', config('app.locale', 'ms'));
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
        $currentValue = match($field) {
            'email' => $user->email,
            'staff_id' => $user->staff_id,
            'grade' => $user->grade?->name_ms ?? $user->grade?->name_en ?? '-',
            'department' => $user->division?->name_ms ?? $user->division?->name_en ?? '-',
            default => '-',
        };

        // Redirect to helpdesk form with pre-filled data
        $this->redirect(route('helpdesk.create', [
            'category' => 'profile_data_correction',
            'prefill_title' => __('profile.correction_request_title', ['field' => __("profile.{$field}")]),
            'prefill_description' => __('profile.correction_request_desc', [
                'field' => __("profile.{$field}"),
                'current_value' => $currentValue,
            ]),
        ]));
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
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                    <button wire:click="requestCorrection('email')"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline">
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
                    <button wire:click="requestCorrection('staff_id')"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline">
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
                    <button wire:click="requestCorrection('grade')"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline">
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
                    <button wire:click="requestCorrection('department')"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 underline">
                        {{ __('profile.request_correction') }}
                    </button>
                </div>
            </div>
        </div>


        {{-- Editable Profile Information --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                        {{ __('profile.name') }} <span class="text-danger-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.phone') }}
                    </label>
                    <input type="tel" id="phone" wire:model="phone" placeholder="+60 3-XXXX XXXX"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mobile --}}
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.mobile') }}
                    </label>
                    <input type="tel" id="mobile" wire:model="mobile" placeholder="+60 1X-XXX XXXX"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    @error('mobile')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.bio') }}
                    </label>
                    <textarea id="bio" wire:model="bio" rows="3" maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        placeholder="{{ __('profile.bio_placeholder') }}"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ strlen($bio) }}/500 {{ __('profile.characters') }}
                    </p>
                    @error('bio')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateProfile">{{ __('profile.save_changes') }}</span>
                        <span wire:loading wire:target="updateProfile">{{ __('profile.saving') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Password Change --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                        {{ __('profile.current_password') }} <span class="text-danger-500">*</span>
                    </label>
                    <input type="password" id="current_password" wire:model="current_password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        required>
                    @error('current_password')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.new_password') }} <span class="text-danger-500">*</span>
                    </label>
                    <input type="password" id="password" wire:model="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        required>
                    @error('password')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('profile.confirm_password') }} <span class="text-danger-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        required>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="updatePassword">{{ __('profile.update_password') }}</span>
                        <span wire:loading wire:target="updatePassword">{{ __('profile.updating') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Language Preference --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="language" value="en"
                            class="form-radio text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">English</span>
                    </label>
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="updateLanguage"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="updateLanguage">{{ __('profile.save_language') }}</span>
                        <span wire:loading wire:target="updateLanguage">{{ __('profile.saving') }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Notification Preferences --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                    <button wire:click="updateNotificationPreferences"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="updateNotificationPreferences">{{ __('profile.save_preferences') }}</span>
                        <span wire:loading
                            wire:target="updateNotificationPreferences">{{ __('profile.saving') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
