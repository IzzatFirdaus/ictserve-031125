{{--
/**
 * Submit Helpdesk Ticket View
 *
 * WCAG 2.2 AA compliant guest form for helpdesk ticket submission
 * with bilingual support, real-time validation, and optimized performance
 *
 * @component SubmitTicket
 * @requirements 1.1, 1.2, 11.1-11.7, 15.3, 21.4
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2, 3.3.1, 3.3.2)
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 */
--}}

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    {{-- Skip Links for Accessibility --}}
    <x-navigation.skip-links />

    {{-- Header --}}
    <header class="mb-8" role="banner">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            {{ __('helpdesk.submit_ticket') }}
        </h1>
        <p class="text-gray-600">
            {{ __('helpdesk.quick_submission') }} - {{ __('helpdesk.no_login_required') }}
        </p>
    </header>

    {{-- Success Message --}}
    @if ($submitted)
        <div role="alert" aria-live="polite" class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <h2 class="text-lg font-semibold text-green-900 mb-1">
                        {{ __('helpdesk.ticket_submitted') }}
                    </h2>
                    <p class="text-green-800 mb-2">
                        <strong>{{ __('helpdesk.ticket_number') }}:</strong> {{ $ticketNumber }}
                    </p>
                    <p class="text-green-700">
                        {{ __('helpdesk.confirmation_email') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Main Form --}}
        <main role="main">
            <form wire:submit.prevent="submitTicket" class="space-y-6" novalidate>
                {{-- Contact Information Section --}}
                <fieldset class="border border-gray-300 rounded-lg p-6">
                    <legend class="text-xl font-semibold text-gray-900 px-2">
                        {{ __('common.contact_information') }}
                    </legend>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        {{-- Full Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.full_name') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <input type="text" id="name" wire:model.live.debounce.300ms="form.name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.name') border-red-600 @enderror"
                                aria-required="true" aria-invalid="@error('form.name') true @else false @enderror"
                                aria-describedby="name-help @error('form.name') name-error @enderror"
                                autocomplete="name" />
                            <p id="name-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.name_help') }}
                            </p>
                            @error('form.name')
                                <p id="name-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email Address --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.email_address') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <input type="email" id="email" wire:model.live.debounce.300ms="form.email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.email') border-red-600 @enderror"
                                aria-required="true" aria-invalid="@error('form.email') true @else false @enderror"
                                aria-describedby="email-help @error('form.email') email-error @enderror"
                                autocomplete="email" />
                            <p id="email-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.email_help') }}
                            </p>
                            @error('form.email')
                                <p id="email-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Phone Number --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.phone_number') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <input type="tel" id="phone" wire:model.live.debounce.300ms="form.phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.phone') border-red-600 @enderror"
                                aria-required="true" aria-invalid="@error('form.phone') true @else false @enderror"
                                aria-describedby="phone-help @error('form.phone') phone-error @enderror"
                                autocomplete="tel" />
                            <p id="phone-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.phone_help') }}
                            </p>
                            @error('form.phone')
                                <p id="phone-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Staff ID (Optional) --}}
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.staff_id') }}
                            </label>
                            <input type="text" id="staff_id" wire:model.lazy="form.staff_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                                aria-describedby="staff-id-help" />
                            <p id="staff-id-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.staff_id_help') }}
                            </p>
                        </div>
                    </div>
                </fieldset>

                {{-- Issue Details Section --}}
                <fieldset class="border border-gray-300 rounded-lg p-6">
                    <legend class="text-xl font-semibold text-gray-900 px-2">
                        {{ __('common.issue_details') }}
                    </legend>

                    <div class="space-y-6 mt-4">
                        {{-- Division --}}
                        <div>
                            <label for="division_id" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.division') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <select id="division_id" wire:model.live="form.division_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.division_id') border-red-600 @enderror"
                                aria-required="true"
                                aria-invalid="@error('form.division_id') true @else false @enderror"
                                aria-describedby="@error('form.division_id') division-error @enderror">
                                <option value="">{{ __('common.select_option') }}</option>
                                @foreach ($this->divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                            @error('form.division_id')
                                <p id="division-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.issue_category') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <select id="category_id" wire:model.live="form.category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.category_id') border-red-600 @enderror"
                                aria-required="true"
                                aria-invalid="@error('form.category_id') true @else false @enderror"
                                aria-describedby="category-help @error('form.category_id') category-error @enderror">
                                <option value="">{{ __('common.select_option') }}</option>
                                @foreach ($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p id="category-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.category_help') }}
                            </p>
                            @error('form.category_id')
                                <p id="category-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.subject') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <input type="text" id="subject" wire:model.live.debounce.300ms="form.subject"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.subject') border-red-600 @enderror"
                                aria-required="true" aria-invalid="@error('form.subject') true @else false @enderror"
                                aria-describedby="@error('form.subject') subject-error @enderror" />
                            @error('form.subject')
                                <p id="subject-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-900 mb-1">
                                {{ __('helpdesk.problem_description') }}
                                <span class="text-red-600" aria-label="{{ __('common.required_field') }}">*</span>
                            </label>
                            <textarea id="description" wire:model.lazy="form.description" rows="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('form.description') border-red-600 @enderror"
                                aria-required="true" aria-invalid="@error('form.description') true @else false @enderror"
                                aria-describedby="description-help @error('form.description') description-error @enderror" minlength="10"
                                maxlength="5000"></textarea>
                            <p id="description-help" class="mt-1 text-sm text-gray-600">
                                {{ __('helpdesk.description_help') }}
                            </p>
                            @error('form.description')
                                <p id="description-error" class="mt-1 text-sm text-red-600" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Form Actions --}}
                <div class="flex justify-end space-x-4" role="group" aria-label="{{ __('common.form_actions') }}">
                    <button type="button" wire:click="clearForm"
                        class="min-h-[44px] px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                        {{ __('helpdesk.clear_form') }}
                    </button>

                    <button type="submit" wire:loading.attr="disabled"
                        class="min-h-[44px] px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        aria-describedby="submit-help">
                        <span wire:loading.remove>{{ __('helpdesk.submit_ticket_button') }}</span>
                        <span wire:loading aria-live="polite">{{ __('common.submitting') }}</span>
                    </button>
                </div>
            </form>
        </main>
    @endif

    {{-- ARIA Live Region for Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements"></div>
</div>

@push('scripts')
    <script>
        // Listen for ticket submission event
        Livewire.on('ticket-submitted', (event) => {
            const announcer = document.getElementById('form-announcements');
            if (announcer) {
                announcer.textContent =
                    '{{ __('helpdesk.ticket_submitted') }}. {{ __('helpdesk.ticket_number') }}: ' + event
                    .ticketNumber;
            }
        });
    </script>
@endpush
