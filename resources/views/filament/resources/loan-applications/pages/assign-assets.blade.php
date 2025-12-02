<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Application Details --}}
        <div class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('loan.filament.application_details') }}
            </h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('loan.filament.application_number') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $record->application_number }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('loan.filament.applicant_name') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $record->applicant_name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('loan.filament.division') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $record->division->name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('loan.fields.loan_period') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $record->loan_start_date->format('d/m/Y') }} - {{ $record->loan_end_date->format('d/m/Y') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Asset Assignment Form --}}
        <form wire:submit="assignAssets">
            {{ $this->form }}
        </form>
    </div>
</x-filament-panels::page>
