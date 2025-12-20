{{--
/**
 * SLA Compliance Gauge Component
 *
 * Visual gauge showing SLA compliance percentage with color-coded status
 * using semantic tokens per D14 §4.1.1.
 *
 * Features:
 * - Color-coded status: Green (>90%), Yellow (70-90%), Red (<70%) per D14 §4.1.1
 * - Accessible labels with aria-label per D14 §10.5
 * - Animated progress bar with prefers-reduced-motion support
 * - Screen reader friendly percentage announcement
 *
 * @component
 * @name SLA Gauge
 * @description SLA compliance gauge with color-coded status
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D14 §4.1.1, D14 §10.5, Task 4.2.2
 * WCAG Level: AA (SC 1.4.1, 1.4.11, 4.1.2)
 *
 * Usage:
 * <x-ui.sla-gauge
 *     :percentage="85"
 *     label="SLA Compliance"
 *     :show-label="true"
 * />
 */
--}}

@props([
'percentage' => 0,
'label' => null,
'showLabel' => true,
'showPercentage' => true,
'size' => 'md',
'animate' => true,
])

@php
$percentage = max(0, min(100, (float) $percentage));

// Color-coded status per D14 §4.1.1
// Green (--txt-success-600) >90%, Yellow (--txt-warning-600) 70-90%, Red (--txt-danger) <70%
    if ($percentage>= 90) {
    $statusColor = 'bg-success-500';
    $statusTextColor = 'text-success-600 dark:text-success-400';
    $statusLabel = __('Excellent');
    } elseif ($percentage >= 70) {
    $statusColor = 'bg-warning-500';
    $statusTextColor = 'text-warning-600 dark:text-warning-400';
    $statusLabel = __('Needs Attention');
    } else {
    $statusColor = 'bg-danger-500';
    $statusTextColor = 'text-danger-600 dark:text-danger-400';
    $statusLabel = __('Critical');
    }

    // Size variants
    $sizeClasses = match ($size) {
    'sm' => 'h-2',
    'lg' => 'h-4',
    default => 'h-3',
    };

    $gaugeId = 'sla-gauge-' . uniqid();
    @endphp

    <div {{ $attributes->merge(['class' => 'w-full']) }} role="meter" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
        aria-valuemax="100" aria-label="{{ $label ?? __('SLA Compliance') }}: {{ number_format($percentage, 1) }}%">
        {{-- Label and percentage display --}}
        @if ($showLabel || $showPercentage)
        <div class="flex items-center justify-between mb-2">
            @if ($showLabel && $label)
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                {{ $label }}
            </span>
            @endif
            @if ($showPercentage)
            <span class="text-sm font-semibold {{ $statusTextColor }}">
                {{ number_format($percentage, 1) }}%
            </span>
            @endif
        </div>
        @endif

        {{-- Progress bar track --}}
        <div class="w-full {{ $sizeClasses }} bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            {{-- Progress bar fill --}}
            <div id="{{ $gaugeId }}"
                class="{{ $sizeClasses }} {{ $statusColor }} rounded-full transition-all duration-500 ease-out motion-reduce:transition-none"
                @if ($animate)
                x-data="{ width: 0 }"
                x-init="setTimeout(() => width = {{ $percentage }}, 100)"
                :style="`width: ${width}%`"
                @else
                style="width: {{ $percentage }}%"
                @endif>
            </div>
        </div>

        {{-- Status label --}}
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs {{ $statusTextColor }}">
                {{ $statusLabel }}
            </span>
            {{-- Screen reader only detailed status --}}
            <span class="sr-only">
                {{ __('SLA compliance is at :percentage percent, status: :status', [
                'percentage' => number_format($percentage, 1),
                'status' => $statusLabel,
            ]) }}
            </span>
        </div>
    </div>
