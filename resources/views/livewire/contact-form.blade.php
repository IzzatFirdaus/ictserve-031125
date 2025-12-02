{{--
/**
 * Contact Form Component View
 *
 * Routes contact submissions to Helpdesk as "General Enquiry" tickets
 * Displays generated Ticket ID for user tracking
 *
 * @wcag-level AA
 * @trace D03-FR-021, R21 (Contact Form Integration)
 */
--}}

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    {{-- Success State --}}
    @if ($submitted && !$submissionFailed)
        <div class="p-8 text-center" x-data="{ copied: false }">
            {{-- Success Icon --}}
            <div
                class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-6">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                {{ __('Message Sent Successfully!') }}
            </h3>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                {{ __('Thank you for contacting us. Your message has been received and a support ticket has been created.') }}
            </p>

            {{-- Ticket Number Display --}}
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 mb-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ __('Your Ticket Number') }}
                </p>
                <div class="flex items-center justify-center gap-3">
                    <span class="text-2xl font-mono font-bold text-primary-600 dark:text-primary-400">
                        {{ $ticketNumber }}
                    </span>
                    <button type="button"
                        @click="navigator.clipboard.writeText('{{ $ticketNumber }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        title="{{ __('Copy to clipboard') }}">
                        <svg x-show="!copied" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="copied" x-cloak class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
                @if ($isOptimisticState)
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                        {{ __('Processing... Final ticket number will be confirmed shortly.') }}
                    </p>
                @endif
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                {{ __('A confirmation email has been sent to :email. Please save your ticket number for future reference.', ['email' => $email]) }}
            </p>

            {{-- Track Ticket Link --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('helpdesk.guest.track') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('Track Your Ticket') }}
                </a>
                <button type="button" wire:click="resetForm"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    {{ __('Send Another Message') }}
                </button>
            </div>
        </div>
    @else
        {{-- Form Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="h-6 w-6 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ __('Send Us a Message') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Your message will be tracked as a support ticket for follow-up.') }}
            </p>
        </div>

        {{-- Error Alert --}}
        @if ($submissionFailed)
            <div
                class="mx-6 mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            {{ $errorMessage }}
                        </p>
                        <button type="button" wire:click="retrySubmission"
                            class="mt-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-500 underline">
                            {{ __('Try Again') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Contact Form --}}
        <form wire:submit="submit" class="p-6 space-y-6">
            {{-- Name Field --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Full Name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" wire:model.live.debounce.300ms="name"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('name') border-red-500 @enderror"
                    placeholder="{{ __('Enter your full name') }}" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Email Address') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" wire:model.live.debounce.300ms="email"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('email') border-red-500 @enderror"
                    placeholder="{{ __('Enter your email address') }}" required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone Field (Optional) --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Phone Number') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                </label>
                <input type="tel" id="phone" wire:model.live.debounce.300ms="phone"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('phone') border-red-500 @enderror"
                    placeholder="{{ __('Enter your phone number') }}">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Field --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" wire:model.live.debounce.300ms="subject"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('subject') border-red-500 @enderror"
                    placeholder="{{ __('What is your message about?') }}" required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message Field --}}
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Message') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="message" wire:model.live.debounce.300ms="message" rows="5"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('message') border-red-500 @enderror"
                    placeholder="{{ __('Please describe your enquiry in detail...') }}" required></textarea>
                <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ __('Minimum 10 characters') }}</span>
                    <span>{{ strlen($message) }}/5000</span>
                </div>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Notice --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                        {{ __('Your message will be converted to a support ticket. You will receive a ticket number for tracking purposes.') }}
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submit" class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ __('Send Message') }}
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Sending...') }}
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>model.live.debounce.300ms="phone"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('phone') border-red-500 @enderror"
                    placeholder="{{ __('Enter your phone number') }}">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Field --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" wire:model.live.debounce.300ms="subject"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('subject') border-red-500 @enderror"
                    placeholder="{{ __('What is your message about?') }}" required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message Field --}}
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Message') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="message" wire:model.live.debounce.300ms="message" rows="5"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('message') border-red-500 @enderror"
                    placeholder="{{ __('Please describe your enquiry in detail...') }}" required></textarea>
                <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ __('Minimum 10 characters') }}</span>
                    <span>{{ strlen($message) }}/5000</span>
                </div>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Notice --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                        {{ __('Your message will be converted to a support ticket. You will receive a ticket number for tracking purposes.') }}
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submit" class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ __('Send Message') }}
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Sending...') }}
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>model.live.debounce.300ms="phone"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('phone') border-red-500 @enderror"
                    placeholder="{{ __('Enter your phone number') }}">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Field --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" wire:model.live.debounce.300ms="subject"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('subject') border-red-500 @enderror"
                    placeholder="{{ __('What is your message about?') }}" required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message Field --}}
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Message') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="message" wire:model.live.debounce.300ms="message" rows="5"
                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('message') border-red-500 @enderror"
                    placeholder="{{ __('Please describe your enquiry in detail...') }}" required></textarea>
                <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ __('Minimum 10 characters') }}</span>
                    <span>{{ strlen($message) }}/5000</span>
                </div>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Notice --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                        {{ __('Your message will be converted to a support ticket. You will receive a ticket number for tracking purposes.') }}
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submit">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ __('Send Message') }}
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Sending...') }}
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
