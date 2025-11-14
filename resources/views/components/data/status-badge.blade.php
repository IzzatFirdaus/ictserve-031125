{{--
/**
 * Component name: Status Badge
 * Description: WCAG 2.2 AA compliant status badge component with semantic colors, icons, and multilingual text support.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.2 (Visual Indicators)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §7 (UI Components)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §5 (Color Accessibility)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D14 §9 (Accessibility Standards)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@props([
    'status' => 'pending',
    'type' => 'default', // default, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'icon' => true,
])

@php
    // Status type mapping
    $statusTypes = [
        'success' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.success')],
        'approved' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.approved')],
        'active' => ['color' => 'success', 'icon' => '●', 'text' => __('common.active')],
        'completed' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.resolved')],

        'pending' => ['color' => 'warning', 'icon' => '⏱', 'text' => __('common.pending')],
        'in_progress' => ['color' => 'warning', 'icon' => '◐', 'text' => __('common.in_progress')],
        'assigned' => ['color' => 'warning', 'icon' => '→', 'text' => __('common.under_review')],

        'rejected' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'declined' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'cancelled' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'overdue' => ['color' => 'danger', 'icon' => '⚠', 'text' => __('common.overdue')],

        'draft' => ['color' => 'default', 'icon' => '○', 'text' => __('common.pending')],
        'closed' => ['color' => 'default', 'icon' => '●', 'text' => __('common.closed')],
    ];

    $currentStatus = $statusTypes[strtolower($status)] ?? $statusTypes['pending'];
    $colorType = $type !== 'default' ? $type : $currentStatus['color'];

    // WCAG 2.2 AA compliant color classes
    $colorClasses = [
        'success' => 'bg-green-100 text-green-800 border-green-300',
        'warning' => 'bg-orange-100 text-orange-900 border-orange-300',
        'danger' => 'bg-red-100 text-red-900 border-red-300',
        'info' => 'bg-blue-100 text-blue-800 border-blue-300',
        'default' => 'bg-gray-100 text-gray-800 border-gray-300',
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];

    $baseClasses = 'inline-flex items-center gap-1.5 font-medium rounded-md border';
    $classes = implode(' ', [
        $baseClasses,
        $colorClasses[$colorType] ?? $colorClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ]);
@endphp

<span
    {{ $attributes->merge(['class' => $classes]) }}
    role="status"
    aria-label="{{ $currentStatus['text'] }}"
>
    @if($icon)
        <span aria-hidden="true" class="flex-shrink-0">{{ $currentStatus['icon'] }}</span>
    @endif
    <span>{{ $slot->isEmpty() ? $currentStatus['text'] : $slot }}</span>
</span>
