<div class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('export.export_submissions') }}</h1>

    <div class="flex items-center gap-6">
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="radio" wire:model="exportFormat" value="csv"
                class="form-radio text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-3 focus-visible:ring-offset-2 dark:bg-gray-700 dark:border-gray-600" />
            <span class="text-gray-700 dark:text-gray-300">CSV</span>
        </label>
        <label class="inline-flex items-center gap-2 min-h-11 cursor-pointer">
            <input type="radio" wire:model="exportFormat" value="pdf"
                class="form-radio text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-3 focus-visible:ring-offset-2 dark:bg-gray-700 dark:border-gray-600" />
            <span class="text-gray-700 dark:text-gray-300">PDF</span>
        </label>
    </div>

    <div wire:loading class="text-sm text-gray-600 dark:text-gray-400">
        Processing
    </div>

    <span class="sr-only">Processing</span>
</div>

