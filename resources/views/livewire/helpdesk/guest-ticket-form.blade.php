<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        {{-- ISO Compliance Header --}}
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Submit Helpdesk Ticket') }}
            </h1>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-mono font-semibold">PK.(S).MOTAC.07.(L1)</span>
            </div>
        </div>

        @if ($submitted)
            {{-- Success Message --}}
            <x-ui.card>
                <div class="text-center py-8">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-100">
                        <svg class="h-8 w-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Ticket Submitted Successfully!') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Your ticket number is') }}: <span class="font-mono font-bold text-primary-600">{{ $ticketNumber }}</span>
                    </p>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('A confirmation email has been sent to') }}: {{ $guest_email }}
                    </p>
                    <div class="mt-6">
                        <x-ui.button wire:click="resetForm">
                            {{ __('Submit Another Ticket') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        @else
            {{-- Progress Indicator --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    @foreach(range(1, $totalSteps) as $step)
                        <div class="flex-1 {{ $loop->first ? '' : 'border-t-2 ' . ($currentStep >= $step ? 'border-primary-600' : 'border-gray-300') }}">
                            <div class="relative flex items-center justify-center">
                                <div @class([
                                    'flex h-10 w-10 items-center justify-center rounded-full border-2 bg-white',
                                    'border-primary-600 text-primary-600' => $currentStep >= $step,
                                    'border-gray-300 text-gray-500' => $currentStep < $step,
                                ])>
                                    @if($currentStep > $step)
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        {{ $step }}
                                    @endif
                                </div>
                                <div class="absolute top-full mt-2 text-xs font-medium text-gray-600">
                                    {{ match($step) {
                                        1 => __('Personal Info'),
                                        2 => __('Issue Details'),
                                        3 => __('Declaration'),
                                    } }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Form Steps --}}
            <x-ui.card>
                <form wire:submit="submit">
                    {{-- Step 1: Personal Information --}}
                    @if($currentStep === 1)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Personal Information') }}</h2>

                            <x-form.input 
                                wire:model.live.debounce.300ms="guest_name" 
                                label="{{ __('Full Name') }}" 
                                required 
                            />

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input 
                                    type="email"
                                    wire:model.live.debounce.300ms="guest_email" 
                                    label="{{ __('Email Address') }}" 
                                    required 
                                />

                                <x-form.input 
                                    wire:model.live.debounce.300ms="guest_phone" 
                                    label="{{ __('Phone Number') }}" 
                                    required 
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input 
                                    wire:model.live.debounce.300ms="guest_staff_id" 
                                    label="{{ __('Staff ID') }}" 
                                    helper="{{ __('Optional if you are a MOTAC staff') }}"
                                />

                                <x-form.input 
                                    wire:model.live.debounce.300ms="job_grade" 
                                    label="{{ __('Job Grade') }}" 
                                    required 
                                />
                            </div>

                            {{-- Searchable Division Select --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Division') }} <span class="text-danger-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.live="divisionSearch" 
                                    placeholder="{{ __('Search division...') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white min-h-44 mb-2"
                                />
                                <select 
                                    wire:model.live="division_id" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white min-h-44"
                                    size="5"
                                >
                                    <option value="">{{ __('Select a division') }}</option>
                                    @foreach($this->divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Issue Details --}}
                    @if($currentStep === 2)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Issue Details') }}</h2>

                            <x-form.select 
                                wire:model.live="category_id" 
                                label="{{ __('Issue Category') }}" 
                                required
                            >
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select 
                                wire:model.live="priority" 
                                label="{{ __('Priority') }}" 
                                required
                            >
                                <option value="low">{{ __('Low') }}</option>
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </x-form.select>

                            <x-form.input 
                                wire:model.live.debounce.300ms="subject" 
                                label="{{ __('Subject') }}" 
                                required 
                            />

                            <x-form.textarea 
                                wire:model.live.debounce.300ms="description" 
                                label="{{ __('Problem Description') }}" 
                                rows="6"
                                helper="{{ __('Please describe the issue in detail (minimum 10 characters)') }}"
                                required 
                            />

                            {{-- File Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Attachments') }} <span class="text-sm text-gray-500">({{ __('Maximum 5 files') }})</span>
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="attachments" 
                                    multiple 
                                    max="5"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                />
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror

                                @if($attachments)
                                    <div class="mt-2 space-y-1">
                                        @foreach($attachments as $index => $attachment)
                                            <div class="text-sm text-gray-600">
                                                {{ $attachment->getClientOriginalName() }} ({{ number_format($attachment->getSize() / 1024, 2) }} KB)
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div wire:loading wire:target="attachments" class="mt-2 text-sm text-primary-600">
                                    {{ __('Uploading...') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Declaration --}}
                    @if($currentStep === 3)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Declaration') }}</h2>

                            {{-- Review Summary --}}
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Review Your Submission') }}</h3>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Name') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $guest_name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Email') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $guest_email }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Subject') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $subject }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Mandatory Declaration -

 Legacy Text --}}
                            <div class="rounded-lg border-2 border-warning-300 bg-warning-50 dark:bg-warning-900/20 p-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                    <strong>Perakuan:</strong><br>
                                    Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam eBorang Laporan Kerosakan ini adalah benar dan tepat. 
                                    Saya faham bahawa maklumat palsu boleh menyebabkan tindakan tatatertib diambil terhadap saya.
                                </p>

                                <x-form.checkbox 
                                    wire:model.live="declaration_accepted" 
                                    label="{{ __('I accept the above declaration') }}" 
                                    required 
                                />
                            </div>
                        </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div class="mt-8 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div>
                            @if($currentStep > 1)
                                <x-ui.button 
                                    type="button" 
                                    variant="secondary" 
                                    wire:click="previousStep"
                                >
                                    {{ __('Previous') }}
                                </x-ui.button>
                            @endif
                        </div>

                        <div>
                            @if($currentStep < $totalSteps)
                                <x-ui.button 
                                    type="button" 
                                    wire:click="nextStep"
                                >
                                    {{ __('Next') }}
                                </x-ui.button>
                            @else
                                <x-ui.button 
                                    type="submit" 
                                    variant="success"
                                    :loading="$isSubmitting"
                                >
                                    {{ __('Submit Ticket') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </form>
            </x-ui.card>
        @endif
    </div>
</div>
