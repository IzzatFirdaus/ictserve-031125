{{--
    Lazy Widget Component
    
    Provides deferred loading for dashboard widgets with accessibility support
    and proper loading states following ICTServe v3.6.1 patterns.
    
    @trace Requirements: R4 (Widget Performance), R17 (Performance Standards)
    @see D04 §3.2 Widget Management Architecture
    @see D12-D14 WCAG 2.2 AA Accessibility Standards
    
    @version 3.6.1
--}}

@props([
    'widgetClass' => '',
    'priority' => 3,
    'placeholder' => [],
    'loadingId' => '',
    'config' => [],
])

@php
    $placeholderConfig = array_merge(
        [
            'type' => 'generic',
            'title' => 'Widget',
            'skeleton' => 'generic-skeleton',
            'height' => 'h-32',
            'animation' => 'pulse',
        ],
        $placeholder,
    );

    $ariaLabel = "Widget {$placeholderConfig['title']} sedang dimuat";
    $loadingText = "Memuat {$placeholderConfig['title']}...";
@endphp

<div data-lazy-widget="{{ $widgetClass }}" data-priority="{{ $priority }}" data-widget-class="{{ $widgetClass }}"
    id="{{ $loadingId }}"
    class="lazy-widget-container {{ $placeholderConfig['height'] }} relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm"
    role="region" aria-label="{{ $ariaLabel }}" aria-live="polite" aria-busy="true">
    {{-- Loading Skeleton --}}
    <div class="loading-skeleton absolute inset-0 p-4">
        {{-- Widget Header Skeleton --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-32"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-24"></div>
                </div>
            </div>
            <div class="w-6 h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>

        {{-- Content Skeleton Based on Type --}}
        @if ($placeholderConfig['type'] === 'stats')
            {{-- Stats Widget Skeleton --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @for ($i = 0; $i < 3; $i++)
                    <div class="text-center">
                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mb-2"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-20 mx-auto"></div>
                    </div>
                @endfor
            </div>
        @elseif($placeholderConfig['type'] === 'chart')
            {{-- Chart Widget Skeleton --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-32"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16"></div>
                </div>
                <div class="h-48 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                <div class="flex justify-center space-x-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-16"></div>
                        </div>
                    @endfor
                </div>
            </div>
        @elseif($placeholderConfig['type'] === 'content')
            {{-- Content Widget Skeleton --}}
            <div class="space-y-4">
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex items-center space-x-4 p-3 border border-gray-100 dark:border-gray-700 rounded">
                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-3/4"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-1/2"></div>
                        </div>
                        <div class="w-16 h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                    </div>
                @endfor
            </div>
        @else
            {{-- Generic Widget Skeleton --}}
            <div class="space-y-4">
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-3/4"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-full"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-5/6"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-2/3"></div>
            </div>
        @endif

        {{-- Loading Indicator --}}
        <div class="absolute bottom-4 right-4 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="sr-only">{{ $loadingText }}</span>
            <span aria-hidden="true">Memuat...</span>
        </div>
    </div>

    {{-- Error State (Hidden by default) --}}
    <div class="error-state hidden absolute inset-0 p-4 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 text-red-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                Widget Gagal Dimuat
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Terdapat masalah semasa memuat widget ini.
            </p>
            <button type="button" onclick="retryLoadWidget(this, '{{ $widgetClass }}')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                aria-label="Cuba semula memuat widget {{ $placeholderConfig['title'] }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Cuba Semula
            </button>
        </div>
    </div>

    {{-- Success State Indicator (Hidden by default) --}}
    <div class="success-indicator hidden absolute top-2 right-2 w-6 h-6 text-green-500" aria-hidden="true">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"></path>
        </svg>
    </div>
</div>

{{-- Screen Reader Announcements --}}
<div id="{{ $loadingId }}-announcements" class="sr-only" aria-live="polite" aria-atomic="true">
    {{-- JavaScript will update this with loading status --}}
</div>

{{-- Widget-specific styles --}}
<style>
    .lazy-widget-container.loading .loading-skeleton {
        display: block;
    }

    .lazy-widget-container.loaded .loading-skeleton {
        display: none;
    }

    .lazy-widget-container.loaded .success-indicator {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    .lazy-widget-container.error .loading-skeleton {
        display: none;
    }

    .lazy-widget-container.error .error-state {
        display: flex !important;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .lazy-widget-container {
            border-width: 2px;
        }

        .loading-skeleton>div {
            background-color: ButtonFace;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {

        .animate-pulse,
        .animate-spin {
            animation: none;
        }

        .loading-skeleton>div {
            opacity: 0.6;
        }
    }

    /* Focus management for accessibility */
    .lazy-widget-container:focus-within {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
</style>

{{-- Enhanced JavaScript for better accessibility --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const widget = document.getElementById('{{ $loadingId }}');
        const announcements = document.getElementById('{{ $loadingId }}-announcements');

        if (!widget || !announcements) return;

        // Listen for widget loading events
        widget.addEventListener('widget:loading', function() {
            announcements.textContent = '{{ $loadingText }}';
            widget.setAttribute('aria-busy', 'true');
        });

        widget.addEventListener('widget:loaded', function() {
            announcements.textContent = 'Widget {{ $placeholderConfig['title'] }} berjaya dimuat';
            widget.setAttribute('aria-busy', 'false');

            // Focus management for screen readers
            const firstFocusable = widget.querySelector(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable && document.activeElement === widget) {
                firstFocusable.focus();
            }
        });

        widget.addEventListener('widget:error', function(event) {
            announcements.textContent =
                'Widget {{ $placeholderConfig['title'] }} gagal dimuat. Sila cuba semula.';
            widget.setAttribute('aria-busy', 'false');

            // Focus on retry button for keyboard users
            const retryButton = widget.querySelector('button[onclick*="retryLoadWidget"]');
            if (retryButton && document.activeElement === widget) {
                retryButton.focus();
            }
        });

        // Keyboard navigation support
        widget.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                if (widget.classList.contains('error')) {
                    const retryButton = widget.querySelector('button[onclick*="retryLoadWidget"]');
                    if (retryButton) {
                        event.preventDefault();
                        retryButton.click();
                    }
                }
            }
        });
    });
</script>
