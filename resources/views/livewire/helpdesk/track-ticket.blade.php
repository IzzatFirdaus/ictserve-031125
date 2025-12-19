{{--
    Track Ticket View - MyDS Design System v2025.2
    @trace D13 §2.2-2.7 - MyDS Design Tokens
    @wcag WCAG 2.2 AA compliant
--}}
@php
    $sectionCardClasses =
        'rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-card';
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-navigation.skip-links />

        {{-- Standardized Header --}}
        <div class="{{ $sectionCardClasses }} mb-6 space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-primary-600 dark:text-primary-400 font-semibold">
                        {{ __('ICT Support') }}
                    </p>
                    <h1 id="form-heading" class="text-2xl font-heading font-bold text-slate-900 dark:text-white">
                        {{ __('Jejak Status Tiket ICTServe') }}
                    </h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ __('Masukkan nombor tiket dan emel untuk melihat status terkini permohonan anda.') }}
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <span
                        class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs font-mono text-slate-600 dark:text-slate-400">
                        PK.(S).MOTAC.07.(L1)
                    </span>
                </div>
            </div>
        </div>

        <div class="{{ $sectionCardClasses }} mb-8">
            <form wire:submit.prevent="track" class="space-y-6" novalidate
                aria-label="{{ __('helpdesk.track_ticket_form') }}">
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form.input name="ticketNumber" label="{{ __('Nombor Tiket') }}"
                        wire:model.live.debounce.300ms="ticketNumber" required autocomplete="off"
                        placeholder="HD2025000001" />

                    <x-form.input name="email" type="email" label="{{ __('Emel Pendaftar') }}"
                        wire:model.live.debounce.300ms="email" required autocomplete="email"
                        placeholder="nama@motac.gov.my" />
                </div>

                <div class="flex items-center justify-between">
                    @if ($showResults)
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ __('Maklumat dikemaskini pada') }}
                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                {{ now()->translatedFormat('d M Y, h:i A') }}
                            </span>
                        </p>
                    @else
                        <div></div>
                    @endif

                    <x-ui.button type="submit" icon="heroicon-o-magnifying-glass" :disabled="count($errors) > 0">
                        {{ __('Jejak Tiket') }}
                    </x-ui.button>
                </div>
            </form>
        </div>

        @if ($notFound)
            <x-alert variant="danger" class="mb-8" icon="heroicon-o-exclamation-circle">
                {{ __('Kami tidak menjumpai tiket yang sepadan. Sila pastikan nombor tiket dan emel adalah tepat.') }}
            </x-alert>
        @endif

        @if ($showResults && $ticket)
            <div class="{{ $sectionCardClasses }} space-y-6" aria-live="polite">
                <header>
                    <h2 class="text-2xl font-heading font-semibold text-slate-900 dark:text-white"></h2>
                    {{ $ticket->subject }}
                    </h2>
                    <p class="mt-1 text-slate-600 dark:text-slate-400">
                        {{ __('Status semasa:') }}
                        <span
                            class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900/30 px-3 py-1 text-sm font-medium text-primary-700 dark:text-primary-300">
                            {{ \Illuminate\Support\Str::headline($ticket->status) }}
                        </span>
                    </p>
                </header>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Nombor Tiket') }}</dt>
                        <dd class="mt-1 text-slate-900 dark:text-white">{{ $ticket->ticket_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Kategori') }}</dt>
                        <dd class="mt-1 text-slate-900 dark:text-white">
                            {{ $ticket->category?->name ?? __('Tidak dinyatakan') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Dicipta') }}</dt>
                        <dd class="mt-1 text-slate-900 dark:text-white">
                            {{ $ticket->created_at?->translatedFormat('d M Y, h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Bahagian Pemohon') }}
                        </dt>
                        <dd class="mt-1 text-slate-900 dark:text-white">
                            {{ $ticket->division?->name ?? ($ticket->guest_division ?? __('Tidak dinyatakan')) }}</dd>
                    </div>
                </dl>

                <section aria-label="{{ __('Garis Masa Tiket') }}">
                    <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-white mb-4">
                        {{ __('Perjalanan Permohonan') }}
                    </h3>

                    <ol class="relative border-l border-primary-200 dark:border-primary-800 space-y-8 pl-6">
                        @forelse ($timeline as $event)
                            <li>
                                <span
                                    class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-primary-300 dark:border-primary-700 bg-white dark:bg-slate-800">
                                    <span @class([
                                        'h-3 w-3 rounded-full',
                                        'bg-primary-600' => $event['completed'],
                                        'bg-white dark:bg-slate-800 border border-primary-300 dark:border-primary-700' => !$event[
                                            'completed'
                                        ],
                                    ])></span>
                                </span>

                                <div @class([
                                    'rounded-lg border p-4 transition-colors duration-200',
                                    'border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/30 shadow-card' =>
                                        $event['current'],
                                    'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' => !$event[
                                        'current'
                                    ],
                                ])>
                                    <h4
                                        class="text-base font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                        {{ $event['label'] }}
                                        @if ($event['current'])
                                            <span
                                                class="inline-flex items-center rounded-full bg-primary-600 px-2.5 py-0.5 text-xs font-medium text-white">
                                                {{ __('Status Semasa') }}
                                            </span>
                                        @endif
                                    </h4>

                                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                        {{ $event['description'] }}
                                    </p>

                                    @if ($event['time'])
                                        <p
                                            class="mt-3 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                            {{ $event['time'] }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-600 dark:text-slate-400">
                                {{ __('Tiada maklumat garis masa tersedia pada masa ini.') }}
                            </li>
                        @endforelse
                    </ol>
                </section>

                <section aria-label="{{ __('Butiran Aduan') }}" class="space-y-3">
                    <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-white">
                        {{ __('Butiran Aduan') }}
                    </h3>

                    <div class="rounded-lg bg-slate-100 dark:bg-slate-700 p-4 text-slate-700 dark:text-slate-300">
                        {{ $ticket->description }}
                    </div>

                    @if ($ticket->resolution_notes)
                        <div class="rounded-lg bg-success-50 dark:bg-success-900/30 p-4">
                            <p class="text-sm font-semibold text-success-700 dark:text-success-300">
                                {{ __('Nota Penyelesaian') }}
                            </p>
                            <p class="mt-2 text-sm text-success-800 dark:text-success-200">
                                {{ $ticket->resolution_notes }}
                            </p>
                        </div>
                    @endif
                </section>
            </div>
        @endif

        <div class="mt-6">
            <x-iso-document-footer />
        </div>
    </div>
</div>
