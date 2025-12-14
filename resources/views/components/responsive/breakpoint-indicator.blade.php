{{--
/**
 * Component: Breakpoint Indicator
 * Description: Development helper showing current responsive breakpoint
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.6 (MyDS Spacing System)
 * @trace D14 §5.1 (Grid System)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * MyDS Breakpoints:
 * - xs: <640px (Mobile)
 * - sm: 640px+ (Small tablet)
 * - md: 768px+ (Tablet)
 * - lg: 1024px+ (Small desktop)
 * - xl: 1280px+ (Desktop)
 * - 2xl: 1536px+ (Large desktop)
 *
 * Note: This component is for development only and should be hidden in production.
 *
 * Usage:
 * @env('local')
 *     <x-responsive.breakpoint-indicator />
 * @endenv
 */
--}}

@props([
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
])

@php
    $positionClasses = match ($position) {
        'bottom-left' => 'bottom-4 left-4',
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        default => 'bottom-4 right-4',
    };
@endphp

@if (app()->environment('local', 'development', 'staging'))
    <div {{ $attributes->merge(['class' => "fixed {$positionClasses} z-50 flex items-center gap-2 rounded-full bg-gray-900 px-3 py-1.5 font-mono text-xs text-white shadow-lg"]) }}
        aria-hidden="true">
        {{-- Breakpoint indicator --}}
        <span class="block sm:hidden">xs</span>
        <span class="hidden sm:block md:hidden">sm</span>
        <span class="hidden md:block lg:hidden">md</span>
        <span class="hidden lg:block xl:hidden">lg</span>
        <span class="hidden xl:block 2xl:hidden">xl</span>
        <span class="hidden 2xl:block">2xl</span>

        {{-- Viewport width --}}
        <span x-data="{ width: window.innerWidth }" x-init="window.addEventListener('resize', () => width = window.innerWidth)" x-text="width + 'px'" class="text-gray-400"></span>
    </div>
@endif
