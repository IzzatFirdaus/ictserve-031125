{{--
    name: notification-preferences.blade.php
    description: Granular notification preference management with toggle controls for 6 notification types
    author: dev-team@motac.gov.my
    trace: SRS-FR-004; D04 §3.3.2; D12 §4; Requirements 3.2
    last-updated: 2025-12-15
    WCAG 2.2 AA Compliant
--}}

<div class="max-w-4xl mx-auto p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('portal.notification_preferences') }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.notification_preferences_description') }}</p>
    </div>

    {{-- Success/Error Messages --}}
    @if($successMessage)
        <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4 mb-6" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-success-600 dark:text-success-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-success-800 dark:text-success-200">{{ $successMessage }}</p>
                @if($lastSaved)
                    <span class="ml-auto text-xs text-success-600 dark:text-success-400">{{ __('portal.saved_at') }}: {{ $lastSaved }}</span>
                @endif
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-4 mb-6" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-danger-600 dark:text-danger-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-danger-800 dark:text-danger-200">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif

    {{-- Preferences Summary Card --}}
    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-primary-900 dark:text-primary-100">{{ __('portal.notifications_enabled') }}</p>
                    <p class="text-xs text-primary-700 dark:text-primary-300">{{ $enabledCount }} {{ __('portal.of') }} 6 {{ __('portal.notification_types') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" wire:click="enableAll" class="text-xs px-3 py-1 min-h-11 bg-primary-600 hover:bg-primary-700 text-white rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    {{ __('portal.enable_all') }}
                </button>
                <button type="button" wire:click="disableAll" class="text-xs px-3 py-1 min-h-11 bg-gray-600 hover:bg-gray-700 text-white rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    {{ __('portal.disable_all') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Email Frequency Configuration per D12 §6.17 --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('portal.email_delivery_settings') }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('portal.email_delivery_description') }}</p>

        {{-- Email Frequency Select --}}
        <div class="mb-4">
            <label for="email-frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('portal.email_frequency_label') }}
            </label>
            <select
                id="email-frequency"
                wire:change="updateEmailFrequency($event.target.value)"
                class="w-full sm:w-64 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 min-h-11"
                aria-describedby="email-frequency-help"
            >
                <option value="immediate" {{ $emailFrequency === 'immediate' ? 'selected' : '' }}>{{ __('portal.email_frequency_immediate') }}</option>
                <option value="daily_digest" {{ $emailFrequency === 'daily_digest' ? 'selected' : '' }}>{{ __('portal.email_frequency_daily') }}</option>
                <option value="weekly_digest" {{ $emailFrequency === 'weekly_digest' ? 'selected' : '' }}>{{ __('portal.email_frequency_weekly') }}</option>
                <option value="disabled" {{ $emailFrequency === 'disabled' ? 'selected' : '' }}>{{ __('portal.email_frequency_disabled') }}</option>
            </select>
            <p id="email-frequency-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('portal.email_frequency_help') }}</p>
        </div>

        {{-- Digest Schedule (shown only for daily/weekly digest) --}}
        @if(in_array($emailFrequency, ['daily_digest', 'weekly_digest']))
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('portal.digest_schedule') }}</h4>
                <div class="flex flex-wrap gap-4">
                    {{-- Digest Time --}}
                    <div>
                        <label for="digest-time" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('portal.digest_time') }}</label>
                        <input
                            type="time"
                            id="digest-time"
                            wire:model.live.debounce.500ms="digestTime"
                            wire:change="updateDigestSchedule"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 min-h-11"
                        >
                    </div>

                    {{-- Digest Day (only for weekly) --}}
                    @if($emailFrequency === 'weekly_digest')
                        <div>
                            <label for="digest-day" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('portal.digest_day') }}</label>
                            <select
                                id="digest-day"
                                wire:model.live="digestDay"
                                wire:change="updateDigestSchedule"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 min-h-11"
                            >
                                <option value="0">{{ __('portal.day_sunday') }}</option>
                                <option value="1">{{ __('portal.day_monday') }}</option>
                                <option value="2">{{ __('portal.day_tuesday') }}</option>
                                <option value="3">{{ __('portal.day_wednesday') }}</option>
                                <option value="4">{{ __('portal.day_thursday') }}</option>
                                <option value="5">{{ __('portal.day_friday') }}</option>
                                <option value="6">{{ __('portal.day_saturday') }}</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Notification Preferences Grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">

        {{-- Ticket Status Updates --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="ticket-status-updates" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.ticket_status_updates') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.ticket_status_updates_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $ticketStatusUpdates ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_ticket_status_updates') }}"
                        wire:click="updatePreference('ticketStatusUpdates', {{ $ticketStatusUpdates ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $ticketStatusUpdates ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_ticket_status_updates') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ticketStatusUpdates ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Loan Approval Notifications --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="loan-approval-notifications" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.loan_approval_notifications') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.loan_approval_notifications_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $loanApprovalNotifications ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_loan_approval_notifications') }}"
                        wire:click="updatePreference('loanApprovalNotifications', {{ $loanApprovalNotifications ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $loanApprovalNotifications ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_loan_approval_notifications') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $loanApprovalNotifications ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Overdue Reminders --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="overdue-reminders" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.overdue_reminders') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.overdue_reminders_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $overdueReminders ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_overdue_reminders') }}"
                        wire:click="updatePreference('overdueReminders', {{ $overdueReminders ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $overdueReminders ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_overdue_reminders') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $overdueReminders ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- System Announcements --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="system-announcements" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.system_announcements') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.system_announcements_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $systemAnnouncements ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_system_announcements') }}"
                        wire:click="updatePreference('systemAnnouncements', {{ $systemAnnouncements ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $systemAnnouncements ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_system_announcements') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $systemAnnouncements ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Ticket Assignments --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="ticket-assignments" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.ticket_assignments') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.ticket_assignments_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $ticketAssignments ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_ticket_assignments') }}"
                        wire:click="updatePreference('ticketAssignments', {{ $ticketAssignments ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $ticketAssignments ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_ticket_assignments') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ticketAssignments ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Comment Replies --}}
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label for="comment-replies" class="flex items-center cursor-pointer">
                        <div class="mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('portal.comment_replies') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('portal.comment_replies_desc') }}</p>
                        </div>
                    </label>
                </div>
                <div class="ml-6">
                    <button
                        type="button"
                        role="switch"
                        aria-checked="{{ $commentReplies ? 'true' : 'false' }}"
                        aria-label="{{ __('portal.toggle_comment_replies') }}"
                        wire:click="updatePreference('commentReplies', {{ $commentReplies ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 min-h-11 min-w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $commentReplies ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">{{ __('portal.toggle_comment_replies') }}</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $commentReplies ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Reset Button --}}
    <div class="mt-6 flex justify-between items-center">
        <button type="button" wire:click="resetToDefaults" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
            {{ __('portal.reset_to_defaults') }}
        </button>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ __('portal.notification_preferences_auto_save') }}
        </p>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="updatePreference,saveAll,enableAll,disableAll,resetToDefaults" class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.saving') }}</span>
        </div>
    </div>
</div>

