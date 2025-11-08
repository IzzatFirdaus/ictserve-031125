<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Export Submissions</h1>

    <div class="flex items-center gap-6">
        <label class="inline-flex items-center gap-2">
            <input type="radio" wire:model="exportFormat" value="csv" class="form-radio" />
            <span>CSV</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="radio" wire:model="exportFormat" value="pdf" class="form-radio" />
            <span>PDF</span>
        </label>
    </div>

    <div wire:loading class="text-sm text-gray-600">
        Processing
    </div>

    <span class="sr-only">Processing</span>
</div>
