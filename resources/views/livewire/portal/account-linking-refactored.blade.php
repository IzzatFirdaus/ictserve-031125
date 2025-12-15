<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
    public bool $showModal = false;
    public string $linkedAccountEmail = '';
    public string $verificationCode = '';
    public bool $showVerificationStep = false;

    public function openModal(): void
    {
        $this->showModal = true;
        $this->showVerificationStep = false;
        $this->linkedAccountEmail = '';
        $this->verificationCode = '';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showVerificationStep = false;
        $this->linkedAccountEmail = '';
        $this->verificationCode = '';
    }

    public function requestLinkingCode(): void
    {
        $this->validate([
            'linkedAccountEmail' => ['required', 'email', 'different:email'],
        ]);

        // Send verification code
        // TODO: Implement sending verification code via email

        $this->showVerificationStep = true;
        $this->dispatch('linking-code-sent');
    }

    public function verifyAndLink(): void
    {
        $this->validate([
            'verificationCode' => ['required', 'string', 'size:6'],
        ]);

        // TODO: Implement verification and linking logic

        $this->dispatch('account-linked');
        $this->closeModal();
    }

    public function resendCode(): void
    {
        // TODO: Implement resending verification code
        $this->dispatch('code-resent');
    }
};

?>

<!-- Modal Backdrop -->
<div class="fixed inset-0 z-40 bg-black bg-opacity-50 dark:bg-opacity-60 transition-opacity duration-200"
     x-show="$wire.showModal"
     @click="$wire.closeModal()"
     aria-hidden="true">
</div>

<!-- Modal Dialog -->
<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-show="$wire.showModal"
     x-transition
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title"
     aria-describedby="modal-description"
     @keydown.escape="$wire.closeModal()"
     x-trap.noscroll="$wire.showModal">

    <div class="bg-white dark:bg-gray-800 rounded-l shadow-dropdown max-w-md w-full overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 id="modal-title" class="text-xl font-heading font-semibold text-gray-900 dark:text-white">
                {{ __('account_linking.title') }}
            </h2>
            <button
                type="button"
                wire:click="closeModal"
                class="inline-flex items-center justify-center min-h-11 min-w-11 rounded-m
                       text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400
                       focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none
                       transition-colors duration-200"
                aria-label="{{ __('common.close') }}">
                <x-heroicon-o-x-mark class="w-6 h-6" aria-hidden="true" />
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <p id="modal-description" class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ __('account_linking.description') }}
            </p>

            <!-- Step 1: Email Entry (Default) -->
            @if(!$this->showVerificationStep)
                <form wire:submit="requestLinkingCode" class="space-y-4">
                    <!-- Email Label and Input -->
                    <div>
                        <label for="linked-email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('account_linking.email_label') }}
                            <span class="text-danger-600" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="email"
                            id="linked-email"
                            wire:model.live.debounce.300ms="linkedAccountEmail"
                            class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   shadow-sm focus:border-primary-500 focus:ring-primary-500
                                   focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                                   min-h-11 px-4 py-2 transition-colors duration-200"
                            placeholder="user@example.com"
                            aria-required="true"
                            aria-describedby="email-help email-error"
                            autocomplete="email"
                        />
                        <p id="email-help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                            {{ __('account_linking.email_help') }}
                        </p>
                        @error('linkedAccountEmail')
                            <p id="email-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Info Alert -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-m">
                        <div class="flex gap-3">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" aria-hidden="true" />
                            <p class="text-sm text-blue-700 dark:text-blue-200">
                                {{ __('account_linking.info_message') }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="flex-1 btn-primary min-h-11 px-4 py-2 rounded-m shadow-button
                                   bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                                   focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                                   transition-colors duration-200 font-medium"
                            wire:loading.attr="disabled"
                            aria-label="{{ __('account_linking.request_code') }}">
                            <span wire:loading.remove>{{ __('account_linking.request_code') }}</span>
                            <span wire:loading class="flex items-center justify-center">
                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" aria-hidden="true" />
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="flex-1 btn-secondary min-h-11 px-4 py-2 rounded-m shadow-button
                                   bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                                   hover:bg-gray-300 dark:hover:bg-gray-600
                                   focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                                   transition-colors duration-200 font-medium">
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            @else
                <!-- Step 2: Verification Code Entry -->
                <form wire:submit="verifyAndLink" class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('account_linking.code_sent_message', ['email' => $this->linkedAccountEmail]) }}
                    </p>

                    <!-- Verification Code Input -->
                    <div>
                        <label for="verification-code" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('account_linking.code_label') }}
                            <span class="text-danger-600" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            id="verification-code"
                            wire:model.live="verificationCode"
                            maxlength="6"
                            inputmode="numeric"
                            class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   shadow-sm focus:border-primary-500 focus:ring-primary-500
                                   focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                                   min-h-11 px-4 py-2 text-center text-2xl letter-spacing tracking-widest transition-colors duration-200"
                            placeholder="000000"
                            aria-required="true"
                            aria-describedby="code-help code-error"
                            autocomplete="one-time-code"
                        />
                        <p id="code-help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                            {{ __('account_linking.code_help') }}
                        </p>
                        @error('verificationCode')
                            <p id="code-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Resend Link -->
                    <div class="text-center">
                        <button
                            type="button"
                            wire:click="resendCode"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300
                                   focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 py-1
                                   font-medium transition-colors duration-200">
                            {{ __('account_linking.resend_code') }}
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="flex-1 btn-primary min-h-11 px-4 py-2 rounded-m shadow-button
                                   bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                                   focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                                   transition-colors duration-200 font-medium"
                            wire:loading.attr="disabled"
                            aria-label="{{ __('account_linking.verify_and_link') }}">
                            <span wire:loading.remove>{{ __('account_linking.verify_and_link') }}</span>
                            <span wire:loading class="flex items-center justify-center">
                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" aria-hidden="true" />
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="flex-1 btn-secondary min-h-11 px-4 py-2 rounded-m shadow-button
                                   bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                                   hover:bg-gray-300 dark:hover:bg-gray-600
                                   focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                                   transition-colors duration-200 font-medium">
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
