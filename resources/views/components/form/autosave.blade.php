{{--
/**
 * Component: Form Autosave
 * Description: LocalStorage-based form draft preservation with recovery prompt
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-012.2 (Draft Preservation)
 * @trace D03-FR-017.5 (Form Autosave)
 * @trace D13 §3.7.2 (Confirmation Dialog)
 * @trace D13 §3.7.3 (Error Prevention Checklist)
 * @wcag WCAG 2.2 Level AA (SC 3.3.4 Error Prevention)
 * @version 1.0.0
 * @created 2025-12-05
 *
 * Requirements:
 * - 12.2: Draft preservation per D13 §3.7.3 error prevention checklist
 * - 17.5: Recovery prompt with confirmation dialog per D13 §3.7.2
 *
 * Usage:
 * <x-form.autosave
 *     form-id="helpdesk-ticket-form"
 *     :fields="['title', 'description', 'category', 'priority']"
 *     :auto-save-interval="30"
 * />
 *
 * Then in your form, add data-autosave="field-name" to inputs:
 * <input type="text" name="title" data-autosave="title" />
 */
--}}

@props([
    'formId' => 'form-' . md5(request()->path()),
    'fields' => [],
    'autoSaveInterval' => 30,
    'showIndicator' => true,
    'confirmRestore' => true,
])

@php
    $storageKey = 'autosave_' . $formId;
@endphp

<div x-data="formAutosave({
    formId: '{{ $formId }}',
    storageKey: '{{ $storageKey }}',
    fields: {{ json_encode($fields) }},
    autoSaveInterval: {{ $autoSaveInterval }},
    showIndicator: {{ $showIndicator ? 'true' : 'false' }},
    confirmRestore: {{ $confirmRestore ? 'true' : 'false' }}
})" x-init="init()" class="relative" {{ $attributes }}>
    {{-- Autosave status indicator --}}
    @if ($showIndicator)
        <div x-show="showStatus" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1" class="fixed bottom-4 right-4 z-50" role="status"
            aria-live="polite">
            <div class="flex items-center gap-2 px-4 py-2 rounded-lg shadow-lg text-sm"
                :class="{
                    'bg-success-50 text-success-700 dark:bg-success-900/50 dark:text-success-300': status === 'saved',
                    'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300': status === 'saving',
                    'bg-warning-50 text-warning-700 dark:bg-warning-900/50 dark:text-warning-300': status === 'unsaved'
                }">
                {{-- Saving spinner --}}
                <template x-if="status === 'saving'">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </template>

                {{-- Saved checkmark --}}
                <template x-if="status === 'saved'">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </template>

                {{-- Unsaved warning --}}
                <template x-if="status === 'unsaved'">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </template>

                <span x-text="statusMessage"></span>
            </div>
        </div>
    @endif

    {{-- Recovery prompt modal --}}
    <template x-teleport="body">
        <div x-show="showRecoveryPrompt" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="recovery-modal-title" role="dialog" aria-modal="true"
            @keydown.escape.window="discardDraft()">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity" aria-hidden="true"></div>

            {{-- Modal panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showRecoveryPrompt" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                    x-trap.noscroll="showRecoveryPrompt">
                    <div class="sm:flex sm:items-start">
                        {{-- Icon --}}
                        <div
                            class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-primary-600 dark:text-primary-400"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 id="recovery-modal-title"
                                class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                {{ __('forms.Recover unsaved draft?') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('forms.We found an unsaved draft from your previous session. Would you like to restore it?') }}
                                </p>
                                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('forms.Last saved:') }} <span x-text="lastSavedTime"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" @click="restoreDraft()"
                            class="inline-flex w-full justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 sm:w-auto min-h-11">
                            {{ __('forms.Restore draft') }}
                        </button>
                        <button type="button" @click="discardDraft()"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto min-h-11">
                            {{ __('forms.Start fresh') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Form content slot --}}
    {{ $slot }}
</div>

