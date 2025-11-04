{{--
/**
 * Component name: Submit Helpdesk Ticket View
 * Description: WCAG 2.2 AA compliant multi-step wizard view for guest helpdesk ticket submission
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1, D03-FR-011.1-11.7
 * @requirements 1.1, 1.2, 11.1-11.7, 21.5
 * @wcag-level AA
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Skip Links --}}
        <x-navigation.skip-links />

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{ __('helpdesk.submit_ticket') }}
            </h1>
            <p class="text-lg text-gray-600">
                {{ __('helpdesk.submit_ticket_description') }}
            </p>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8" role="progressbar" aria-valuenow="{{ $currentStep }}" aria-valuemin="1"
            aria-valuemax="{{ $totalSteps }}" aria-label="{{ __('helpdesk.wizard_progress') }}">
            <div class="flex items-center justify-between">
                @for ($step = 1; $step <= $totalSteps; $step++)
                    <div class="flex-1 {{ $step < $totalSteps ? 'pr-4' : '' }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <button type="button" wire:click="goToStep({{ $step }})"
                                    @class([
                                        'flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors min-h-[44px] min-w-[44px]',
                                        'bg-blue-600 border-blue-600 text-white' => $step <= $currentStep,
                                        'bg-white border-gray-300 text-gray-500' => $step > $currentStep,
                                    ])
                                    aria-current="{{ $step === $currentStep ? 'step' : 'false' }}"
                                    {{ $step > $currentStep ? 'disabled' : '' }}>
                                    <span class="text-sm font-semibold">{{ $step }}</span>
                                </button>
                            </div>
                            @if ($step < $totalSteps)
                                <div class="flex-1 ml-4">
                                    <div @class([
                                        'h-1 rounded-full transition-colors',
                                        'bg-blue-600' => $step < $currentStep,
                                        'bg-gray-300' => $step >= $currentStep,
                                    ])></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Form Card --}}
        <x-ui.card>
            <form wire:submit="submit" novalidate>
                {{-- Step 1: Contact Information --}}
                @if ($currentStep === 1)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_1_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_1_title') }}
                        </h2>

                        <x-form.input name="guest_name" label="{{ __('helpdesk.full_name') }}"
                            wire:model.live.debounce.300ms="guest_name" required autocomplete="name"
                            aria-describedby="guest_name-help" />

                        <x-form.input name="guest_email" type="email" label="{{ __('helpdesk.email_address') }}"
                            wire:model.live.debounce.300ms="guest_email" required autocomplete="email"
                            aria-describedby="guest_email-help" />

                        <x-form.input name="guest_phone" type="tel" label="{{ __('helpdesk.phone_number') }}"
                            wire:model.live.debounce.300ms="guest_phone" required autocomplete="tel"
                            aria-describedby="guest_phone-help" />

                        <x-form.input name="staff_id" label="{{ __('helpdesk.staff_id') }}" wire:model.lazy="staff_id"
                            aria-describedby="staff_id-help" />

                        <x-form.select name="division_id" label="{{ __('helpdesk.division') }}"
                            wire:model.live="division_id" required aria-describedby="division_id-help">
                            <option value="">{{ __('helpdesk.select_division') }}</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>
                @endif

                {{-- Step 2: Issue Details --}}
                @if ($currentStep === 2)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_2_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_2_title') }}
                        </h2>

                        <x-form.select name="category_id" label="{{ __('helpdesk.category') }}"
                            wire:model.live="category_id" required aria-describedby="category_id-help">
                            <option value="">{{ __('helpdesk.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="priority" label="{{ __('helpdesk.priority') }}" wire:model.live="priority"
                            required aria-describedby="priority-help">
                            <option value="low">{{ __('helpdesk.priority_low') }}</option>
                            <option value="normal">{{ __('helpdesk.priority_normal') }}</option>
                            <option value="high">{{ __('helpdesk.priority_high') }}</option>
                            <option value="urgent">{{ __('helpdesk.priority_urgent') }}</option>
                        </x-form.select>

                        <x-form.input name="subject" label="{{ __('helpdesk.subject') }}"
                            wire:model.live.debounce.300ms="subject" required maxlength="255"
                            aria-describedby="subject-help" />

                        <x-form.textarea name="description" label="{{ __('helpdesk.description') }}"
                            wire:model.lazy="description" required rows="6" minlength="10" maxlength="5000"
                            aria-describedby="description-help" />

                        <x-form.select name="asset_id" label="{{ __('helpdesk.related_asset') }}"
                            wire:model.live="asset_id" aria-describedby="asset_id-help">
                            <option value="">{{ __('helpdesk.no_asset') }}</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_tag }})
                                </option>
                            @endforeach
                        </x-form.select>
                    </div>
                @endif

                {{-- Step 3: Attachments --}}
                @if ($currentStep === 3)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_3_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_3_title') }}
                        </h2>

                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('helpdesk.attachments') }}
                                <span class="text-gray-500">({{ __('helpdesk.optional') }})</span>
                            </label>

                            <div x-data="{ isDragging: false }" @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                                :class="{ 'border-blue-600 bg-blue-50': isDragging }"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors">
                                <input type="file" wire:model="attachments" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="sr-only" id="attachments"
                                    x-ref="fileInput" aria-describedby="attachments-help" />

                                <label for="attachments" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span
                                            class="font-semibold text-blue-600 hover:text-blue-700">{{ __('helpdesk.click_to_upload') }}</span>
                                        {{ __('helpdesk.or_drag_and_drop') }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('helpdesk.file_types') }}: JPG, PNG, PDF, DOC, DOCX
                                        ({{ __('helpdesk.max_size') }}: 10MB)
                                    </p>
                                </label>
                            </div>

                            <div wire:loading wire:target="attachments" class="text-sm text-gray-600" role="status"
                                aria-live="polite">
                                {{ __('helpdesk.uploading') }}...
                            </div>

                            @if (!empty($attachments))
                                <ul class="space-y-2" role="list"
                                    aria-label="{{ __('helpdesk.uploaded_files') }}">
                                    @foreach ($attachments as $index => $attachment)
                                        <li class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                            <span
                                                class="text-sm text-gray-700">{{ $attachment->getClientOriginalName() }}</span>
                                            <button type="button"
                                                wire:click="$set('attachments.{{ $index }}', null)"
                                                class="text-red-600 hover:text-red-800 min-h-[44px] min-w-[44px] flex items-center justify-center"
                                                aria-label="{{ __('helpdesk.remove_file', ['name' => $attachment->getClientOriginalName()]) }}">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @error('attachments')
                                <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Step 4: Confirmation --}}
                @if ($currentStep === 4 && $ticketNumber)
                    <div class="space-y-6 text-center" role="region"
                        aria-label="{{ __('helpdesk.confirmation') }}">
                        <div class="flex justify-center">
                            <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ __('helpdesk.ticket_submitted') }}
                        </h2>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <p class="text-sm text-gray-600 mb-2">{{ __('helpdesk.ticket_number') }}</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $ticketNumber }}</p>
                        </div>

                        <p class="text-gray-600">
                            {{ __('helpdesk.confirmation_email_sent') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <x-ui.button type="button" wire:click="resetForm" variant="secondary">
                                {{ __('helpdesk.submit_another') }}
                            </x-ui.button>

                            <x-ui.button type="button" onclick="window.location.href='{{ route('welcome') }}'"
                                variant="primary">
                                {{ __('helpdesk.return_home') }}
                            </x-ui.button>
                        </div>
                    </div>
                @endif

                {{-- Navigation Buttons --}}
                @if ($currentStep < 4 || !$ticketNumber)
                    <div class="mt-8 flex justify-between" role="group"
                        aria-label="{{ __('helpdesk.form_navigation') }}">
                        @if ($currentStep > 1)
                            <x-ui.button type="button" wire:click="previousStep" variant="secondary">
                                {{ __('helpdesk.previous') }}
                            </x-ui.button>
                        @else
                            <div></div>
                        @endif

                        @if ($currentStep < 3)
                            <x-ui.button type="button" wire:click="nextStep" variant="primary">
                                {{ __('helpdesk.next') }}
                            </x-ui.button>
                        @elseif ($currentStep === 3)
                            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('helpdesk.submit_button') }}</span>
                                <span wire:loading>{{ __('helpdesk.submitting') }}...</span>
                            </x-ui.button>
                        @endif
                    </div>
                @endif
            </form>
        </x-ui.card>

        {{-- ARIA Live Region for Announcements --}}
        <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements"></div>
    </div>
</div>
