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
        $application = LoanApplication::where('tracking_token', $token)
            ->where('tracking_token_expires_at', '>', now())
            ->first();

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
        $application = LoanApplication::where('application_number', $this->tracking_number)
            ->where('applicant_email', $this->email)
            ->first();

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

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Track Your Loan Application
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Enter your application number and email to check the status
            </p>
        </div>

        <!-- Tracking Form -->
        @if(!$application)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <form wire:submit="trackApplication">
                <div class="space-y-4">
                    <!-- Application Number -->
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Application Number
                        </label>
                        <input
                            type="text"
                            id="tracking_number"
                            wire:model="tracking_number"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="LA202511XXXX"
                            required
                        >
                        @error('tracking_number')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="your.email@motac.gov.my"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Error Message -->
                    @if($error)
                    <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800 dark:text-red-200">{{ $error }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Track Application
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Application Details -->
        @if($application)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <!-- Status Header -->
            <div class="bg-{{ $this->statusColor }}-50 dark:bg-{{ $this->statusColor }}-900/20 px-6 py-4 border-b border-{{ $this->statusColor }}-200 dark:border-{{ $this->statusColor }}-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-{{ $this->statusColor }}-900 dark:text-{{ $this->statusColor }}-100">
                            Application {{ $application->application_number }}
                        </h2>
                        <p class="text-sm text-{{ $this->statusColor }}-700 dark:text-{{ $this->statusColor }}-300">
                            Submitted on {{ $application->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $this->statusColor }}-100 dark:bg-{{ $this->statusColor }}-900 text-{{ $this->statusColor }}-800 dark:text-{{ $this->statusColor }}-200">
                        {{ $application->status->getLabel() }}
                    </span>
                </div>
            </div>

            <!-- Application Information -->
            <div class="px-6 py-4 space-y-4">
                <!-- Applicant Details -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Applicant Information</h3>
                    <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->applicant_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->applicant_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->applicant_phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Division</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->division?->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Loan Period -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Loan Period</h3>
                    <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->loan_start_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">End Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $application->loan_end_date->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Equipment List -->
                @if($application->loanItems->count() > 0)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Equipment</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($application->loanItems as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $item->asset->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $item->equipment_type ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $item->condition_before ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Purpose -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Purpose</h3>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $application->purpose }}</p>
                </div>

                <!-- Track Another Button -->
                <div class="pt-4">
                    <button
                        wire:click="$set('application', null)"
                        class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Track Another Application
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- ISO Footer -->
        <div class="mt-8">
            <x-iso-document-footer />
        </div>
    </div>
</div>
