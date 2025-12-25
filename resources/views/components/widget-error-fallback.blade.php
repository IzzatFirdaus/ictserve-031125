@props([
    'errorData' => [],
    'widgetName' => 'Widget',
    'errorId' => null,
    'userMessage' => 'Ralat tidak dijangka berlaku.',
    'canRetry' => true,
    'retryCount' => 0,
    'nextRetryIn' => null,
])

{{-- 
Widget Error Fallback Component

Displays user-friendly error messages for failed widgets with
retry functionality and accessibility compliance.

Features:
- WCAG 2.2 AA compliant error display
- Bahasa Melayu error messages
- Retry functionality with countdown
- Accessible error announcements
- Loading states and animations
- Responsive design

@trace Requirements: R7 (Widget Error Handling), R6 (Accessibility)

@see D04 §3.2 Dashboard widgets
@see D12-D14 UI/UX standards - WCAG 2.2 AA compliance

@version 3.6.1
--}}

<div class="widget-error-fallback bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-800 p-6 shadow-sm"
    role="alert" aria-live="polite" aria-labelledby="error-title-{{ $errorId }}"
    aria-describedby="error-description-{{ $errorId }}">
    {{-- Error Header --}}
    <div class="flex items-start space-x-3">
        {{-- Error Icon --}}
        <div class="shrink-0">
            <svg class="h-6 w-6 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>

        {{-- Error Content --}}
        <div class="flex-1 min-w-0">
            {{-- Error Title --}}
            <h3 id="error-title-{{ $errorId }}" class="text-sm font-medium text-red-800 dark:text-red-200">
                Ralat {{ $widgetName }}
            </h3>

            {{-- Error Description --}}
            <div id="error-description-{{ $errorId }}" class="mt-1 text-sm text-red-700 dark:text-red-300">
                <p>{{ $userMessage }}</p>

                @if ($errorId)
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                        ID Ralat: {{ $errorId }}
                    </p>
                @endif
            </div>

            {{-- Retry Information --}}
            @if ($retryCount > 0)
                <div class="mt-2 text-xs text-red-600 dark:text-red-400">
                    <p>Percubaan: {{ $retryCount }}/3</p>
                    @if ($nextRetryIn)
                        <p class="mt-1">
                            Cuba semula dalam
                            <span id="retry-countdown-{{ $errorId }}" class="font-mono font-medium"
                                aria-live="polite">
                                {{ $nextRetryIn }}
                            </span>
                            saat
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-4 flex flex-col sm:flex-row gap-2">
        @if ($canRetry)
            <button type="button" onclick="retryWidget('{{ $errorData['widget_class'] ?? '' }}')"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-600 transition-colors duration-200"
                aria-describedby="retry-help-{{ $errorId }}">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Cuba Semula
            </button>
            <div id="retry-help-{{ $errorId }}" class="sr-only">
                Klik untuk mencuba memuatkan {{ $widgetName }} semula
            </div>
        @endif

        <button type="button"
            onclick="reportWidgetError('{{ $errorId }}', '{{ $errorData['widget_class'] ?? '' }}')"
            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
            aria-describedby="report-help-{{ $errorId }}">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            Laporkan Masalah
        </button>
        <div id="report-help-{{ $errorId }}" class="sr-only">
            Klik untuk melaporkan masalah ini kepada pentadbir sistem
        </div>
    </div>

    {{-- Fallback Content Area --}}
    @if (isset($errorData['fallback_data']))
        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md border-l-4 border-gray-300 dark:border-gray-500">
            <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">
                Data Cadangan
            </h4>

            @if (isset($errorData['fallback_data']['stats']))
                {{-- Stats Widget Fallback --}}
                <div class="grid grid-cols-1 gap-2">
                    @foreach ($errorData['fallback_data']['stats'] as $stat)
                        <div class="text-center p-2 bg-white dark:bg-gray-600 rounded border">
                            <div class="text-lg font-semibold text-gray-500 dark:text-gray-400">
                                {{ $stat['value'] }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">
                                {{ $stat['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(isset($errorData['fallback_data']['chart']))
                {{-- Chart Widget Fallback --}}
                <div class="text-center p-4 bg-white dark:bg-gray-600 rounded border">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Carta tidak dapat dipaparkan
                    </p>
                </div>
            @else
                {{-- Generic Content Fallback --}}
                <div class="text-center p-4 bg-white dark:bg-gray-600 rounded border">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $errorData['fallback_data']['content']['message'] ?? 'Kandungan tidak tersedia' }}
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- Loading State (hidden by default, shown during retry) --}}
    <div id="retry-loading-{{ $errorId }}"
        class="mt-4 items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-md hidden" role="status"
        aria-live="polite">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="text-sm text-blue-700 dark:text-blue-300">
            Sedang cuba semula...
        </span>
        <span class="sr-only">Sedang memuatkan semula {{ $widgetName }}</span>
    </div>
