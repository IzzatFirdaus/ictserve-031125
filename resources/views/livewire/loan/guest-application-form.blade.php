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
            $this->validate([
                'selected_assets' => 'required|array|min:1',
            ], [
                'selected_assets.required' => 'Sila pilih sekurang-kurangnya satu peralatan.',
                'selected_assets.min' => 'Sila pilih sekurang-kurangnya satu peralatan.',
            ]);
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
            'assetCategories' => AssetCategory::with(['assets' => function($query) {
                $query->available()->orderBy('name');
            }])->get(),
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

<main role="main" id="main-content" class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <h1 id="form-heading" class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Borang Permohonan Pinjaman Peralatan ICT</h1>
    <livewire:language-switcher />

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6" aria-labelledby="form-heading">
        
        <!-- Step Indicator -->
        <nav aria-label="Progress">
            <ol role="list" class="flex items-center">
                @foreach(range(1, 5) as $step)
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full {{ $step < 5 ? ($step < $currentStep ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600') : '' }}"></div>
                        </div>
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $step <= $currentStep ? 'bg-indigo-600 hover:bg-indigo-900' : 'bg-gray-200 dark:bg-gray-600 hover:bg-gray-300' }}">
                            @if($step < $currentStep)
                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">Step {{ $step }} Completed</span>
                            @elseif($step === $currentStep)
                                <span class="h-2.5 w-2.5 rounded-full bg-white" aria-hidden="true"></span>
                                <span class="sr-only">Step {{ $step }} Current</span>
                            @else
                                <span class="sr-only">Step {{ $step }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- Step 1: Applicant Info -->
        @if($currentStep === 1)
        <section class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" aria-labelledby="applicant-info-heading">
            <h2 id="applicant-info-heading" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Maklumat Pemohon</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="applicant_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penuh</label>
                    <input type="text" id="applicant_name" wire:model="applicant_name"
                        aria-required="true"
                        aria-invalid="@error('applicant_name') true @else false @enderror"
                        aria-describedby="@error('applicant_name') applicant_name_error @enderror"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_name') <span id="applicant_name_error" role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="applicant_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emel Rasmi</label>
                    <input type="email" id="applicant_email" wire:model="applicant_email"
                        aria-required="true"
                        aria-invalid="@error('applicant_email') true @else false @enderror"
                        aria-describedby="@error('applicant_email') applicant_email_error @enderror"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_email') <span id="applicant_email_error" role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="applicant_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Telefon</label>
                    <input type="text" id="applicant_phone" wire:model="applicant_phone"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_phone') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="applicant_staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Pekerja / No. Kad Pengenalan</label>
                    <input type="text" id="applicant_staff_id" wire:model="applicant_staff_id"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_staff_id') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="division_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bahagian</label>
                    <select id="division_id" wire:model="division_id"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Pilih Bahagian</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="applicant_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jawatan</label>
                    <input type="text" id="applicant_position" wire:model="applicant_position"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_position') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="applicant_grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gred</label>
                    <input type="text" id="applicant_grade" wire:model="applicant_grade"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('applicant_grade') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>
        @endif

        <!-- Step 2: Responsible Officer -->
        @if($currentStep === 2)
        <section class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Maklumat Pegawai Bertanggungjawab</h2>
            <x-form.toggle
                wire:model.live="is_applicant_responsible"
                id="is_applicant_responsible"
                label="Adakah anda Pegawai Bertanggungjawab?"
                description="Jika anda memohon bagi pihak pegawai atasan, sila matikan butang ini."
            />

            @if(!$is_applicant_responsible)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                    <div class="col-span-2">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-white">Maklumat Pegawai Bertanggungjawab</h3>
                    </div>
                    <div>
                        <label for="responsible_officer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pegawai</label>
                        <input type="text" id="responsible_officer_name" wire:model="responsible_officer_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('responsible_officer_name') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emel Pegawai</label>
                        <input type="email" id="responsible_officer_email" wire:model="responsible_officer_email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('responsible_officer_email') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Telefon Pegawai</label>
                        <input type="text" id="responsible_officer_phone" wire:model="responsible_officer_phone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('responsible_officer_phone') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jawatan Pegawai</label>
                        <input type="text" id="responsible_officer_position" wire:model="responsible_officer_position"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('responsible_officer_position') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gred Pegawai</label>
                        <input type="text" id="responsible_officer_grade" wire:model="responsible_officer_grade"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('responsible_officer_grade') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif
        </section>
        @endif

        <!-- Step 3: Asset Selection -->
        @if($currentStep === 3)
        <section class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" aria-labelledby="asset-selection-heading">
            <h2 id="asset-selection-heading" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pilih Peralatan</h2>

            @foreach($assetCategories as $category)
                @if($category->assets->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ $category->name }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($category->assets as $asset)
                                <label class="flex items-start p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                    <input type="checkbox"
                                        wire:model.live="selected_assets"
                                        value="{{ $asset->id }}"
                                        class="mt-1 mr-3 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $asset->name }}</div>
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

            @error('selected_assets') <span role="alert" class="text-red-500 text-sm">{{ $message }}</span> @enderror

            @if(count($selected_assets) > 0)
                <div class="mt-4 p-4 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900 dark:text-white">Jumlah Nilai Peralatan Dipilih:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-300">RM {{ number_format($this->totalValue, 2) }}</span>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ count($selected_assets) }} peralatan dipilih
                    </div>
                </div>
            @endif
        </section>
        @endif

        <!-- Step 4: Loan Details -->
        @if($currentStep === 4)
        <section class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" aria-labelledby="loan-details-heading">
            <h2 id="loan-details-heading" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Butiran Pinjaman</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan Pinjaman</label>
                    <textarea id="purpose" wire:model="purpose" rows="3"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"></textarea>
                    @error('purpose') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Penggunaan</label>
                    <input type="text" id="location" wire:model="location"
                        aria-required="true"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('location') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="loan_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh Mula</label>
                        <input type="date" id="loan_start_date" wire:model.live="loan_start_date"
                            aria-required="true"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('loan_start_date') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="loan_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh Pulang</label>
                        <input type="date" id="loan_end_date" wire:model="loan_end_date"
                            aria-required="true"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        @error('loan_end_date') <span role="alert" class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Step 5: Declaration & Review -->
        @if($currentStep === 5)
        <section class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Semakan & Perakuan</h2>
            
            <!-- Summary of Application -->
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm">
                <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Ringkasan Permohonan</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div class="col-span-1"><dt class="text-gray-500 dark:text-gray-400">Nama:</dt> <dd class="font-medium text-gray-900 dark:text-white">{{ $applicant_name }}</dd></div>
                    <div class="col-span-1"><dt class="text-gray-500 dark:text-gray-400">Emel:</dt> <dd class="font-medium text-gray-900 dark:text-white">{{ $applicant_email }}</dd></div>
                    <div class="col-span-1"><dt class="text-gray-500 dark:text-gray-400">Tujuan:</dt> <dd class="font-medium text-gray-900 dark:text-white">{{ $purpose }}</dd></div>
                    <div class="col-span-1"><dt class="text-gray-500 dark:text-gray-400">Tarikh:</dt> <dd class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($loan_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($loan_end_date)->format('d/m/Y') }}</dd></div>
                    <div class="col-span-2 mt-2">
                        <dt class="text-gray-500 dark:text-gray-400">Peralatan Dipilih:</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ count($selected_assets) }} unit (Nilai: RM {{ number_format($this->totalValue, 2) }})</dd>
                    </div>
                </dl>
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms_accepted" wire:model="terms_accepted" type="checkbox"
                        aria-required="true"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms_accepted" class="font-medium text-gray-700 dark:text-gray-300">Perakuan Pemohon</label>
                    <p class="text-gray-500 dark:text-gray-400">Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.</p>
                </div>
            </div>
            @error('terms_accepted') <span role="alert" class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror
        </section>
        @endif

        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            @if($currentStep > 1)
                <button type="button" wire:click="previousStep"
                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Kembali
                </button>
            @else
                <div></div> <!-- Spacer -->
            @endif

            @if($currentStep < 5)
                <button type="button" wire:click="nextStep"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Seterusnya
                </button>
            @else
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    Hantar Permohonan
                </button>
            @endif
        </div>
    </form>

    <x-iso-document-footer />
</main>