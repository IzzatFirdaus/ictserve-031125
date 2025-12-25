<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Dashboard Prestasi Laravel Pulse
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Pemantauan prestasi aplikasi secara masa nyata termasuk masa respons, query perlahan, dan
                        kesihatan sistem.
                    </p>
                </div>

                @if ($isPulseEnabled)
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                            Aktif
                        </span>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">
                            Tidak Aktif
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pulse Dashboard Embed --}}
        @if ($isPulseEnabled)
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">
                            Dashboard Pulse
                        </h3>
                        <a href="{{ $pulseUrl }}" target="_blank"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
                            aria-label="Buka Pulse dalam tab baru">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                            </svg>
                            Buka dalam Tab Baru
                        </a>
                    </div>
                </div>

                {{-- Iframe Container with WCAG 2.2 AA Compliance --}}
                <div class="relative" style="height: 800px;">
                    <iframe src="{{ $pulseUrl }}" class="w-full h-full border-0"
                        title="Laravel Pulse Dashboard - Pemantauan Prestasi Aplikasi"
                        aria-label="Dashboard prestasi Laravel Pulse yang menunjukkan metrik masa nyata"
                        sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-popups-to-escape-sandbox"
                        loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>

                    {{-- Loading Overlay --}}
                    <div id="pulse-loading"
                        class="absolute inset-0 bg-white dark:bg-gray-800 flex items-center justify-center"
                        aria-live="polite" aria-label="Memuatkan dashboard Pulse">
                        <div class="text-center">
                            <div
                                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400 mx-auto">
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Memuatkan Dashboard Pulse...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Prestasi Masa Nyata</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Pantau masa respons dan throughput</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Query Perlahan</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Kesan dan optimumkan query database</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Ralat & Pengecualian</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Pantau dan selesaikan ralat aplikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Pulse Disabled State --}}
            <div
                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Laravel Pulse Tidak Aktif
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Laravel Pulse tidak diaktifkan dalam konfigurasi aplikasi. Sila hubungi pentadbir sistem
                            untuk mengaktifkannya.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- JavaScript for iframe loading --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iframe = document.querySelector('iframe[src*="pulse"]');
                const loadingOverlay = document.getElementById('pulse-loading');

                if (iframe && loadingOverlay) {
                    iframe.addEventListener('load', function() {
                        loadingOverlay.style.display = 'none';
                    });

                    // Fallback: hide loading after 10 seconds
                    setTimeout(function() {
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                    }, 10000);
                }
            });
        </script>
    @endpush
</x-filament-panels::page>