</div>

{{-- JavaScript for Error Handling --}}
<script>
    /**
     * Widget Error Handling JavaScript
     * 
     * Provides retry functionality and error reporting for failed widgets
     * with accessibility support and user feedback.
     */

    /**
     * Retry a failed widget
     */
    function retryWidget(widgetClass) {
        if (!widgetClass) {
            console.error('Widget class not provided for retry');
            return;
        }

        const errorId = '{{ $errorId }}';
        const loadingElement = document.getElementById(`retry-loading-${errorId}`);
        const retryButton = event.target;

        // Show loading state
        if (loadingElement) {
            loadingElement.classList.remove('hidden');
            loadingElement.classList.add('flex');
        }

        // Disable retry button
        retryButton.disabled = true;
        retryButton.classList.add('opacity-50', 'cursor-not-allowed');

        // Announce retry attempt to screen readers
        announceToScreenReader(`Sedang cuba semula ${widgetClass.split('\\').pop()}`);

        // Simulate retry (in real implementation, this would trigger widget reload)
        setTimeout(() => {
            // Hide loading state
            if (loadingElement) {
                loadingElement.classList.add('hidden');
                loadingElement.classList.remove('flex');
            }

            // Re-enable retry button
            retryButton.disabled = false;
            retryButton.classList.remove('opacity-50', 'cursor-not-allowed');

            // In real implementation, trigger widget refresh via Livewire
            if (window.Livewire) {
                window.Livewire.emit('retryWidget', widgetClass);
            }

            announceToScreenReader('Percubaan selesai');
        }, 2000);
    }

    /**
     * Report widget error to administrators
     */
    function reportWidgetError(errorId, widgetClass) {
        if (!errorId || !widgetClass) {
            console.error('Error ID or widget class not provided for reporting');
            return;
        }

        // Show confirmation
        if (confirm('Adakah anda pasti untuk melaporkan masalah ini kepada pentadbir sistem?')) {
            // In real implementation, send error report via API
            fetch('/api/widget-errors/report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        error_id: errorId,
                        widget_class: widgetClass,
                        user_feedback: 'User reported widget error'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        announceToScreenReader('Laporan masalah telah dihantar kepada pentadbir');
                        alert('Laporan masalah telah dihantar. Terima kasih atas maklum balas anda.');
                    } else {
                        announceToScreenReader('Gagal menghantar laporan masalah');
                        alert('Maaf, gagal menghantar laporan. Sila cuba lagi kemudian.');
                    }
                })
                .catch(error => {
                    console.error('Error reporting widget error:', error);
                    announceToScreenReader('Ralat semasa menghantar laporan');
                    alert('Ralat berlaku semasa menghantar laporan. Sila cuba lagi kemudian.');
                });
        }
    }

    /**
     * Announce message to screen readers
     */
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;

        document.body.appendChild(announcement);

        // Remove after announcement
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    /**
     * Start retry countdown if applicable
     */
    @if ($nextRetryIn)
        document.addEventListener('DOMContentLoaded', function() {
            let countdown = {{ $nextRetryIn }};
            const countdownElement = document.getElementById('retry-countdown-{{ $errorId }}');

            if (countdownElement) {
                const timer = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;

                    if (countdown <= 0) {
                        clearInterval(timer);
                        countdownElement.textContent = '0';

                        // Auto-retry when countdown reaches zero
                        setTimeout(() => {
                            const retryButton = document.querySelector(
                                `[onclick="retryWidget('{{ $errorData['widget_class'] ?? '' }}')"]`
                            );
                            if (retryButton && !retryButton.disabled) {
                                retryButton.click();
                            }
                        }, 500);
                    }
                }, 1000);
            }
        });
    @endif
</script>

{{-- Styles for better visual hierarchy --}}
<style>
    .widget-error-fallback {
        /* Ensure proper contrast ratios for WCAG 2.2 AA compliance */
        --error-bg: theme('colors.red.50');
        --error-border: theme('colors.red.200');
        --error-text: theme('colors.red.800');
    }

    .dark .widget-error-fallback {
        --error-bg: theme('colors.red.900/10');
        --error-border: theme('colors.red.800');
        --error-text: theme('colors.red.200');
    }

    /* Focus styles for accessibility */
    .widget-error-fallback button:focus {
        outline: 2px solid theme('colors.red.500');
        outline-offset: 2px;
    }

    /* Animation for loading state */
    @keyframes pulse-error {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .widget-error-fallback .animate-pulse-error {
        animation: pulse-error 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .widget-error-fallback {
            padding: 1rem;
        }

        .widget-error-fallback .flex-col {
            gap: 0.5rem;
        }
    }
</style>
