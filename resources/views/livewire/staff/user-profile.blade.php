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
        <h1 class="text-3xl font-bold text-slate-100">
            {{ __('profile.title') }}
        </h1>
        <p class="mt-2 text-sm text-slate-300">
            {{ __('profile.description') }}
        </p>
    </div>

    {{-- ARIA Live Region for Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="profile-announcements"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Profile Picture Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-slate-100">
                    {{ __('profile.picture_title') }}
                </h2>
                <p class="mt-1 text-sm text-slate-300">
                    {{ __('profile.picture_description') }}
                </p>
            </x-slot>

            {{-- Success Alert --}}
            @if ($profilePictureUpdateSuccess)
                <x-ui.alert type="success" dismissible>
                    {{ __('profile.picture_updated') }}
                </x-ui.alert>
            @endif

            {{-- Error Alert --}}
            @if ($profilePictureError)
                <x-ui.alert type="error" dismissible>
                    {{ $profilePictureError }}
                </x-ui.alert>
            @endif

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                {{-- Current Profile Picture --}}
                <div class="shrink-0">
                    @if ($currentProfilePicture)
                        <img src="{{ asset('storage/' . $currentProfilePicture) }}"
                            alt="{{ __('profile.current_picture') }}"
                            class="h-32 w-32 rounded-full object-cover border-4 border-slate-700 shadow-lg">
                    @else
                        <div
                            class="h-32 w-32 rounded-full bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-slate-700 shadow-lg">
                            <span class="text-4xl font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Upload Section --}}
                <div class="flex-1 w-full">
                    <form wire:submit.prevent="updateProfilePicture" class="space-y-4">
                        {{-- File Input --}}
                        <div>
                            <label for="profilePicture" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('profile.upload_picture') }}
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="file" wire:model="profilePicture" id="profilePicture"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    class="block w-full text-sm text-slate-300
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-600 file:text-white
                                              hover:file:bg-blue-700
                                              file:cursor-pointer
                                              cursor-pointer
                                              border border-slate-700 rounded-md
                                              bg-slate-800
                                              focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <p class="mt-2 text-xs text-slate-400">
                                {{ __('profile.picture_requirements') }}
                            </p>

                            {{-- Loading indicator for upload --}}
                            <div wire:loading wire:target="profilePicture" class="mt-2">
                                <div class="flex items-center text-sm text-blue-400">
                                    <x-heroicon-o-arrow-path class="animate-spin h-4 w-4 mr-2" />
                                    {{ __('profile.uploading') }}
                                </div>
                            </div>

                            {{-- Preview uploaded image --}}
                            @if ($profilePicture)
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-slate-300 mb-2">{{ __('profile.preview') }}</p>
                                    <img src="{{ $profilePicture->temporaryUrl() }}"
                                        class="h-24 w-24 rounded-full object-cover border-2 border-blue-500 shadow-md"
                                        alt="{{ __('profile.preview_picture') }}">
                                </div>
                            @endif

                            @error('profilePicture')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-3">
                            @if ($profilePicture)
                                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                                    wire:target="updateProfilePicture">
                                    <span wire:loading.remove wire:target="updateProfilePicture">
                                        {{ __('profile.save_picture') }}
                                    </span>
                                    <span wire:loading wire:target="updateProfilePicture">
                                        {{ __('profile.saving') }}
                                    </span>
                                </x-ui.button>

                                <x-ui.button type="button" variant="secondary"
                                    wire:click="$set('profilePicture', null)">
                                    {{ __('common.cancel') }}
                                </x-ui.button>
                            @endif

                            @if ($currentProfilePicture)
                                <x-ui.button type="button" variant="danger" wire:click="removeProfilePicture"
                                    wire:confirm="{{ __('profile.confirm_remove_picture') }}">
                                    <span wire:loading.remove wire:target="removeProfilePicture">
                                        {{ __('profile.remove_picture') }}
                                    </span>
                                    <span wire:loading wire:target="removeProfilePicture">
                                        {{ __('common.removing') }}
                                    </span>
                                </x-ui.button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </x-ui.card>

        {{-- Profile Information Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-slate-100">
                    {{ __('profile.information_title') }}
                </h2>
                <p class="mt-1 text-sm text-slate-300">
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

                    {{-- Read-Only Fields with Request Correction Links --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('common.email') }}
                        </label>
                        <input type="email" id="email" value="{{ $email }}" disabled
                            class="block w-full min-h-11 px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-300 cursor-not-allowed" />
                        <div class="mt-1 flex items-center justify-between">
                            <p id="email-readonly" class="text-xs text-slate-400">
                                {{ __('common.read_only_field') }}
                            </p>
                            <button wire:click="requestCorrection('email')" type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 rounded">
                                {{ __('profile.request_correction') }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('common.staff_id') }}
                        </label>
                        <input type="text" id="staff_id" value="{{ $staff_id }}" disabled
                            class="block w-full min-h-11 px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-300 cursor-not-allowed" />
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                {{ __('common.read_only_field') }}
                            </p>
                            <button wire:click="requestCorrection('staff_id')" type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 rounded">
                                {{ __('profile.request_correction') }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="grade" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('common.grade') }}
                        </label>
                        <input type="text" id="grade" value="{{ $grade }}" disabled
                            class="block w-full min-h-11 px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-300 cursor-not-allowed" />
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                {{ __('common.read_only_field') }}
                            </p>
                            <button wire:click="requestCorrection('grade')" type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 rounded">
                                {{ __('profile.request_correction') }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="division" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('common.division') }}
                        </label>
                        <input type="text" id="division" value="{{ $division }}" disabled
                            class="block w-full min-h-11 px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-300 cursor-not-allowed" />
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                {{ __('common.read_only_field') }}
                            </p>
                            <button wire:click="requestCorrection('division')" type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 rounded">
                                {{ __('profile.request_correction') }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('common.position') }}
                        </label>
                        <input type="text" id="position" value="{{ $position }}" disabled
                            class="block w-full min-h-11 px-3 py-2.5 rounded-md shadow-sm bg-slate-800 border border-slate-700 text-slate-300 cursor-not-allowed" />
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                {{ __('common.read_only_field') }}
                            </p>
                            <button wire:click="requestCorrection('position')" type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 rounded">
                                {{ __('profile.request_correction') }}
                            </button>
                        </div>
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
                <h2 class="text-xl font-semibold text-slate-100">
                    {{ __('profile.notifications_title') }}
                </h2>
                <p class="mt-1 text-sm text-slate-300">
                    {{ __('profile.preferences_description') }}
                </p>
            </x-slot>

            <div class="space-y-4">
                {{-- Helpdesk Notifications --}}
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
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
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
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
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        {{ __('profile.system_notifications') }}
                    </h3>
                    <div class="space-y-3">
                        <x-form.checkbox wire:model.live="notificationPreferences.system_announcements"
                            wire:change="updateNotificationPreferences" id="system_announcements"
                            name="system_announcements" :label="__('profile.system_announcements')" :description="__('profile.system_announcements_desc')" />
                    </div>
                </div>

                {{-- Email Frequency Settings (Requirement 17.5) --}}
                <div class="border-b border-slate-800 pb-4">
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        {{ __('profile.email_frequency_title') ?: 'Email Frequency' }}
                    </h3>
                    <p class="text-xs text-slate-400 mb-3">
                        {{ __('profile.email_frequency_desc') ?: 'Choose how often you receive email notifications' }}
                    </p>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" wire:model.live="emailFrequency" wire:change="updateEmailFrequency"
                                value="immediate" name="email_frequency"
                                class="h-4 w-4 text-blue-600 border-slate-600 bg-slate-800 focus:ring-blue-500 focus:ring-offset-slate-900">
                            <span
                                class="text-sm text-slate-300">{{ __('profile.email_immediate') ?: 'Immediate' }}</span>
                            <span
                                class="text-xs text-slate-400">{{ __('profile.email_immediate_desc') ?: '(Receive emails as events occur)' }}</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" wire:model.live="emailFrequency" wire:change="updateEmailFrequency"
                                value="daily" name="email_frequency"
                                class="h-4 w-4 text-blue-600 border-slate-600 bg-slate-800 focus:ring-blue-500 focus:ring-offset-slate-900">
                            <span
                                class="text-sm text-slate-300">{{ __('profile.email_daily') ?: 'Daily Digest' }}</span>
                            <span
                                class="text-xs text-slate-400">{{ __('profile.email_daily_desc') ?: '(Receive a daily summary)' }}</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" wire:model.live="emailFrequency" wire:change="updateEmailFrequency"
                                value="weekly" name="email_frequency"
                                class="h-4 w-4 text-blue-600 border-slate-600 bg-slate-800 focus:ring-blue-500 focus:ring-offset-slate-900">
                            <span
                                class="text-sm text-slate-300">{{ __('profile.email_weekly') ?: 'Weekly Digest' }}</span>
                            <span
                                class="text-xs text-slate-400">{{ __('profile.email_weekly_desc') ?: '(Receive a weekly summary)' }}</span>
                        </label>
                    </div>
                </div>

                {{-- In-App Notifications Toggle (Requirement 17.5) --}}
                <div>
                    <h3 class="text-sm font-medium text-slate-100 mb-3">
                        {{ __('profile.inapp_notifications_title') ?: 'In-App Notifications' }}
                    </h3>
                    <div class="space-y-3">
                        <x-form.checkbox wire:model.live="inAppNotifications" wire:change="updateInAppNotifications"
                            id="inapp_notifications" name="inapp_notifications" :label="__('profile.inapp_notifications') ?: 'Enable In-App Notifications'"
                            :description="__('profile.inapp_notifications_desc') ?:
                                'Receive real-time notifications within the application via WebSocket'" />
                    </div>
                </div>

                {{-- Auto-save indicator --}}
                <div class="flex items-center text-sm text-slate-300">
                    <x-heroicon-o-arrow-path wire:loading wire:target="updateNotificationPreferences" class="animate-spin h-4 w-4 mr-2" />
                    <span wire:loading wire:target="updateNotificationPreferences">
                        {{ __('profile.saving_preferences') }}
                    </span>
                    <span wire:loading.remove wire:target="updateNotificationPreferences">
                        {{ __('profile.preferences_auto_save') }}
                    </span>
                </div>
            </div>
        </x-ui.card>

        {{-- Two Factor Authentication Card --}}
        <livewire:auth.two-factor-authentication />

        {{-- Password Change Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-slate-100">
                    {{ __('profile.password_title') }}
                </h2>
                <p class="mt-1 text-sm text-slate-300">
                    {{ __('profile.security_description') }}
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
                        <p class="mt-2 text-xs text-slate-300">
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
            </form>
        </x-ui.card>

        {{-- Browser Sessions Card --}}
        <livewire:staff.session-manager />
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

            Livewire.on('profile-picture-updated', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });

            Livewire.on('profile-picture-removed', (event) => {
                const announcer = document.getElementById('profile-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 3000);
                }
            });
        });
    </script>
@endpush
