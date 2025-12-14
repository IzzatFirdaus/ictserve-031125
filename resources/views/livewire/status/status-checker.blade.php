@php
    $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag();
@endphp

<div class="bg-slate-50 dark:bg-gray-900 min-h-screen">

    {{-- Hero Section (light default, optional dark) --}}
    <section class="bg-primary-600 dark:bg-primary-700 text-white py-10 md:py-14 theme-transition" role="banner"
        aria-labelledby="status-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0"
                    aria-hidden="true">
                    <x-heroicon-o-magnifying-glass class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-sm text-blue-100 font-semibold tracking-wide uppercase">
                        {{ __('status.page_tagline') ?? 'Status Semasa' }}</p>
                    <h1 id="status-heading" class="text-3xl md:text-4xl font-heading font-bold tracking-tight">
                        {{ __('status.title') }}</h1>
                </div>
            </div>
            <p class="text-base md:text-lg text-blue-50 max-w-2xl leading-relaxed">{{ __('status.subtitle') }}</p>
        </div>
    </section>

    {{-- Main Content --}}
    <section id="main-content" class="py-12 md:py-16" aria-labelledby="form-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                <div class="col-span-4 md:col-span-8 lg:col-span-8">
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-card dark:shadow-dropdown rounded-2xl p-6 md:p-8 theme-transition">
                        <div class="flex items-start gap-3 mb-6">
                            <div class="h-10 w-10 rounded-full bg-primary-50 dark:bg-primary-900/40 flex items-center justify-center"
                                aria-hidden="true">
                                <x-heroicon-o-key class="w-5 h-5 text-primary-600 dark:text-primary-300" />
                            </div>
                            <div>
                                <h2 id="form-heading"
                                    class="text-2xl font-heading font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('status.form_label') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ __('status.form_helper') ?? __('status.subtitle') }}</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="checkStatus" class="space-y-6" novalidate
                            aria-label="{{ __('status.form_label') }}">
                            <div class="space-y-5">
                                {{-- Token Input --}}
                                <div>
                                    <label for="token"
                                        class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">
                                        {{ __('status.token_label') }}
                                        <span class="text-danger-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="token" wire:model.live.debounce.300ms="token"
                                            class="block w-full px-4 py-3 rounded-lg border {{ $errorBag->has('token') ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500' : 'border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500' }} bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm transition-colors"
                                            placeholder="{{ __('status.token_placeholder') }}" autocomplete="off"
                                            aria-describedby="token-help token-error" required />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3"
                                            aria-hidden="true">
                                            <x-heroicon-o-shield-check
                                                class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                                        </div>
                                    </div>
                                    <p id="token-help" class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ __('status.token_help') }}
                                    </p>
                                    @error('token')
                                        <p id="token-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400"
                                            role="alert">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Type Selection (Optional) --}}
                                <div>
                                    <label for="type"
                                        class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">
                                        {{ __('status.type_label') }}
                                    </label>
                                    <select id="type" wire:model="type"
                                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        aria-describedby="type-help">
                                        <option value="auto">{{ __('status.type_auto') }}</option>
                                        <option value="ticket">{{ __('status.type_ticket') }}</option>
                                        <option value="loan">{{ __('status.type_loan') }}</option>
                                    </select>
                                    <p id="type-help" class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ __('status.type_help') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                @if ($showResults)
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ __('status.last_updated') }}
                                        <span
                                            class="font-medium text-gray-800 dark:text-gray-100">{{ now()->translatedFormat('d M Y, h:i A') }}</span>
                                    </p>
                                @else
                                    <div></div>
                                @endif

                                <div class="flex gap-3 flex-wrap">
                                    @if ($showResults || $notFound)
                                        <button type="button" wire:click="clearSearch"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-semibold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 min-h-11">
                                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                                            {{ __('status.clear') }}
                                        </button>
                                    @endif

                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 min-h-11 shadow-button disabled:opacity-60 disabled:cursor-not-allowed"
                                        wire:loading.attr="disabled" wire:target="checkStatus">
                                        <span wire:loading.remove wire:target="checkStatus"
                                            class="flex items-center gap-2">
                                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                            {{ __('status.check_button') }}
                                        </span>
                                        <span wire:loading wire:target="checkStatus" class="flex items-center gap-2">
                                            <x-heroicon-o-arrow-path class="animate-spin -ml-1 h-4 w-4 text-white" />
                                            {{ __('status.checking') }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Error Message --}}
                    @if ($notFound)
                        <div class="mt-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4"
                            role="alert" aria-live="polite">
                            <div class="flex">
                                <div class="shrink-0">
                                    <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-500" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">
                                        {{ __('status.not_found_title') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-200 space-y-1">
                                        <p>{{ __('status.not_found_message') }}</p>
                                        <ul class="list-disc list-inside space-y-1">
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
                        <div class="mt-8 space-y-6" aria-live="polite">
                            {{-- Submission Header --}}
                            <div
                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            @if ($foundType === 'ticket')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-100">
                                                    <x-heroicon-o-ticket class="w-3 h-3 mr-1" />
                                                    {{ __('status.type_ticket') }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                                    <x-heroicon-o-clipboard-document-list class="w-3 h-3 mr-1" />
                                                    {{ __('status.type_loan') }}
                                                </span>
                                            @endif
                                        </div>

                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            @if ($foundType === 'ticket')
                                                {{ $submission->subject ?? $submission->ticket_number }}
                                            @else
                                                {{ __('status.loan_reference', ['ref' => $submission->application_number]) }}
                                            @endif
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                            @if ($foundType === 'ticket')
                                                {{ __('status.ticket_number') }}: <span
                                                    class="font-mono font-medium">{{ $submission->ticket_number }}</span>
                                            @else
                                                {{ __('status.submitted_on', ['date' => $submission->created_at->translatedFormat('d M Y')]) }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="shrink-0">
                                        @if ($foundType === 'ticket')
                                            @php
                                                $statusClass = match ($submission->status) {
                                                    'open',
                                                    'new'
                                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                                                    'assigned',
                                                    'in_progress'
                                                        => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                                    'resolved'
                                                        => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                                                    default
                                                        => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">
                                                {{ \Illuminate\Support\Str::headline($submission->status) }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $submission->status->color() }}-100 text-{{ $submission->status->color() }}-800 dark:bg-{{ $submission->status->color() }}-900/30 dark:text-{{ $submission->status->color() }}-100">
                                                {{ $submission->status->label() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Submission Details --}}
                                <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @if ($foundType === 'ticket')
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.category') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $submission->category?->name ?? __('status.not_specified') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.priority') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ \Illuminate\Support\Str::headline($submission->priority ?? 'normal') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.division') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $submission->division?->name ?? ($submission->guest_division ?? __('status.not_specified')) }}
                                            </dd>
                                        </div>
                                    @else
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.applicant') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $submission->applicant_name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.loan_period') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $submission->loan_start_date->format('d M Y') }} -
                                                {{ $submission->loan_end_date?->format('d M Y') ?? $submission->expected_return_date?->format('d M Y') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                {{ __('status.location') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $submission->location ?? __('status.not_specified') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- Timeline Section --}}
                            <div
                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                                    {{ __('status.timeline_title') }}
                                </h3>

                                <ol class="relative border-l-2 border-blue-200 dark:border-blue-900/50 space-y-8 ml-3">
                                    @forelse ($timeline as $event)
                                        <li class="ml-6">
                                            @php
                                                $markerClass = $event['current']
                                                    ? 'border-blue-600 bg-blue-600'
                                                    : ($event['completed']
                                                        ? 'border-blue-600 bg-white dark:bg-blue-900/40'
                                                        : 'border-gray-300 bg-white dark:bg-gray-800');
                                            @endphp
                                            <span
                                                class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border-2 {{ $markerClass }}">
                                                @if ($event['current'])
                                                    <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                                @elseif ($event['completed'])
                                                    <x-heroicon-s-check class="h-3.5 w-3.5 text-blue-600" />
                                                @else
                                                    <span
                                                        class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-500"></span>
                                                @endif
                                            </span>

                                            @php
                                                $cardClass = $event['current']
                                                    ? 'border-blue-300 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 shadow-sm ring-1 ring-blue-200/80'
                                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800';
                                            @endphp
                                            <div class="rounded-lg border p-4 transition-all {{ $cardClass }}">
                                                <h4
                                                    class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2 flex-wrap">
                                                    {{ $event['label'] }}
                                                    @if ($event['current'])
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-medium text-white">
                                                            {{ __('status.current_status') }}
                                                        </span>
                                                    @endif
                                                </h4>

                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $event['description'] }}
                                                </p>

                                                @if ($event['time'])
                                                    <p
                                                        class="mt-3 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                        {{ $event['time'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <li class="ml-6 text-sm text-gray-600 dark:text-gray-300">
                                            {{ __('status.no_timeline') }}
                                        </li>
                                    @endforelse
                                </ol>
                            </div>

                            {{-- Public Comments Section --}}
                            @if (count($publicComments) > 0)
                                <div
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                        {{ __('status.comments_title') }}
                                    </h3>

                                    <div class="space-y-4">
                                        @foreach ($publicComments as $comment)
                                            <div
                                                class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $comment['author'] }}</span>
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $comment['created_at'] }}</span>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-200">
                                                    {{ $comment['content'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Loan Items Section (for loans only) --}}
                            @if ($foundType === 'loan' && $submission->loanItems->count() > 0)
                                <div
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                        {{ __('status.loan_items_title') }}
                                    </h3>

                                    <ul
                                        class="divide-y divide-gray-200 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                        @foreach ($submission->loanItems as $item)
                                            <li
                                                class="px-4 py-3 flex justify-between items-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                                <div class="flex items-center gap-3">
                                                    <x-heroicon-o-computer-desktop
                                                        class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                                                    <span class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $item->asset?->name ?? ($item->asset_category_name ?? __('status.unknown_item')) }}
                                                    </span>
                                                </div>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                    x{{ $item->quantity ?? 1 }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Description Section (for tickets only) --}}
                            @if ($foundType === 'ticket' && $submission->description)
                                <div
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                        {{ __('status.description_title') }}
                                    </h3>

                                    <div
                                        class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 p-4 text-gray-700 dark:text-gray-200">
                                        {{ $submission->description }}
                                    </div>

                                    @if ($submission->resolution_notes)
                                        <div
                                            class="mt-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 p-4">
                                            <p
                                                class="text-sm font-semibold text-emerald-700 dark:text-emerald-200 mb-2">
                                                {{ __('status.resolution_notes') }}
                                            </p>
                                            <p class="text-sm text-emerald-800 dark:text-emerald-100">
                                                {{ $submission->resolution_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Help Section --}}
                    <div class="mt-10 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ __('status.help_text') }}
                            <a href="{{ route('helpdesk.create') }}"
                                class="text-primary-600 dark:text-primary-300 font-semibold hover:underline focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 rounded-sm">
                                {{ __('status.contact_support') }}
                            </a>
                        </p>
                    </div>
                </div>

                {{-- Quick Help Card --}}
                <aside class="col-span-4 md:col-span-8 lg:col-span-4 space-y-4">
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-card dark:shadow-dropdown p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            {{ __('status.quick_help_title') ?? 'Bantuan Pantas' }}</h3>
                        <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-200">
                            <li class="flex items-start gap-2">
                                <x-heroicon-o-envelope class="w-5 h-5 text-primary-600 dark:text-primary-300 mt-0.5" />
                                <div>
                                    <p class="font-semibold">helpdesk@motac.gov.my</p>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ __('status.quick_help_email') ?? 'Emel sokongan BPM' }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-o-phone class="w-5 h-5 text-primary-600 dark:text-primary-300 mt-0.5" />
                                <div>
                                    <p class="font-semibold">+603-8891 7000</p>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ __('status.quick_help_phone') ?? 'Talian bantuan helpdesk' }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-o-information-circle
                                    class="w-5 h-5 text-primary-600 dark:text-primary-300 mt-0.5" />
                                <div>
                                    <p class="font-semibold">
                                        {{ __('status.quick_help_ticket') ?? 'Hantar tiket baharu' }}</p>
                                    <a href="{{ route('helpdesk.submit') }}"
                                        class="text-primary-600 dark:text-primary-300 font-semibold hover:underline focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 rounded-sm">{{ __('status.quick_help_ticket_cta') ?? 'Pergi ke borang helpdesk' }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
