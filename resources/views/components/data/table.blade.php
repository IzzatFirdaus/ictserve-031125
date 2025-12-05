{{--
/**
 * Enhanced Data Table Component
 *
 * WCAG 2.2 Level AA compliant data table with proper semantic structure,
 * sortable headers, sticky headers, and responsive design with mobile card view.
 *
 * Features:
 * - Semantic HTML table structure with caption and <th scope="col">
 * - Sortable column headers with aria-sort attribute per D14 §10.5
 * - Sticky headers for long tables per D14 §6.6
 * - Zebra striping for readability per D14 §6.6
 * - Responsive design: Full table (≥768px), Card view (<768px) per D12 §6.14
 * - Keyboard navigation support
 * - Screen reader accessible
 *
 * @component
 * @name Enhanced Data Table
 * @description Accessible data table with sticky headers, sorting, and mobile card view
 * @author Pasukan BPM MOTAC
 * @version 2.0.0
 * @since 2025-11-03
 * @updated 2025-12-05
 *
 * Requirements Traceability: D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D03-FR-019, D04 §6.1, D10 §7, D12 §6.14, D14 §6.6, D14 §10.5
 * WCAG Level: AA (SC 1.3.1, 1.3.2, 2.1.1, 2.4.6, 4.1.2)
 * Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
 * Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * Usage:
 * <x-data.table
 *     caption="User List"
 *     :sticky-header="true"
 *     :sortable="true"
 *     sort-column="name"
 *     sort-direction="asc"
 * >
 *   <x-slot:header>
 *     <x-data.table-header column="name" :sortable="true">Name</x-data.table-header>
 *     <x-data.table-header column="email" :sortable="true">Email</x-data.table-header>
 *   </x-slot:header>
 *   <tr>
 *     <td>John Doe</td>
 *     <td>john@example.com</td>
 *   </tr>
 * </x-data.table>
 */
--}}

@props([
    'caption' => null,
    'header' => null,
    'striped' => true,
    'hover' => true,
    'stickyHeader' => false,
    'sortable' => false,
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'mobileCardView' => true,
    'emptyMessage' => null,
    'loading' => false,
])

@php
    $tableId = 'table-' . uniqid();
    $wrapperClasses = 'relative';
    $scrollContainerClasses = 'overflow-x-auto -mx-4 sm:mx-0';
    $tableClasses = 'min-w-full divide-y divide-gray-300 dark:divide-gray-600';

    // Sticky header classes per D14 §6.6
    $theadClasses = 'bg-gray-50 dark:bg-gray-800';
    if ($stickyHeader) {
        $theadClasses .= ' sticky top-0 z-10 shadow-sm';
    }

    $thBaseClasses = 'px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100';
    $tbodyClasses = 'divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900';

    // Zebra striping per D14 §6.6
    $trClasses = $striped ? 'even:bg-gray-50 dark:even:bg-gray-800/50' : '';
    $trClasses .= $hover ? ' hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors duration-150' : '';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClasses]) }} x-data="{
    sortColumn: @js($sortColumn),
    sortDirection: @js($sortDirection),
    isMobile: window.innerWidth < 768,
    init() {
        this.checkViewport();
        window.addEventListener('resize', () => this.checkViewport());
    },
    checkViewport() {
        this.isMobile = window.innerWidth < 768;
    },
    sort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
        this.$dispatch('table-sort', { column: this.sortColumn, direction: this.sortDirection });
    },
    getSortAriaLabel(column) {
        if (this.sortColumn !== column) return 'none';
        return this.sortDirection === 'asc' ? 'ascending' : 'descending';
    }
}">
    {{-- Loading overlay --}}
    @if ($loading)
        <div class="absolute inset-0 bg-white/75 dark:bg-gray-900/75 z-20 flex items-center justify-center"
            aria-busy="true">
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="sr-only">{{ __('Loading...') }}</span>
            </div>
        </div>
    @endif

    {{-- Desktop Table View (≥768px) per D12 §6.14 --}}
    <div class="{{ $scrollContainerClasses }}" x-show="!isMobile || !{{ $mobileCardView ? 'true' : 'false' }}" x-cloak>
        <table id="{{ $tableId }}" class="{{ $tableClasses }}" role="table"
            aria-describedby="{{ $caption ? $tableId . '-caption' : null }}">
            @if ($caption)
                <caption id="{{ $tableId }}-caption" class="sr-only">{{ $caption }}</caption>
            @endif

            @if ($header)
                <thead class="{{ $theadClasses }}">
                    <tr role="row">
                        {{ $header }}
                    </tr>
                </thead>
            @endif

            <tbody class="{{ $tbodyClasses }}" role="rowgroup">
                @if ($slot->isEmpty() && $emptyMessage)
                    <tr>
                        <td colspan="100" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View (<768px) per D12 §6.14 --}}
    @if ($mobileCardView && isset($mobileCards))
        <div class="space-y-4 md:hidden" x-show="isMobile" x-cloak role="list"
            aria-label="{{ $caption ?? __('Data list') }}">
            {{ $mobileCards }}
        </div>
    @endif
</div>
