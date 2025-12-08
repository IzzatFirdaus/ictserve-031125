<?php

use App\Models\LoanApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use function Livewire\Volt\{state, mount, computed};

state([
    'tracking_number' => '',
    'email' => '',
    'application' => null,
    'error' => null,
]);

mount(function () {
    // Check if tracking token is in URL
    $token = request()->query('token');
    if ($token) {
        $this->loadApplicationByToken($token);
    }
});

$loadApplicationByToken = function (string $token) {
    try {
        $application = LoanApplication::where('tracking_token', $token)->where('tracking_token_expires_at', '>', now())->first();

        if (!$application) {
            $this->error = 'Invalid or expired tracking link. Please use the tracking form below.';
            return;
        }

        $this->application = $application->load(['loanItems.asset', 'division']);
        $this->tracking_number = $application->application_number;
        $this->email = $application->applicant_email;
    } catch (\Exception $e) {
        $this->error = 'An error occurred while loading your application.';
        \Log::error('Tracking token error', ['error' => $e->getMessage()]);
    }
};

$trackApplication = function () {
    $this->validate([
        'tracking_number' => 'required|string',
        'email' => 'required|email',
    ]);

    try {
        $application = LoanApplication::where('application_number', $this->tracking_number)->where('applicant_email', $this->email)->first();

        if (!$application) {
            $this->error = 'No application found with the provided details. Please check your application number and email address.';
            $this->application = null;
            return;
        }

        $this->application = $application->load(['loanItems.asset', 'division']);
        $this->error = null;
    } catch (\Exception $e) {
        $this->error = 'An error occurred while searching for your application.';
        \Log::error('Application tracking error', ['error' => $e->getMessage()]);
    }
};

$statusColor = computed(function () {
    if (!$this->application) {
        return 'gray';
    }

    return match ($this->application->status->value) {
        'draft' => 'gray',
        'submitted', 'under_review' => 'blue',
        'pending_info' => 'yellow',
        'approved', 'ready_issuance' => 'green',
        'rejected' => 'red',
        'issued', 'in_use' => 'purple',
        'returning', 'returned', 'completed' => 'green',
        'overdue' => 'red',
        default => 'gray',
    };
});

?>

{{--
    Track Loan Application View - MyDS Design System v2025.2
    @trace D13 §2.2-2.7 - MyDS Design Tokens
    @wcag WCAG 2.2 AA compliant
--}}
@php
    $sectionCardClasses = 'rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-card';
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-navigation.skip-links />

        {{-- Standardized Header --}}
        <div class="{{ $sectionCardClasses }} mb-6 space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-primary-600 dark:text-primary-400 font-semibold">
                        {{ __('Asset Loan') }}
                    </p>
                    <h1 id="form-heading" class="text-2xl font-heading font-bold text-gray-900 dark:text-white">
                        {{ __('Jejak Status Permohonan Pinjaman') }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Masukkan nombor permohonan dan emel untuk melihat status terkini permohonan anda.') }}
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-mono text-gray-600 dark:text-gray-400">
                        PK.(S).MOTAC.07.(L3)
                    </span>
                </div>
            </div>
        </div>

        @if (!$application)
            <div class="{{ $sectionCardClasses }} mb-8">
                <form wire:submit="trackApplication" class="space-y-6" novalidate
                    aria-label="{{ __('loan.track_application_form') }}">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-form.input name="tracking_number" label="{{ __('Nombor Permohonan') }}"
                            wire:model.live.debounce.300ms="tracking_number" required autocomplete="off"
                            placeholder="LA202511XXXX" />

                        <x-form.input name="email" type="email" label="{{ __('Emel Pemohon') }}"
                            wire:model.live.debounce.300ms="email" required autocomplete="email"
                            placeholder="nama@motac.gov.my" />

                    </div>

                    <div class="flex items-center justify-end">
                        <x-ui.button type="submit" icon="heroicon-o-magnifying-glass" :disabled="count($errors) > 0">
                            {{ __('Jejak Permohonan') }}
                        </x-ui.button>
                </form>
            </div>
        @endif

        @if ($error)
            <x-alert variant="danger" class="mb-8" icon="heroicon-o-exclamation-circle">
                {{ $error }}
            </x-alert>
        @endif

        @if ($application)
            <div class="{{ $sectionCardClasses }} space-y-6" aria-live="polite">
                <header>
                    <h2 class="text-2xl font-heading font-semibold text-gray-900 dark:text-white">
                        {{ $application->application_number }}
                    </h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ __('Status semasa:') }}
                        <span
                            class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900/30 px-3 py-1 text-sm font-medium text-primary-700 dark:text-primary-300">
                            {{ $application->status->getLabel() }}
                        </span>
                    </p>
                </header>
                <section aria-label="{{ __('Maklumat Pemohon') }}">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Maklumat Pemohon') }}
                    </h3>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nama') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $application->applicant_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Emel') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $application->applicant_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Telefon') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $application->applicant_phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bahagian') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">
                                {{ $application->division?->name ?? __('Tidak dinyatakan') }}</dd>
                        </div>
                    </dl>
                </section>

                <section aria-label="{{ __('Tempoh Pinjaman') }}">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Tempoh Pinjaman') }}
                    </h3>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tarikh Mula') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">
                                {{ $application->loan_start_date->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tarikh Tamat') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">
                                {{ $application->loan_end_date->translatedFormat('d M Y') }}</dd>
                        </div>
                    </dl>
                </section>

                @if ($application->loanItems->count() > 0)
                    <section aria-label="{{ __('Senarai Peralatan') }}">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('Senarai Peralatan') }}
                        </h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Item') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Jenis') }}</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Keadaan') }}</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($application->loanItems as $item)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->asset->name ?? __('Tidak dinyatakan') }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->equipment_type ?? __('Tidak dinyatakan') }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->condition_before ?? __('Tidak dinyatakan') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif

                <section aria-label="{{ __('Tujuan Pinjaman') }}" class="space-y-3">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white">
                        {{ __('Tujuan Pinjaman') }}
                    </h3>
                    <div class="rounded-lg bg-gray-100 dark:bg-gray-700 p-4 text-gray-700 dark:text-gray-300">
                        {{ $application->purpose }}
                    </div>
                </section>
            </div>
        @endif

        <div class="mt-6">
            <x-iso-document-footer />
        </div>
    </div>
</div>
