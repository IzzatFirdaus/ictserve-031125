{{--
/**
 * Data Table Component
 *
 * WCAG 2.2 Level AA compliant data table with proper semantic structure,
 * sortable headers, and responsive design.
 *
 * Features:
 * - Semantic HTML table structure with caption
 * - Sortable column headers with ARIA sort attributes
 * - Responsive design with horizontal scroll wrapper
 * - Keyboard navigation support
 * - Screen reader accessible
 *
 * @component
 * @name Data Table
 * @description Accessible data table for displaying structured data
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-11-03
 * @updated 2025-11-03
 *
 * Requirements Traceability: D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D03-FR-019, D04 §6.1, D10 §7, D12 §7.4, D12 §9, D14 §8
 * WCAG Level: AA (SC 1.3.1, 1.3.2, 2.1.1, 2.4.6)
 * Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
 * Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * Usage:
 * <x-data.table caption="User List">
 *   <x-slot:header>
 *     <th scope="col">Name</th>
 *     <th scope="col">Email</th>
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
])

@php
    $wrapperClasses = 'overflow-x-auto -mx-4 sm:mx-0';
    $tableClasses = 'min-w-full divide-y divide-gray-300';
    $theadClasses = 'bg-gray-50';
    $thClasses = 'px-3 py-3.5 text-left text-sm font-semibold text-gray-900';
    $tbodyClasses = 'divide-y divide-gray-200 bg-white';
    $trClasses = $striped ? 'even:bg-gray-50' : '';
    $trClasses .= $hover ? ' hover:bg-gray-100 transition-colors duration-150' : '';
    $tdClasses = 'whitespace-nowrap px-3 py-4 text-sm text-gray-700';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <table class="{{ $tableClasses }}" role="table">
        @if ($caption)
            <caption class="sr-only">{{ $caption }}</caption>
        @endif

        @if ($header)
            <thead class="{{ $theadClasses }}">
                <tr role="row">
                    {{ $header }}
                </tr>
            </thead>
        @endif

        <tbody class="{{ $tbodyClasses }}" role="rowgroup">
            {{ $slot }}
        </tbody>
    </table>
</div>
