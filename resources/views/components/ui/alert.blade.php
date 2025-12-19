{{--
/**
 * UI Alert Component - MyDS Design System
 *
 * @component ui.alert
 * @description WCAG 2.2 AA compliant alert with MyDS semantic colors
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2 (MyDS Semantic Colors)
 * @trace D14 §9.3 (Notification Guidelines)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props([
'variant' => 'info',
'dismissible' => false,
'title' => null,
])

@php
// MyDS semantic color tokens (D13 §2.2)
$variants = [
'info' => [
'container' =>
'bg-primary-50 text-primary-800 border-primary-200 dark:bg-primary-900/30 dark:text-primary-300 dark:border-primary-800',
'icon' => 'text-primary-500 dark:text-primary-400',
'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
],
'success' => [
'container' =>
'bg-success-50 text-success-800 border-success-200 dark:bg-success-900/30 dark:text-success-300 dark:border-success-800',
'icon' => 'text-success-500 dark:text-success-400',
'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
],
'warning' => [
'container' =>
'bg-warning-50 text-warning-800 border-warning-200 dark:bg-warning-900/30 dark:text-warning-300 dark:border-warning-800',
'icon' => 'text-warning-500 dark:text-warning-400',
'icon_path' =>
'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
],
'danger' => [
'container' =>
'bg-danger-50 text-danger-800 border-danger-200 dark:bg-danger-900/30 dark:text-danger-300 dark:border-danger-800',
'icon' => 'text-danger-500 dark:text-danger-400',
'icon_path' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
],
];

$config = $variants[$variant] ?? $variants['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-out duration-200"
    x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95"
    class="rounded-(--radius-l) border p-4 {{ $config['container'] }}" role="alert" {{ $attributes }}>
    <div class="flex">
        <div class="shrink-0">
            <svg class="h-5 w-5 {{ $config['icon'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon_path'] }}" />
            </svg>
        </div>
        <div class="ml-3 flex-1 md:flex md:justify-between">
            <div>
                @if ($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
                @endif
                <div class="text-sm {{ $title ? 'mt-2' : '' }}">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @if ($dismissible)
        <div class="ml-auto pl-3">
            {{-- 44px touch target per D12 §4.1 --}}
            <button @click="show = false" type="button"
                class="inline-flex items-center justify-center min-h-11 min-w-11 rounded-(--radius-s) p-2 focus:outline-none {{ $config['container'] }} hover:bg-opacity-75 transition-colors duration-200"
                aria-label="{{ __('common.dismiss') }}">
                <span class="sr-only">{{ __('common.dismiss') }}</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        @endif
    </div>
</div>
