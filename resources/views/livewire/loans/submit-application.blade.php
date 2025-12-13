{{--
/**
 * Component name: Submit Loan Application View
 * Description: WCAG 2.2 AA compliant multi-step wizard view for guest asset loan application
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.2, D03-FR-012.1-12.5
 * @requirements 1.2, 1.4, 11.1-11.7, 21.5
 * @wcag-level AA
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@php
    $sectionCardClasses = 'rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-6 shadow-xl shadow-slate-950/40';
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-slate-950 py-12 theme-transition">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Skip Links --}}
        <x-navigation.skip-links />

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100 mb-2">
                {{ __('loans.submit_application') }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-slate-300">
                {{ __('loans.submit_application_description') }}
            </p>
        </div>

        {{-- Context Banner --}}
        <div class="mb-8 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/60 p-5 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 dark:bg-blue-900/40 px-3 py-1 text-sm font-semibold text-blue-800 dark:text-blue-100">
                    <span aria-hidden="true" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-blue-300 dark:border-blue-200 text-xs">i</span>
                    <span>{{ __('loans.wizard_progress') }}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-slate-200">
                    {{ __('loans.submit_application_description') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-slate-400">
                    {{ __('loans.next_steps') }}: {{ __('loans.next_step_1') }}
                </p>
            </div>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-6 shadow-xl shadow-slate-950/40" role="progressbar" aria-valuenow="{{ $currentStep }}" aria-valuemin="1"
            aria-valuemax="{{ $totalSteps }}" aria-label="{{ __('loans.wizard_progress') }}">
            <div class="flex items-center justify-between">
                @for ($step = 1; $step <= $totalSteps; $step++)
                    <div class="flex-1 {{ $step < $totalSteps ? 'pr-4' : '' }}">
                        <div class="flex flex-col items-center text-center">
                            <div class="flex items-center w-full">
                                <div class="shrink-0">
                                    <button type="button" wire:click="goToStep({{ $step }})"
                                        @class([
                                            'flex items-center justify-center w-12 h-12 rounded-full border transition min-h-[48px] min-w-[48px] text-base font-semibold shadow-lg shadow-slate-950/30',
                                            'bg-green-600 border-green-400/70 text-white ring-2 ring-green-400/40' => $step <= $currentStep,
                                            'bg-gray-100 dark:bg-slate-900/60 border-gray-300 dark:border-slate-700 text-gray-400 dark:text-slate-400' => $step > $currentStep,
                                        ])
                                        aria-current="{{ $step === $currentStep ? 'step' : 'false' }}"
                                        {{ $step > $currentStep ? 'disabled' : '' }}>
                                        <span>{{ $step }}</span>
                                    </button>
                                </div>
                                @if ($step < $totalSteps)
                                    <div class="flex-1 mx-4">
                                        <div class="h-1.5 rounded-full transition-colors {{ $step < $currentStep ? 'bg-green-600' : 'bg-gray-200 dark:bg-slate-800' }}"></div>
                                    </div>
                                @endif
                            </div>
                            <p class="mt-3 text-xs font-medium text-gray-600 dark:text-slate-300">
                                {{ __('loans.step_' . $step . '_label') }}
                            </p>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Form Card --}}
        <x-ui.card variant="portal" rounded="rounded-2xl" shadow="shadow-xl shadow-slate-950/40">
            <form wire:submit="submit" novalidate>
                {{-- Step 1: Applicant Information --}}
                @if ($currentStep === 1)
                    <section class="{{ $sectionCardClasses }} space-y-6" role="region" aria-label="{{ __('loans.step_1_title') }}">
                        <div class="rounded-xl bg-slate-900/90 dark:bg-slate-800/90 border border-slate-800 dark:border-slate-700 px-4 py-3 text-slate-100">
                            <p class="text-sm font-semibold tracking-wide uppercase">{{ __('loans.step_1_title') }}</p>
                            <p class="mt-1 text-xs text-slate-200">{{ __('loans.submit_application_description') }}</p>
                        </div>

                        <div class="space-y-6">
                            <x-form.input name="applicant_name" label="{{ __('loans.full_name') }}"
                                wire:model.blur="applicant_name" required autocomplete="name"
                                aria-describedby="applicant_name-help" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input name="applicant_email" type="email" label="{{ __('loans.email_address') }}"
                                    wire:model.blur="applicant_email" required autocomplete="email"
                                    aria-describedby="applicant_email-help" />

                                <x-form.input name="applicant_phone" type="tel" label="{{ __('loans.phone_number') }}"
                                    wire:model.blur="applicant_phone" required autocomplete="tel"
                                    aria-describedby="applicant_phone-help" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input name="staff_id" label="{{ __('loans.staff_id') }}" wire:model.lazy="staff_id"
                                    aria-describedby="staff_id-help" />

                                <x-form.select name="grade" label="{{ __('loans.grade') }}" wire:model.live="grade" required
                                    aria-describedby="grade-help">
                                    <option value="41">{{ __('loans.grade_41') }}</option>
                                    <option value="44">{{ __('loans.grade_44') }}</option>
                                    <option value="48">{{ __('loans.grade_48') }}</option>
                                    <option value="52">{{ __('loans.grade_52') }}</option>
                                    <option value="54">{{ __('loans.grade_54') }}</option>
                                </x-form.select>
                            </div>

                            <x-form.select name="division_id" label="{{ __('loans.division') }}"
                                wire:model.live="division_id" required aria-describedby="division_id-help">
                                <option value="">{{ __('loans.select_division') }}</option>
                                @foreach ($this->divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    </section>
                @endif

                {{-- Step 2: Asset Selection --}}
                @if ($currentStep === 2)
                    <section class="{{ $sectionCardClasses }} space-y-6" role="region" aria-label="{{ __('loans.step_2_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-100 mb-4">
                            {{ __('loans.step_2_title') }}
                        </h2>

                        <x-form.input name="search_query" type="search" label="{{ __('loans.search_assets') }}"
                            wire:model.live.debounce.500ms="search_query"
                            placeholder="{{ __('loans.search_placeholder') }}" aria-describedby="search-help" />

                        <div class="space-y-4" role="list" aria-label="{{ __('loans.available_assets') }}">
                            @forelse ($this->availableAssets as $asset)
                                <div role="listitem"
                                    class="border rounded-lg p-4 hover:border-green-600 transition-colors {{ in_array($asset->id, $selected_assets) ? 'border-green-600 bg-green-50' : 'border-gray-300 dark:border-slate-700' }}">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" wire:click="toggleAsset({{ $asset->id }})"
                                            {{ in_array($asset->id, $selected_assets) ? 'checked' : '' }}
                                            class="mt-1 h-5 w-5 text-green-600 border-gray-300 dark:border-slate-700 rounded focus:ring-green-600"
                                            aria-label="{{ __('loans.select_asset', ['name' => $asset->name]) }}" />
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900 dark:text-slate-100">{{ $asset->name }}</div>
                                            <div class="text-sm text-gray-600 dark:text-slate-300">{{ $asset->asset_tag }}</div>
                                            @if ($asset->description)
                                                <div class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ $asset->description }}</div>
                                            @endif
                                            @if ($asset->category)
                                                <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">{{ $asset->category->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-slate-400 text-center py-8">{{ __('loans.no_assets_found') }}</p>
                            @endforelse
                        </div>

                        @if (count($selected_assets) > 0)
                            <div class="bg-green-50 border border-green-200 dark:bg-green-900/30 dark:border-green-600 rounded-lg p-4"
                                role="status" aria-live="polite">
                                <p class="text-sm font-semibold text-green-800 dark:text-green-200">
                                    {{ __('loans.selected_count', ['count' => count($selected_assets)]) }}
                                </p>
                            </div>
                        @endif

                        @error('selected_assets')
                            <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </section>
                @endif

                {{-- Step 3: Loan Period --}}
                @if ($currentStep === 3)
                    <section class="{{ $sectionCardClasses }} space-y-6" role="region" aria-label="{{ __('loans.step_3_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-100 mb-4">
                            {{ __('loans.step_3_title') }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input name="start_date" type="date" label="{{ __('loans.start_date') }}"
                                wire:model.live="start_date" wire:change="checkAvailability" required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" aria-describedby="start_date-help" />

                            <x-form.input name="end_date" type="date" label="{{ __('loans.end_date') }}"
                                wire:model.live="end_date" wire:change="checkAvailability" required
                                min="{{ $start_date ?? date('Y-m-d', strtotime('+2 days')) }}"
                                aria-describedby="end_date-help" />
                        </div>

                        @if (!empty($availability_status))
                            <div class="bg-blue-50 border border-blue-200 dark:bg-blue-900/30 dark:border-blue-700 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('loans.availability_status') }}
                                </h3>
                                <ul class="space-y-1">
                                    @foreach ($availability_status as $status)
                                        <li
                                            class="text-sm {{ $status['available'] ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                            {{ $status['asset_name'] }}:
                                            {{ $status['available'] ? __('loans.available') : __('loans.not_available') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <x-form.textarea name="purpose" label="{{ __('loans.purpose') }}" wire:model.lazy="purpose"
                            required rows="4" minlength="10" maxlength="1000"
                            aria-describedby="purpose-help" />

                        <x-form.input name="location" label="{{ __('loans.location') }}"
                            wire:model.blur="location" required maxlength="255"
                            aria-describedby="location-help" />
                    </section>
                @endif

                {{-- Step 4: Confirmation --}}
                @if ($currentStep === 4 && $applicationNumber)
                    <section class="{{ $sectionCardClasses }} space-y-6 text-center" role="region" aria-label="{{ __('loans.confirmation') }}">
                        <div class="flex justify-center">
                            <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-100">
                            {{ __('loans.application_submitted') }}
                        </h2>

                        <div class="bg-green-50 border border-green-200 dark:bg-green-900/30 dark:border-green-600 rounded-lg p-6">
                            <p class="text-sm text-gray-600 dark:text-slate-300 mb-2">{{ __('loans.application_number') }}</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-300">{{ $applicationNumber }}</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 dark:bg-blue-900/30 dark:border-blue-700 rounded-lg p-4 text-left">
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">{{ __('loans.next_steps') }}</h3>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-slate-200">
                                <li>{{ __('loans.next_step_1') }}</li>
                                <li>{{ __('loans.next_step_2') }}</li>
                                <li>{{ __('loans.next_step_3') }}</li>
                            </ol>
                        </div>

                        <p class="text-gray-600 dark:text-slate-300">
                            {{ __('loans.confirmation_email_sent') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <x-ui.button type="button" wire:click="resetForm" variant="secondary">
                                {{ __('loans.submit_another') }}
                            </x-ui.button>

                            <x-ui.button type="button" onclick="window.location.href='{{ route('welcome') }}'"
                                variant="primary">
                                {{ __('loans.return_home') }}
                            </x-ui.button>
                        </div>
                    </section>
                @endif

                {{-- Navigation Buttons --}}
                @if ($currentStep < 4 || !$applicationNumber)
                    <div class="mt-8 flex justify-between" role="group"
                        aria-label="{{ __('loans.form_navigation') }}">
                        @if ($currentStep > 1)
                            <x-ui.button type="button" wire:click="previousStep" variant="secondary">
                                {{ __('loans.previous') }}
                            </x-ui.button>
                        @else
                            <div></div>
                        @endif

                        @if ($currentStep < 3)
                            <x-ui.button type="button" wire:click="nextStep" variant="primary">
                                {{ __('loans.next') }}
                            </x-ui.button>
                        @elseif ($currentStep === 3)
                            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('loans.submit_application') }}</span>
                                <span wire:loading>{{ __('loans.submitting') }}...</span>
                            </x-ui.button>
                        @endif
                    </div>
                @endif
            </form>
        </x-ui.card>

        {{-- Help / Contact Panel --}}
        <div class="mt-8 rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-100">{{ __('loans.help_card_title') }}</h3>
                <p class="text-sm text-gray-700 dark:text-slate-200">{{ __('loans.help_card_description') }}</p>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700 dark:text-slate-200">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="font-medium">{{ __('loans.help_card_email') }}</dt>
                        <dd>
                            <a class="text-blue-700 dark:text-blue-300 hover:underline" href="mailto:bpm@motac.gov.my">
                                bpm@motac.gov.my
                            </a>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="font-medium">{{ __('loans.help_card_phone') }}</dt>
                        <dd>
                            <a class="text-blue-700 dark:text-blue-300 hover:underline" href="tel:+60326152345">
                                03-2615 2345
                            </a>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ARIA Live Region for Announcements --}}
        <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements"></div>
    </div>
</div>