<script>
    function formAutosave(config) {
        return {
            formId: config.formId,
            storageKey: config.storageKey,
            fields: config.fields,
            autoSaveInterval: config.autoSaveInterval * 1000,
            showIndicator: config.showIndicator,
            confirmRestore: config.confirmRestore,

            status: 'idle',
            statusMessage: '',
            showStatus: false,
            showRecoveryPrompt: false,
            lastSavedTime: '',
            saveTimer: null,
            statusTimer: null,

            init() {
                // Check for existing draft
                this.checkForDraft();

                // Set up autosave
                this.setupAutosave();

                // Listen for form submission to clear draft
                this.setupFormSubmitListener();

                // Warn before leaving with unsaved changes
                this.setupBeforeUnloadListener();
            },

            checkForDraft() {
                const draft = this.getDraft();
                if (draft && Object.keys(draft.data).length > 0) {
                    this.lastSavedTime = this.formatDate(new Date(draft.timestamp));

                    if (this.confirmRestore) {
                        this.showRecoveryPrompt = true;
                    } else {
                        this.restoreDraft();
                    }
                }
            },

            setupAutosave() {
                // Watch for changes on tracked fields
                this.fields.forEach(field => {
                    const elements = document.querySelectorAll(`[data-autosave="${field}"], [name="${field}"]`);
                    elements.forEach(el => {
                        el.addEventListener('input', () => this.scheduleAutosave());
                        el.addEventListener('change', () => this.scheduleAutosave());
                    });
                });

                // Also set up periodic autosave
                if (this.autoSaveInterval > 0) {
                    setInterval(() => this.saveDraft(), this.autoSaveInterval);
                }
            },

            setupFormSubmitListener() {
                const form = document.getElementById(this.formId) || document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', () => this.clearDraft());
                }

                // Also listen for Livewire form submissions
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('commit', ({
                        succeed
                    }) => {
                        succeed(() => {
                            // Check if this was a successful form submission
                            this.clearDraft();
                        });
                    });
                }
            },

            setupBeforeUnloadListener() {
                window.addEventListener('beforeunload', (e) => {
                    if (this.hasUnsavedChanges()) {
                        this.saveDraft();
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });
            },

            scheduleAutosave() {
                this.status = 'unsaved';
                this.statusMessage = '{{ __('forms.Unsaved changes') }}';
                this.showStatus = true;

                clearTimeout(this.saveTimer);
                this.saveTimer = setTimeout(() => this.saveDraft(), 2000);
            },

            saveDraft() {
                this.status = 'saving';
                this.statusMessage = '{{ __('forms.Saving...') }}';
                this.showStatus = true;

                const data = this.collectFormData();
                const draft = {
                    data: data,
                    timestamp: Date.now()
                };

                try {
                    localStorage.setItem(this.storageKey, JSON.stringify(draft));

                    this.status = 'saved';
                    this.statusMessage = '{{ __('forms.Draft saved') }}';

                    // Hide status after 3 seconds
                    clearTimeout(this.statusTimer);
                    this.statusTimer = setTimeout(() => {
                        this.showStatus = false;
                    }, 3000);
                } catch (e) {
                    console.error('Failed to save draft:', e);
                    this.status = 'unsaved';
                    this.statusMessage = '{{ __('forms.Failed to save') }}';
                }
            },

            collectFormData() {
                const data = {};
                this.fields.forEach(field => {
                    const el = document.querySelector(`[data-autosave="${field}"], [name="${field}"]`);
                    if (el) {
                        if (el.type === 'checkbox') {
                            data[field] = el.checked;
                        } else if (el.type === 'radio') {
                            const checked = document.querySelector(`[name="${field}"]:checked`);
                            data[field] = checked ? checked.value : null;
                        } else {
                            data[field] = el.value;
                        }
                    }
                });
                return data;
            },

            getDraft() {
                try {
                    const stored = localStorage.getItem(this.storageKey);
                    return stored ? JSON.parse(stored) : null;
                } catch (e) {
                    return null;
                }
            },

            restoreDraft() {
                const draft = this.getDraft();
                if (draft && draft.data) {
                    Object.entries(draft.data).forEach(([field, value]) => {
                        const el = document.querySelector(`[data-autosave="${field}"], [name="${field}"]`);
                        if (el) {
                            if (el.type === 'checkbox') {
                                el.checked = value;
                            } else if (el.type === 'radio') {
                                const radio = document.querySelector(`[name="${field}"][value="${value}"]`);
                                if (radio) radio.checked = true;
                            } else {
                                el.value = value;
                                // Trigger input event for Livewire/Alpine reactivity
                                el.dispatchEvent(new Event('input', {
                                    bubbles: true
                                }));
                            }
                        }
                    });

                    // Announce restoration to screen readers
                    this.$dispatch('announce', {
                        message: '{{ __('forms.Draft restored successfully') }}'
                    });
                }

                this.showRecoveryPrompt = false;
            },

            discardDraft() {
                this.clearDraft();
                this.showRecoveryPrompt = false;

                // Announce to screen readers
                this.$dispatch('announce', {
                    message: '{{ __('forms.Starting with a fresh form') }}'
                });
            },

            clearDraft() {
                try {
                    localStorage.removeItem(this.storageKey);
                } catch (e) {
                    console.error('Failed to clear draft:', e);
                }
            },

            hasUnsavedChanges() {
                const currentData = this.collectFormData();
                const draft = this.getDraft();

                if (!draft) {
                    return Object.values(currentData).some(v => v !== '' && v !== null && v !== false);

                    return JSON.stringify(currentData) !== JSON.stringify(draft.data);
                },

                formatDate(date) {
                    return date.toLocaleString('{{ app()->getLocale() }}', {
                        dateStyle: 'medium',
                        timeStyle: 'short'
                    });
                }
            };
        }
</script>
