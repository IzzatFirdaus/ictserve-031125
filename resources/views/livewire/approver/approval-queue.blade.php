{{--
/**
 * Approval Queue View
 * Grade 41+ approval interface with bulk actions and SLA monitoring
 * @wcag-level AA
 */
--}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('Barisan Kelulusan') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Semak dan luluskan permohonan pinjaman yang tertunda') }}
        </p>
    </div>

    {{-- SLA Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Jumlah Menunggu') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->slaStats['total'] }}</p>
                </div>
                <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                    <svg class="h-6 w-6 text-primary-600 dark:text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Kritikal (>48j)') }}</p>
                    <p class="text-2xl font-bold text-danger-600">{{ $this->slaStats['urgent'] }}</p>
                </div>
                <div class="p-3 bg-danger-100 dark:bg-danger-900 rounded-full">
                    <svg class="h-6 w-6 text-danger-600 dark:text-danger-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Amaran (24-48j)') }}</p>
                    <p class="text-2xl font-bold text-warning-600">{{ $this->slaStats['warning'] }}</p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900 rounded-full">
                    <svg class="h-6 w-6 text-warning-600 dark:text-warning-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Normal (<24j)') }}</p>
                    <p class="text-2xl font-bold text-success-600">{{ $this->slaStats['normal'] }}</p>
                </div>
                <div class="p-3 bg-success-100 dark:bg-success-900 rounded-full">
                    <svg class="h-6 w-6 text-success-600 dark:text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Bulk Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <x-form.input 
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    :placeholder="__('Cari permohonan...')"
                    name="search"
                />
            </div>

            {{-- Bulk Actions --}}
            @if(!empty($selected))
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ count($selected) }} {{ __('dipilih') }}
                    </span>
                    <button 
                        wire:click="bulkApprove"
                        class="min-h-11 px-4 py-2 bg-success-600 text-white rounded-lg hover:bg-success-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-success-500 focus-visible:ring-offset-2"
                    >
                        {{ __('Lulus Pukal') }}
                    </button>
                    <button 
                        wire:click="bulkReject"
                        class="min-h-11 px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2"
                    >
                        {{ __('Tolak Pukal') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Applications Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left">
                        <input 
                            type="checkbox"
                            wire:model.live="selectAll"
                            class="rounded border-gray-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        />
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <button wire:click="sortBy('application_number')" class="flex items-center hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                            {{ __('No. Permohonan') }}
                            @if($sortField === 'application_number')
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Pemohon') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Tujuan') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <button wire:click="sortBy('created_at')" class="flex items-center hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                            {{ __('Dihantar') }}
                            @if($sortField === 'created_at')
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Status SLA') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->pendingApplications as $application)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <input 
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $application->id }}"
                                class="rounded border-gray-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $application->application_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $application->applicant_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ Str::limit($application->purpose, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $application->created_at->format('Y-m-d H:i') }}
                            <br>
                            <span class="text-xs">{{ $application->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $hoursElapsed = $application->created_at->diffInHours(now());
                                $slaClass = $hoursElapsed > 48 ? 'bg-danger-100 text-danger-800' : ($hoursElapsed > 24 ? 'bg-warning-100 text-warning-800' : 'bg-success-100 text-success-800');
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $slaClass }}">
                                {{ number_format($hoursElapsed, 0) }}h
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button 
                                wire:click="approve({{ $application->id }})"
                                class="inline-flex min-h-11 items-center text-success-600 hover:text-success-900 dark:hover:text-success-400 mr-3 focus:outline-none focus-visible:ring-3 focus-visible:ring-success-500 focus-visible:ring-offset-2 rounded"
                            >
                                {{ __('Lulus') }}
                            </button>
                            <button 
                                wire:click="reject({{ $application->id }})"
                                class="inline-flex min-h-11 items-center text-danger-600 hover:text-danger-900 dark:hover:text-danger-400 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2 rounded"
                            >
                                {{ __('Tolak') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Tiada permohonan menunggu') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $this->pendingApplications->links() }}
        </div>
    </div>
</div>
