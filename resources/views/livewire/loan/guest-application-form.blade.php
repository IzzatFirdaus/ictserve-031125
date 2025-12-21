<?php

use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Services\WorkingDayCalculator;
use App\Services\ResponsibleOfficerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\GuestApplicationTrackingMail;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

new class extends Component {
    // Multi-step wizard state
    public int $currentStep = 1;

    public function nextStep()
    {
        try {
            $this->validateStep($this->currentStep);
            if ($this->currentStep < 5) {
                $this->currentStep++;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Livewire automatically handles displaying validation errors
            // No need to re-throw or add errors manually here unless custom logic is needed
            throw $e;
        }
    }

    protected function validateStep($step)
    {
        if ($step === 1) {
            $this->validate([
                'applicant_name' => 'required|string|max:255',
                'applicant_email' => 'required|email|max:255',
                'applicant_phone' => 'required|string|max:20',
                'applicant_staff_id' => 'required|string|max:20',
                'division_id' => 'required|exists:divisions,id',
                'applicant_position' => 'required|string|max:100',
                'applicant_grade' => 'required|string|max:10',
            ]);
        } elseif ($step === 2) {
            if (!$this->is_applicant_responsible) {
                $this->validate([
                    'responsible_officer_name' => 'required|string|max:255',
                    'responsible_officer_email' => 'required|email|max:255',
                    'responsible_officer_phone' => 'required|string|max:20',
                    'responsible_officer_position' => 'required|string|max:100',
                    'responsible_officer_grade' => 'required|string|max:10',
                ]);
            }
        } elseif ($step === 3) {
            $this->validate(
                [
                    'selected_assets' => 'required|array|min:1',
                ],
                [
                    'selected_assets.required' => 'Sila pilih sekurang-kurangnya satu peralatan.',
                    'selected_assets.min' => 'Sila pilih sekurang-kurangnya satu peralatan.',
                ],
            );
        } elseif ($step === 4) {
            $this->validate([
                'purpose' => 'required|string|max:1000',
                'location' => 'required|string|max:255',
                'loan_start_date' => 'required|date|after:today',
                'loan_end_date' => 'required|date|after_or_equal:loan_start_date',
            ]);
            // 3-Day Rule Validation
            $calculator = new WorkingDayCalculator();
            if (!$calculator->validateLeadTime(now(), $this->loan_start_date, 3)) {
                $nextAvailable = $calculator->getNextAvailableDate(now(), 3)->format('d/m/Y');
                $this->addError('loan_start_date', "Permohonan mesti dibuat sekurang-kurangnya 3 hari bekerja sebelum tarikh pinjaman. Tarikh terawal yang boleh dipilih ialah $nextAvailable.");
                // Prevent moving to next step if validation fails
                throw new \Illuminate\Validation\ValidationException($this->validator);
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    // Applicant Information
    public string $applicant_name = '';

    public string $applicant_email = '';

    public string $applicant_phone = '';

    public string $applicant_staff_id = '';

    public int $division_id;

    public string $applicant_position = '';

    public string $applicant_grade = '';

    // Responsible Officer Delegation
    public bool $is_applicant_responsible = true;

    public string $responsible_officer_name = '';

    public string $responsible_officer_email = '';

    public string $responsible_officer_phone = '';

    public string $responsible_officer_position = '';

    public string $responsible_officer_grade = '';

    // Loan Details
    public string $purpose = '';

    public string $location = '';

    public string $loan_start_date = '';

    public string $loan_end_date = '';

    public string $return_location = 'Pejabat ICTServe';

    // Asset Selection
    public array $selected_assets = [];

    // Declaration
    #[Validate('accepted')]
    public bool $terms_accepted = false;

    // Computed Properties
    public function with(): array
    {
        return [
            'divisions' => Division::all(),
            'assetCategories' => AssetCategory::with([
                'assets' => function ($query) {
                    $query->available()->orderBy('name');
                },
            ])->get(),
        ];
    }

    #[Computed]
    public function totalValue()
    {
        if (empty($this->selected_assets)) {
            return 0;
        }
        return Asset::whereIn('id', $this->selected_assets)->sum('purchase_value');
    }

    // Methods
    public function updatedLoanStartDate($value)
    {
        // 3-Day Rule Validation
        $calculator = new WorkingDayCalculator();
        if (!$calculator->validateLeadTime(now(), $value, 3)) {
            $nextAvailable = $calculator->getNextAvailableDate(now(), 3)->format('d/m/Y');
            $this->addError('loan_start_date', "Permohonan mesti dibuat sekurang-kurangnya 3 hari bekerja sebelum tarikh pinjaman. Tarikh terawal yang boleh dipilih ialah $nextAvailable.");
        } else {
            $this->resetErrorBag('loan_start_date');
        }
    }

    public function save()
    {
        $this->validateStep(1);
        $this->validateStep(2);
        $this->validateStep(3);
        $this->validateStep(4);
        $this->validate([
            'terms_accepted' => 'accepted',
        ]);

        DB::transaction(function () {
            // Create Loan Application
            $application = LoanApplication::create([
                'application_number' => 'LA' . now()->format('Ym') . random_int(1000, 9999),
                'applicant_name' => $this->applicant_name,
                'applicant_email' => $this->applicant_email,
                'applicant_phone' => $this->applicant_phone,
                'staff_id' => $this->applicant_staff_id,
                'division_id' => $this->division_id,
                'applicant_position' => $this->applicant_position,
                'applicant_grade' => $this->applicant_grade,
                'grade' => $this->applicant_grade, // Required for compatibility

                'is_applicant_responsible' => $this->is_applicant_responsible,
                'responsible_officer_name' => $this->is_applicant_responsible ? null : $this->responsible_officer_name,
                'responsible_officer_email' => $this->is_applicant_responsible ? null : $this->responsible_officer_email,
                'responsible_officer_phone' => $this->is_applicant_responsible ? null : $this->responsible_officer_phone,
                'responsible_officer_position' => $this->is_applicant_responsible ? null : $this->responsible_officer_position,
                'responsible_officer_grade' => $this->is_applicant_responsible ? null : $this->responsible_officer_grade,

                'purpose' => $this->purpose,
                'location' => $this->location,
                'loan_start_date' => $this->loan_start_date,
                'loan_end_date' => $this->loan_end_date,
                'expected_return_date' => $this->loan_end_date,
                'return_location' => $this->return_location, // Moved here as requested
                'declared_at' => now(),
                'status' => 'submitted',

                // Tracking token generation
                'tracking_token' => Str::random(32),
                'tracking_token_expires_at' => now()->addDays(30),
            ]);

            // Create Loan Items for selected assets
            foreach ($this->selected_assets as $assetId) {
                $asset = Asset::with('category')->findOrFail($assetId);

                LoanItem::create([
                    'loan_application_id' => $application->id,
                    'asset_id' => $assetId,
                    'equipment_type' => $asset->category->name ?? 'General Equipment',
                    'quantity' => 1,
                    'unit_value' => $asset->purchase_value,
                    'total_value' => $asset->purchase_value,
                    'condition_before' => $asset->condition,
                ]);
            }

            // Update total value (return_location removed from here)
            $application->update([
                'total_value' => $application->loanItems()->sum('total_value'),
            ]);

            // Handle Delegation
            if (!$this->is_applicant_responsible) {
                $service = new ResponsibleOfficerService();
                $service->handleDelegatedApplication($application);
            }

            // Send tracking email
            Mail::to($this->applicant_email)->send(new GuestApplicationTrackingMail($application, url('/loan/track-application?token=' . $application->tracking_token)));

            session()->flash('message', 'Permohonan berjaya dihantar! Sila semak emel anda.');
            $this->reset();
        });
    }
}; ?>

{{-- MyDS Design System v2025.2 | WCAG 2.2 AA | Trace: D13 §2.2-2.7 --}}
@php
    $sectionCardClasses =
        'rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-card';
    $stepLabels = [
        1 => 'Maklumat Pemohon',
        2 => 'Pegawai Bertanggungjawab',
        3 => 'Pilih Peralatan',
        4 => 'Butiran Pinjaman',
        5 => 'Semakan & Perakuan',
    ];
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="{{ $sectionCardClasses }} mb-6 space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-primary-600 dark:text-primary-400 font-semibold">
                        Pinjaman Aset ICT
                    </p>
                    <h1 id="form-heading" class="text-2xl font-heading font-bold text-gray-900 dark:text-white">
                        Borang Permohonan Pinjaman Peralatan ICT
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Lengkapkan semua langkah di bawah untuk menghantar permohonan pinjaman peralatan.
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <span
                        class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-mono text-gray-600 dark:text-gray-400">
                        PK.(S).MOTAC.07.(L3)
                    </span>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="p-4 bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-300 rounded-lg"
                    role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        {{-- Step Indicator --}}
        <div class="{{ $sectionCardClasses }} mb-8">
            <nav aria-label="Progress">
                <ol class="flex items-center justify-between">
                    @foreach (range(1, 5) as $step)
                        <li class="flex-1 {{ $step < 5 ? 'pr-2' : '' }}">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 rounded-full border transition-colors duration-200 min-h-11 min-w-11 text-sm font-semibold shadow-button
                                            {{ $step < $currentStep ? 'bg-success-600 border-success-400/70 text-white' : '' }}
                                            {{ $step === $currentStep ? 'bg-primary-600 border-primary-400/70 text-white ring-2 ring-primary-400/40' : '' }}
                                            {{ $step > $currentStep ? 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500' : '' }}">
                                            @if ($step < $currentStep)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                    aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <span>{{ $step }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($step < 5)
                                        <div class="flex-1 mx-2" aria-hidden="true">
                                            <div
                                                class="h-1 rounded-full transition-colors duration-200 {{ $step < $currentStep ? 'bg-success-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <span
                                    class="mt-2 text-xs font-medium {{ $step === $currentStep ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $stepLabels[$step] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        <form wire:submit="save" class="space-y-6" aria-labelledby="form-heading">
            {{-- Step 1: Applicant Info --}}
            @if ($currentStep === 1)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="applicant-info-heading">
                    <div
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100/80 dark:bg-gray-700/50 px-5 py-4">
                        <h2 id="applicant-info-heading"
                            class="text-lg font-heading font-semibold text-gray-900 dark:text-white">
                            Maklumat Pemohon
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Isi butiran asas pemohon.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input wire:model.live.debounce.300ms="applicant_name" name="applicant_name"
                            label="Nama Penuh" required />
                        <x-form.input wire:model.live.debounce.300ms="applicant_email" name="applicant_email"
                            type="email" label="Emel Rasmi" required />
                        <x-form.input wire:model.live.debounce.300ms="applicant_phone" name="applicant_phone"
                            type="tel" label="No. Telefon" required />
                        <x-form.input wire:model.live.debounce.300ms="applicant_staff_id" name="applicant_staff_id"
                            label="No. Pekerja / No. Kad Pengenalan" required />
                        <x-form.select name="division_id" label="Bahagian" wire:model.live="division_id" required>
                            <option value="">{{ __('loan.placeholders.select_division', []) ?? 'Pilih Bahagian' }}</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.input wire:model.live.debounce.300ms="applicant_position" name="applicant_position"
                            label="Jawatan" required />
                        <x-form.input wire:model.live.debounce.300ms="applicant_grade" name="applicant_grade"
                            label="Gred" required />
                    </div>
                </section>
            @endif

            {{-- Step 2: Responsible Officer --}}
            @if ($currentStep === 2)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="responsible-heading">
                    <div
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100/80 dark:bg-gray-700/50 px-5 py-4">
                        <h2 id="responsible-heading" class="text-lg font-heading font-semibold text-gray-900 dark:text-white">
                            Maklumat Pegawai Bertanggungjawab
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Sahkan sama ada anda sendiri pegawai bertanggungjawab atau isikan maklumat pegawai yang
                            mewakili anda.
                        </p>
                    </div>

                    <x-form.toggle wire:model.live="is_applicant_responsible" id="is_applicant_responsible"
                        label="Adakah anda Pegawai Bertanggungjawab?"
                        description="Matikan jika memohon bagi pihak pegawai atasan." />

                    @if (!$is_applicant_responsible)
                        <div class="space-y-4 rounded-lg border border-warning-200 dark:border-warning-800 bg-warning-50 dark:bg-warning-900/30 p-4">
                            <p class="text-sm text-warning-800 dark:text-warning-300">
                                Sila lengkapkan maklumat pegawai bertanggungjawab di bawah.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input wire:model.live.debounce.300ms="responsible_officer_name"
                                name="responsible_officer_name" label="Nama Pegawai" required />
                            <x-form.input wire:model.live.debounce.300ms="responsible_officer_email"
                                name="responsible_officer_email" type="email" label="Emel Pegawai" required />
                            <x-form.input wire:model.live.debounce.300ms="responsible_officer_phone"
                                name="responsible_officer_phone" type="tel" label="No. Telefon Pegawai" required />
                            <x-form.input wire:model.live.debounce.300ms="responsible_officer_position"
                                name="responsible_officer_position" label="Jawatan Pegawai" required />
                            <x-form.input wire:model.live.debounce.300ms="responsible_officer_grade"
                                name="responsible_officer_grade" label="Gred Pegawai" required />
                        </div>
                    @endif
                </section>
            @endif

            {{-- Step 3: Asset Selection --}}
            @if ($currentStep === 3)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="asset-selection-heading">
                    <div
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100/80 dark:bg-gray-700/50 px-5 py-4">
                        <h2 id="asset-selection-heading"
                            class="text-lg font-heading font-semibold text-gray-900 dark:text-white">
                            Pilih Peralatan
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pilih sekurang-kurangnya satu
                            peralatan.</p>
                    </div>

                    @foreach ($assetCategories as $category)
                        @if ($category->assets->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $category->name }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($category->assets as $asset)
                                        <label
                                            class="flex items-start p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150">
                                            <input type="checkbox" wire:model.live="selected_assets"
                                                value="{{ $asset->id }}"
                                                class="mt-1 mr-3 h-4 w-4 text-primary-600 border-gray-300 rounded focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $asset->name }}
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $asset->brand }} {{ $asset->model }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    Nilai: RM {{ number_format($asset->purchase_value, 2) }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @error('selected_assets')
                        <p role="alert" class="text-danger-600 dark:text-danger-400 text-sm">{{ $message }}</p>
                    @enderror

                    @if (count($selected_assets) > 0)
                        <div class="mt-2 p-4 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-white">Jumlah Nilai Peralatan
                                    Dipilih:</span>
                                <span class="text-lg font-bold text-primary-600 dark:text-primary-300">RM
                                    {{ number_format($this->totalValue, 2) }}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ count($selected_assets) }} peralatan dipilih
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Step 4: Loan Details --}}
            @if ($currentStep === 4)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="loan-details-heading">
                    <div
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100/80 dark:bg-gray-700/50 px-5 py-4">
                        <h2 id="loan-details-heading"
                            class="text-lg font-heading font-semibold text-gray-900 dark:text-white">Butiran Pinjaman
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Nyatakan tujuan, lokasi, serta tarikh
                            pinjaman.</p>
                    </div>

                    <x-form.textarea wire:model.lazy="purpose" name="purpose" label="Tujuan Pinjaman" rows="3"
                        required />
                    <x-form.input wire:model.live.debounce.300ms="location" name="location" label="Lokasi Penggunaan"
                        required />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form.input wire:model.live="loan_start_date" name="loan_start_date" type="date"
                            label="Tarikh Mula" required />
                        <x-form.input wire:model="loan_end_date" name="loan_end_date" type="date" label="Tarikh Pulang"
                            required />
                    </div>
                </section>
            @endif

            {{-- Step 5: Declaration & Review --}}
            @if ($currentStep === 5)
                <section class="{{ $sectionCardClasses }} space-y-6">
                    <div
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100/80 dark:bg-gray-700/50 px-5 py-4">
                        <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white">Semakan &
                            Perakuan</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sahkan maklumat sebelum dihantar.</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/70 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                            Ringkasan Permohonan</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nama</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $applicant_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Emel</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $applicant_email }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Tujuan</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $purpose }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Tarikh</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($loan_start_date)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($loan_end_date)->format('d/m/Y') }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Peralatan Dipilih</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ count($selected_assets) }} unit
                                    (Nilai: RM {{ number_format($this->totalValue, 2) }})</dd>
                            </div>
                        </dl>
                    </div>

                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input id="terms_accepted" wire:model="terms_accepted" type="checkbox" aria-required="true"
                            class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Saya mengesahkan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada
                            di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.
                        </span>
                    </label>
                    @error('terms_accepted')
                        <p role="alert" class="text-danger-600 dark:text-danger-400 text-sm">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            {{-- Navigation Buttons --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4">
                <div>
                    @if ($currentStep > 1)
                        <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                            {{ __('common.previous') ?? 'Kembali' }}
                        </x-ui.button>
                    @endif
                </div>
                <div class="flex gap-3 sm:justify-end">
                    @if ($currentStep < 5)
                        <x-ui.button type="button" variant="primary" wire:click="nextStep">
                            {{ __('common.next') ?? 'Seterusnya' }}
                        </x-ui.button>
                    @else
                        <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('loan.actions.submit_application') ?? 'Hantar Permohonan' }}</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('common.submitting') ?? 'Menghantar...' }}
                            </span>
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </form>

        <div class="mt-6">
            <x-iso-document-footer />
        </div>
    </div>
</div>
