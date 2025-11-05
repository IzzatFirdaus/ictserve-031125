{{--
/**
 * User Profile Page
 *
 * Provides comprehensive profile management interface for authenticated staff members.
 * Includes profile information editing, notification preferences, and password change.
 *
 * @component livewire.staff.user-profile
 * @author Frontend Engineering Team
 * @trace D03-FR-020 (User Profile Management)
 * @trace D04 §5.3 (Authenticated Portal Design)
 * @trace D12 §4.2 (Profile Management UI)
 * @version 1.0
 * @wcag WCAG 2.2 Level AA
 */
--}}

<div class="py-6">
    {{-- Page Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('profile.title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('profile.description') }}
        </p>
    </div>

    {{-- ARIA Live Region for Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="profile-announcements"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Profile Information Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('profile.information_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('profile.information_description') }}
                </p>
            </x-slot>

            <form wire:submit.prevent="updateProfile" class="space-y-6">
                {{-- Success Alert --}}
                @if ($profileUpdateSuccess)
                    <x-ui.alert type="success" dismissible>
                        {{ __('profile.update_success') }}
                    </x-ui.alert>
                @endif

                {{-- Error Alert --}}
                @if ($profileError)
                    <x-ui.alert type="error" dismissible>
                        {{ $profileError }}
                    </x-ui.alert>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Editable Fields --}}
                    <div>
                        <x-form.input wire:model="name" id="name" name="name" type="text" :label="__('profile.name')"
                            :placeholder="__('profile.name_placeholder')" required autocomplete="name" />
                    </div>

                    <div>
                        <x-form.input wire:model="phone" id="phone" name="phone" type="tel" :label="__('profile.phone')"
                            :placeholder="__('profile.phone_placeholder')" autocomplete="tel" />
                    </div>

                    {{-- Read-Only Fields --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.email') }}
                        </label>
                        <input type="email" id="email" value="{{ $email }}" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                            aria-describedby="email-readonly" />
                        <p id="email-readonly" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('profile.email_readonly') }}
                        </p>
                    </div>

                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.staff_id') }}
                        </label>
                        <input type="text" id="staff_id" value="{{ $staff_id }}" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.grade') }}
                        </label>
                        <input type="text" id="grade" value="{{ $grade }}" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.division') }}
                        </label>
                        <input type="text" id="division" value="{{ $division }}" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed" />
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('profile.position') }}
                        </label>
                        <input type="text" id="position" value="{{ $position }}" readonly
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed" />
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="updateProfile">
                        <span wire:loading.remove wire:target="updateProfile">
                            {{ __('profile.save_changes') }}
                        </span>
                        <span wire:loading wire:target="updateProfile">
                            {{ __('profile.saving') }}
                        </span>
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Notification Preferences Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('profile.notifications_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('profile.notifications_description') }}
                </p>
            </x-slot>

            <div class="space-y-4">
                {{-- Helpdesk Notifications --}}
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('profile.helpdesk_notifications') }}
                    </h3>
                    <div class="space-y-3">
                        <x-form.checkbox wire:model.live="notificationPreferences.ticket_updates"
                            wire:change="updateNotificationPreferences" id="ticket_updates" name="ticket_updates"
                            :label="__('profile.ticket_updates')" :description="__('profile.ticket_updates_desc')" />

                        <x-form.checkbox wire:model.live="notificationPreferences.ticket_assignments"
                            wire:change="updateNotificationPreferences" id="ticket_assignments"
                            name="ticket_assignments" :label="__('profile.ticket_assignments')" :description="__('profile.ticket_assignments_desc')" />

                        <x-form.checkbox wire:model.live="notificationPreferences.ticket_comments"
                            wire:change="updateNotificationPreferences" id="ticket_comments" name="ticket_comments"
                            :label="__('profile.ticket_comments')" :description="__('profile.ticket_comments_desc')" />

                        <x-form.checkbox wire:model.live="notificationPreferences.sla_alerts"
                            wire:change="updateNotificationPreferences" id="sla_alerts" name="sla_alerts"
                            :label="__('profile.sla_alerts')" :description="__('profile.sla_alerts_desc')" />
                    </div>
                </div>

                {{-- Asset Loan Notifications --}}
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('profile.loan_notifications') }}
                    </h3>
                    <div class="space-y-3">
                        <x-form.checkbox wire:model.live="notificationPreferences.loan_updates"
                            wire:change="updateNotificationPreferences" id="loan_updates" name="loan_updates"
                            :label="__('profile.loan_updates')" :description="__('profile.loan_updates_desc')" />

                        <x-form.checkbox wire:model.live="notificationPreferences.loan_approvals"
                            wire:change="updateNotificationPreferences" id="loan_approvals" name="loan_approvals"
                            :label="__('profile.loan_approvals')" :description="__('profile.loan_approvals_desc')" />

                        <x-form.checkbox wire:model.live="notificationPreferences.loan_reminders"
                            wire:change="updateNotificationPreferences" id="loan_reminders" name="loan_reminders"
                            :label="__('profile.loan_reminders')" :description="__('profile.loan_reminders_desc')" />
                    </div>
                </div>

                {{-- System Notifications --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('profile.system_notifications') }}
                    </h3>
                    <div class="space-y-3">
                        <x-form.checkbox wire:model.live="notificationPreferences.system_announcements"
                            wire:change="updateNotificationPreferences" id="system_announcements"
                            name="system_announcements" :label="__('profile.system_announcements')" :description="__('profile.system_announcements_desc')" />
                    </div>
                </div>

                {{-- Auto-save indicator --}}
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg wire:loading wire:target="updateNotificationPreferences" class="animate-spin h-4 w-4 mr-2"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading wire:target="updateNotificationPreferences">
                        {{ __('profile.saving_preferences') }}
                    </span>
                    <span wire:loading.remove wire:target="updateNotificationPreferences">
                        {{ __('profile.preferences_auto_save') }}
                    </span>
                </div>
            </div>
        </x-ui.card>

        {{-- Password Change Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('profile.password_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('profile.password_description') }}
                </p>
            </x-slot>

            <form wire:submit.prevent="updatePassword" class="space-y-6">
                {{-- Success Alert --}}
                @if ($passwordUpdateSuccess)
                    <x-ui.alert type="success" dismissible>
                        {{ __('profile.password_updated') }}
                    </x-ui.alert>
                @endif

                {{-- Error Alert --}}
                @if ($passwordError)
                    <x-ui.alert type="error" dismissible>
                        {{ $passwordError }}
                    </x-ui.alert>
                @endif

                <div class="space-y-4">
                    <div>
                        <x-form.input wire:model="current_password" id="current_password" name="current_password"
                            type="password" :label="__('profile.current_password')" :placeholder="__('profile.current_password_placeholder')" required
                            autocomplete="current-password" />
                    </div>

                    <div>
                        <x-form.input wire:model="password" id="password" name="password" type="password"
                            :label="__('profile.new_password')" :placeholder="__('profile.new_password_placeholder')" required autocomplete="new-password" />
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('profile.password_requirements') }}
                        </p>
                    </div>

                    <div>
                        <x-form.input wire:model="password_confirmation" id="password_confirmation"
                            name="password_confirmation" type="password" :label="__('profile.confirm_password')" :placeholder="__('profile.confirm_password_placeholder')"
                            required autocomplete="new-password" />
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="updatePassword">
                        <span wire:loading.remove wire:target="updatePassword">
                            {{ __('profile.update_password') }}
                        </span>
                        <span wire:loading wire:target="updatePassword">
                            {{ __('profile.updating_password') }}
                        </span>
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</div>

@push('scripts')
    <script>
        // Listen for Livewire events and announce to screen readers
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('profile-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });

            Livewire.on('preferences-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });

            Livewire.on('password-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });
        });
    </script>
@endpush
