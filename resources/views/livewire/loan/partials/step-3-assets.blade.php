{{-- Step 3: Asset Selection --}}
@php
$sectionCardClasses = 'rounded-2xl border border-gray-200 bg-white p-6 shadow-card';
@endphp

<section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-3-heading" role="region">
    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
        <h2 id="step-3-heading" class="text-lg font-semibold text-gray-900">
            {{ __('loan.form.section_3_equipment_list') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.select_equipment_note') }}</p>
    </div>

    {{-- Asset Selection Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                        {{ __('loan.table.no') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('loan.table.equipment_type') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        {{ __('loan.table.quantity') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('loan.table.notes') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                        {{ __('loan.table.availability') }}
                    </th>
                    <th scope="col" class="px-4 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($selectedAssets as $index => $item)
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-4 py-4">
                        <select
                            wire:model.live="selectedAssets.{{ $index }}.category_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 text-sm">
                            <option value="">{{ __('loan.placeholders.select_equipment') }}</option>
                            @foreach ($this->assetCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error("selectedAssets.{$index}.category_id")
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </td>
                    <td class="px-4 py-4">
                        <input
                            type="number"
                            wire:model.live="selectedAssets.{{ $index }}.quantity"
                            min="1"
                            max="10"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 text-sm">
                        @error("selectedAssets.{$index}.quantity")
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </td>
                    <td class="px-4 py-4">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="selectedAssets.{{ $index }}.notes"
                            placeholder="{{ __('loan.placeholders.notes') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 text-sm">
                    </td>
                    <td class="px-4 py-4">
                        @if (isset($assetAvailability[$index]))
                        @if ($assetAvailability[$index]['available'])
                        <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-medium text-success-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('loan.status.available') }}
                        </span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-danger-100 px-2.5 py-0.5 text-xs font-medium text-danger-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            {{ __('loan.status.unavailable') }}
                        </span>
                        @endif
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if (count($selectedAssets) > 1)
                        <button
                            type="button"
                            wire:click="removeAssetRow({{ $index }})"
                            class="text-danger-600 hover:text-danger-800 p-2 rounded-md hover:bg-danger-50 min-h-11 min-w-11 flex items-center justify-center"
                            aria-label="{{ __('loan.actions.remove_equipment') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Add Equipment Button --}}
    <div class="flex justify-start">
        <x-ui.button type="button" variant="secondary" wire:click="addAssetRow">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('loan.actions.add_equipment') }}
        </x-ui.button>
    </div>

    @error('selectedAssets')
    <p class="text-sm text-danger-600">{{ $message }}</p>
    @enderror
</section>