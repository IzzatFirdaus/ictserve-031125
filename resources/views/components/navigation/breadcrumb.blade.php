{{--
/**
 * Enhanced Breadcrumb Component
 *
 * WCAG 2.2 Level AA compliant breadcrumb navigation with proper ARIA attributes.
 *
 * Features:
 * - aria-label="Breadcrumb" per D12 §6.1
 * - aria-current="page" for current page per D14 §10.4
 * - Chevron-right separator icons (Heroicons) per D14 §8.1
 * - Keyboard accessible links
 * - Dark mode support
 *
 * @component
 * @name Breadcrumb
 * @description Accessible breadcrumb navigation
 * @author Pasukan BPM MOTAC
 * @version 2.0.0
 * @since 2025-11-03
 * @updated 2025-12-05
 *
 * Requirements Traceability: D12 §6.1, D14 §10.4, D14 §8.1
 * WCAG Level: AA (SC 2.4.8, 4.1.2)
 *
 * Usage:
 * <x-navigation.breadcrumb :items="['Dashboard' => route('dashboard'), 'Tickets' => route('tickets.index'), 'View Ticket' => null]" />
 */
--}}

@props([
    'items' => [],
    'homeRoute' => 'dashboard',
    'homeLabel' => null,
])

@php
    $homeLabel = $homeLabel ?? __('Home');
@endphp

<nav {{ $attributes->merge(['class' => 'flex']) }} aria-label="{{ __('Breadcrumb') }}">
    <ol role="list" class="flex items-center space-x-2">
        {{-- Home link --}}
        <li>
            <div>
                <a href="{{ route($homeRoute) }}"
                    class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 rounded">
                    {{-- Home icon (Heroicons) per D14 §8.1 --}}
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">{{ $homeLabel }}</span>
                </a>
            </div>
        </li>

        {{-- Breadcrumb items --}}
        @foreach ($items as $label => $url)
            <li>
                <div class="flex items-center">
                    {{-- Chevron-right separator (Heroicons) per D14 §8.1 --}}
                    <svg class="h-5 w-5 shrink-0 text-gray-300 dark:text-gray-600" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd" />
                    </svg>

                    @if (!$loop->last && $url)
                        {{-- Link to parent page --}}
                        <a href="{{ $url }}"
                            class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 rounded transition-colors duration-150">{{ $label }}</a>
                    @else
                        {{-- Current page with aria-current per D14 §10.4 --}}
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                            aria-current="page">{{ $label }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
