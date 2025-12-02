@php
    $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag();
@endphp

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-navigation.skip-links />

        {{-- Header Section --}}
        <div class="mb-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <x-heroicon-o-magnifying-glass class="w-8 h-8 text-blue-600" />
                </div>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ __('status.title') }}
            </h1>
            <p class="mt-2 text-gray-600 max-w-2xl mx-auto">
                {{ __('status.subtitle') }}
            </p>
        </div>

        {{-- Search Form --}}
        <x-ui.card class="mb-8">
            <form wire:submit.prevent="checkStatus" class="space-y-6" novalidate aria-label="{{ __('status.form_label') }}">
                <div class="space-y-4">
                    {{-- Token Input --}}
                    <div>
                        <label for="token" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('status.token_label') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="token"
                                wire:model.live.debounce.300ms="token"
                                class="block w-full px-4 py-3 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ $errorBag->has('token') ? 'border-red-500' : 'border-gray-300' }}"
                                placeholder="{{ __('status.token_placeholder') }}"
                                autocomplete="off"
                                aria-describedby="token-help token-error"
                                required
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <x-heroicon-o-key class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                        <p id="token-help" class="mt-1 text-sm text-gray-500">
                            {{ __('status.token_help') }}
                        </p>
                        @error('token')
                            <p id="token-error" class="mt-1 text-sm text-red-600" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Type Selection (Optional) --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('status.type_label') }}
                        </label>
                        <select
                            id="type"
                            wire:model="type"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            aria-describedby="type-help"
                        >
                            <option value="auto">{{ __('status.type_auto') }}</option>
                            <option value="ticket">{{ __('status.type_ticket') }}</option>
                            <option value="loan">{{ __('status.type_loan') }}</option>
                        </select>
                        <p id="type-help" class="mt-1 text-sm text-gray-500">
                            {{ __('status.type_help') }}
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    @if ($showResults)
                        <p class="text-sm text-gray-500">
                            {{ __('status.last_updated') }}
                            <span class="font-medium text-gray-700">{{ now()->translatedFormat('d M Y, h:i A') }}</span>
                        </p>
                    @else
                        <div></div>
                    @endif

                    <div class="flex gap-3">
                        @if ($showResults || $notFound)
                            <button
                                type="button"
                                wire:click="clearSearch"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                                {{ __('status.clear') }}
                            </button>
                        @endif

                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="checkStatus"
                        >
                            <span wire:loading.remove wire:target="checkStatus">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 mr-2" />
                                {{ __('status.check_button') }}
                            </span>
                            <span wire:loading wire:target="checkStatus" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('status.checking') }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </x-ui.card>

        {{-- Error Message --}}
        @if ($notFound)
            <div class="mb-8 rounded-lg bg-red-50 border border-red-200 p-4" role="alert" aria-live="polite">
                <div class="flex">
                    <div class="shrink-0">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ __('status.not_found_title') }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ __('status.not_found_message') }}</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                <li>{{ __('status.not_found_hint_1') }}</li>
                                <li>{{ __('status.not_found_hint_2') }}</li>
                                <li>{{ __('status.not_found_hint_3') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Results Section --}}
        @if ($showResults && $submission)
            <div class="space-y-6" aria-live="polite">
                {{-- Submission Header --}}
                <x-ui.card>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @if ($foundType === 'ticket')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <x-heroicon-o-ticket class="w-3 h-3 mr-1" />
                                        {{ __('status.type_ticket') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-clipboard-document-list class="w-3 h-3 mr-1" />
                                        {{ __('status.type_loan') }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-xl font-semibold text-gray-900">
                                @if ($foundType === 'ticket')
                                    {{ $submission->subject ?? $submission->ticket_number }}
                                @else
                                    {{ __('status.loan_reference', ['ref' => $submission->application_number]) }}
                                @endif
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                @if ($foundType === 'ticket')
                                    {{ __('status.ticket_number') }}: <span class="font-mono font-medium">{{ $submission->ticket_number }}</span>
                                @else
                                    {{ __('status.submitted_on', ['date' => $submission->created_at->translatedFormat('d M Y')]) }}
                                @endif
                            </p>
                        </div>

                        <div class="shrink-0">
                            @if ($foundType === 'ticket')
                                @php
                                    $statusClass = match($submission->status) {
                                        'open', 'new' => 'bg-yellow-100 text-yellow-800',
                                        'assigned', 'in_progress' => 'bg-blue-100 text-blue-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">
                                    {{ \Illuminate\Support\Str::headline($submission->status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $submission->status->color() }}-100 text-{{ $submission->status->color() }}-800">
                                    {{ $submission->status->label() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Submission Details --}}
                    <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @if ($foundType === 'ticket')
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.category') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $submission->category?->name ?? __('status.not_specified') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.priority') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \Illuminate\Support\Str::headline($submission->priority ?? 'normal') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.division') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $submission->division?->name ?? $submission->guest_division ?? __('status.not_specified') }}</dd>
                            </div>
                        @else
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.applicant') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $submission->applicant_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.loan_period') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $submission->loan_start_date->format('d M Y') }} - {{ $submission->loan_end_date?->format('d M Y') ?? $submission->expected_return_date?->format('d M Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('status.location') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $submission->location ?? __('status.not_specified') }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-ui.card>

                {{-- Timeline Section --}}
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        {{ __('status.timeline_title') }}
                    </h3>

                    <ol class="relative border-l-2 border-blue-200 space-y-8 ml-3">
                        @forelse ($timeline as $event)
                            <li class="ml-6">
                                @php
                                    $markerClass = $event['current'] 
                                        ? 'border-blue-600 bg-blue-600'
                                        : ($event['completed'] ? 'border-blue-600 bg-white' : 'border-gray-300 bg-white');
                                @endphp
                                <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border-2 {{ $markerClass }}">
                                    @if ($event['current'])
                                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                    @elseif ($event['completed'])
                                        <x-heroicon-s-check class="h-3.5 w-3.5 text-blue-600" />
                                    @else
                                        <span class="h-2 w-2 rounded-full bg-gray-300"></span>
                                    @endif
                                </span>

                                @php
                                    $cardClass = $event['current']
                                        ? 'border-blue-300 bg-blue-50 shadow-sm ring-1 ring-blue-200'
                                        : 'border-gray-200 bg-white';
                                @endphp
                                <div class="rounded-lg border p-4 transition-all {{ $cardClass }}">
                                    <h4 class="text-base font-semibold text-gray-900 flex items-center gap-2 flex-wrap">
                                        {{ $event['label'] }}
                                        @if ($event['current'])
                                            <span class="inline-flex items-center rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-medium text-white">
                                                {{ __('status.current_status') }}
                                            </span>
                                        @endif
                                    </h4>

                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $event['description'] }}
                                    </p>

                                    @if ($event['time'])
                                        <p class="mt-3 text-xs uppercase tracking-wide text-gray-500">
                                            {{ $event['time'] }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="ml-6 text-sm text-gray-600">
                                {{ __('status.no_timeline') }}
                            </li>
                        @endforelse
                    </ol>
                </x-ui.card>

                {{-- Public Comments Section --}}
                @if (count($publicComments) > 0)
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ __('status.comments_title') }}
                        </h3>

                        <div class="space-y-4">
                            @foreach ($publicComments as $comment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $comment['author'] }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment['created_at'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $comment['content'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-ui.card>
                @endif

                {{-- Loan Items Section (for loans only) --}}
                @if ($foundType === 'loan' && $submission->loanItems->count() > 0)
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ __('status.loan_items_title') }}
                        </h3>

                        <ul class="divide-y divide-gray-200 rounded-lg border border-gray-200 overflow-hidden">
                            @foreach ($submission->loanItems as $item)
                                <li class="px-4 py-3 flex justify-between items-center bg-white hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <x-heroicon-o-computer-desktop class="w-5 h-5 text-gray-400" />
                                        <span class="text-sm text-gray-900">
                                            {{ $item->asset?->name ?? $item->asset_category_name ?? __('status.unknown_item') }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">
                                        x{{ $item->quantity ?? 1 }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </x-ui.card>
                @endif

                {{-- Description Section (for tickets only) --}}
                @if ($foundType === 'ticket' && $submission->description)
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ __('status.description_title') }}
                        </h3>

                        <div class="rounded-lg bg-gray-100 p-4 text-gray-700">
                            {{ $submission->description }}
                        </div>

                        @if ($submission->resolution_notes)
                            <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                                <p class="text-sm font-semibold text-emerald-700 mb-2">
                                    {{ __('status.resolution_notes') }}
                                </p>
                                <p class="text-sm text-emerald-800">{{ $submission->resolution_notes }}</p>
                            </div>
                        @endif
                    </x-ui.card>
                @endif
            </div>
        @endif

        {{-- Help Section --}}
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                {{ __('status.help_text') }}
                <a href="{{ route('helpdesk.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    {{ __('status.contact_support') }}
                </a>
            </p>
        </div>
    </div>
</div>
