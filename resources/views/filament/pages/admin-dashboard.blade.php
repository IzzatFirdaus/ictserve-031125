{{-- 
    ICTServe Admin Dashboard - MyDS Compliant Layout
    
    Implements D12-D14 UI/UX guidelines:
    - MyDS 12-8-4 responsive grid system
    - WCAG 2.2 AA accessibility compliance
    - Bahasa Melayu interface
    - Proper semantic HTML structure
--}}

<x-filament-panels::page>
    {{-- Skip link for accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 
              bg-primary-600 text-white px-4 py-2 rounded-md z-50 
              focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        tabindex="1">
        Langkau ke kandungan utama
    </a>

    {{-- Main content landmark for skip link accessibility --}}
    <main id="main-content" role="main" aria-label="Kandungan utama papan pemuka">

        {{-- Dashboard Header Section --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- MOTAC Logo Icon --}}
                    <img src="{{ asset('images/motac-logo.png') }}" alt="Logo MOTAC" class="h-12 w-auto">
                    <div>
                        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                            Papan Pemuka Pentadbir ICTServe
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Gambaran keseluruhan sistem ICTServe MOTAC
                        </p>
                    </div>
                </div>

                {{-- Real-time status indicator --}}
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" aria-label="Status: Dalam talian">
                    </div>
                    <span>Kemaskini terakhir: <time
                            datetime="{{ now()->toISOString() }}">{{ now()->format('H:i') }}</time></span>
                </div>
            </div>
        </div>

        {{-- Header Widgets Section (Critical Alerts & Overview Stats) --}}
        @if ($this->getHeaderWidgets())
            <section aria-label="Widget utama" class="mb-8">
                <div class="grid grid-cols-4 gap-4.5 md:grid-cols-8 md:gap-6 lg:grid-cols-12 lg:gap-6">
                    @foreach ($this->getHeaderWidgets() as $widget)
                        @php
                            $widgetClass = match (class_basename($widget)) {
                                'EnhancedRealTimeDashboardWidget' => 'col-span-4 md:col-span-8 lg:col-span-12',
                                'UnifiedDashboardOverview' => 'col-span-4 md:col-span-8 lg:col-span-12',
                                'CriticalAlertsWidget' => 'col-span-4 md:col-span-8 lg:col-span-12',
                                'HelpdeskStatsOverview' => 'col-span-4 md:col-span-4 lg:col-span-6',
                                'LoanApprovalQueueWidget' => 'col-span-4 md:col-span-4 lg:col-span-6',
                                default => 'col-span-4 md:col-span-4 lg:col-span-4',
                            };
                        @endphp

                        <div class="{{ $widgetClass }}">
                            @livewire($widget, ['lazy' => false])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Main Content Section --}}
        <div class="grid grid-cols-4 gap-4.5 md:grid-cols-8 md:gap-6 lg:grid-cols-12 lg:gap-6 mb-8">

            {{-- Quick Actions Panel --}}
            <aside class="col-span-4 md:col-span-3 lg:col-span-4" role="complementary" aria-label="Tindakan pantas">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Tindakan Pantas
                    </h2>

                    <nav aria-label="Menu tindakan pantas">
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ url('/admin/operations/helpdesk-tickets/create') }}"
                                    class="flex items-center gap-3 p-3 text-sm font-medium text-gray-700 dark:text-gray-300 
                                          bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900 
                                          hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200
                                          focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <x-heroicon-o-ticket class="w-5 h-5" />
                                    Cipta Tiket Baharu
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/operations/loan-applications/create') }}"
                                    class="flex items-center gap-3 p-3 text-sm font-medium text-gray-700 dark:text-gray-300 
                                          bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900 
                                          hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200
                                          focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <x-heroicon-o-document-plus class="w-5 h-5" />
                                    Permohonan Pinjaman
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/helpdesk-reports') }}"
                                    class="flex items-center gap-3 p-3 text-sm font-medium text-gray-700 dark:text-gray-300 
                                          bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900 
                                          hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200
                                          focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                                    Laporan Sistem
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <section class="col-span-4 md:col-span-5 lg:col-span-8" aria-label="Kandungan utama">
                @if ($this->getMainContentWidgets())
                    <div class="space-y-6">
                        @foreach ($this->getMainContentWidgets() as $widget)
                            <div class="w-full">
                                @livewire($widget, ['lazy' => false])
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Charts Section (Bottom of Page) --}}
        <section class="mt-8" aria-label="Carta dan analitik" x-data="{
            expandedCharts: false,
            chartHeight: '350px',
            init() {
                // Watch for changes and update chart height with proper transitions
                this.$watch('expandedCharts', (value) => {
                    this.chartHeight = value ? '500px' : '350px';
        
                    // Trigger chart resize after transition completes
                    this.$nextTick(() => {
                        setTimeout(() => {
                            // Dispatch resize event to charts
                            window.dispatchEvent(new Event('resize'));
        
                            // Trigger Livewire chart refresh if available
                            if (window.Livewire) {
                                window.Livewire.dispatch('chart-resize', { expanded: value });
                            }
                        }, 350); // Wait for CSS transition to complete
                    });
                });
            }
        }">
            {{-- Chart Section Header with Toggle Control --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Carta & Analitik
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Visualisasi data sistem dalam bentuk carta interaktif
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Saiz Carta:</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="expandedCharts" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            <span x-show="!expandedCharts">Kecil</span>
                            <span x-show="expandedCharts">Besar</span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Chart Widgets Grid with Improved Resizing --}}
            @if ($this->getChartWidgets())
                <div class="grid gap-6 transition-all duration-300 ease-in-out"
                    :class="expandedCharts ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'">
                    @foreach ($this->getChartWidgets() as $widget)
                        <div class="w-full">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 transition-all duration-300 ease-in-out overflow-hidden"
                                :style="{
                                    minHeight: chartHeight,
                                    height: chartHeight,
                                    maxHeight: chartHeight
                                }"
                                x-transition:enter="transition-all duration-300 ease-in-out"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">
                                <div class="h-full w-full">
                                    @livewire($widget, ['lazy' => true])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Tiada carta tersedia pada masa ini.</p>
                </div>
            @endif
        </section>

        {{-- System Status Footer --}}
        <footer class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700" role="contentinfo">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-4">
                    <span>Sistem ICTServe v3.6.1</span>
                    <span>•</span>
                    <span>Laravel {{ \Illuminate\Foundation\Application::VERSION }}</span>
                    <span>•</span>
                    <span>Filament v4.3.1</span>
                </div>

                <div class="flex items-center gap-2">
                    @php
                        try {
                            $activeUsers = (int) \App\Models\User::where('is_active', 1)
                                ->where('last_login_at', '>', now()->subHours(24))
                                ->count();
                        } catch (\Exception $e) {
                            $activeUsers = 0;
                        }
                    @endphp
                    <span>Pengguna aktif: {{ $activeUsers }}</span>
                </div>
            </div>
        </footer>
    </main>
</x-filament-panels::page>
