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
    // Status type mapping with MyDS semantic colors
    $statusTypes = [
        'success' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.success')],
        'approved' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.approved')],
        'active' => ['color' => 'success', 'icon' => '●', 'text' => __('common.active')],
        'completed' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.resolved')],
        'resolved' => ['color' => 'success', 'icon' => '✓', 'text' => __('common.resolved')],

        'pending' => ['color' => 'warning', 'icon' => '⏱', 'text' => __('common.pending')],
        'in_progress' => ['color' => 'warning', 'icon' => '◐', 'text' => __('common.in_progress')],
        'assigned' => ['color' => 'warning', 'icon' => '→', 'text' => __('common.under_review')],
        'processing' => ['color' => 'warning', 'icon' => '◐', 'text' => __('common.in_progress')],

        'rejected' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'declined' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'cancelled' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.rejected')],
        'overdue' => ['color' => 'danger', 'icon' => '⚠', 'text' => __('common.overdue')],
        'failed' => ['color' => 'danger', 'icon' => '✕', 'text' => __('common.failed')],

        'draft' => ['color' => 'default', 'icon' => '○', 'text' => __('common.pending')],
        'closed' => ['color' => 'default', 'icon' => '●', 'text' => __('common.closed')],
        'new' => ['color' => 'info', 'icon' => '★', 'text' => __('common.new')],
    ];

    $currentStatus = $statusTypes[strtolower($status)] ?? $statusTypes['pending'];
    $colorType = $type !== 'default' ? $type : $currentStatus['color'];

    // MyDS semantic color tokens (WCAG 2.2 AA compliant)
    // Using semantic tokens from D13 §2.2
    $colorClasses = [
        'success' =>
            'bg-success-50 text-success-600 border-success-300 dark:bg-success-900/20 dark:text-success-400 dark:border-success-700',
        'warning' =>
            'bg-warning-50 text-warning-600 border-warning-300 dark:bg-warning-900/20 dark:text-warning-400 dark:border-warning-700',
        'danger' =>
            'bg-danger-50 text-danger-600 border-danger-300 dark:bg-danger-900/20 dark:text-danger-400 dark:border-danger-700',
        'info' =>
            'bg-primary-50 text-primary-600 border-primary-300 dark:bg-primary-900/20 dark:text-primary-400 dark:border-primary-700',
        'default' =>
            'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600',
    ];

    // Size classes with rounded-full per MyDS radius system
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];

    $baseClasses = 'inline-flex items-center gap-1.5 font-medium rounded-full border transition-colors duration-200';
    $classes = implode(' ', [
        $baseClasses,
        $colorClasses[$colorType] ?? $colorClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }} role="status" aria-label="{{ $currentStatus['text'] }}">
    @if ($icon)
        <span aria-hidden="true" class="shrink-0">{{ $currentStatus['icon'] }}</span>
    @endif
    <span>{{ $slot->isEmpty() ? $currentStatus['text'] : $slot }}</span>
</span>
