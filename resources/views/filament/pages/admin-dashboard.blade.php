{{-- ICTServe Admin Dashboard - WCAG 2.2 AA Compliant --}}
{{-- D12-D14 UI/UX Design Guidelines Implementation --}}

<x-filament-panels::page>
    {{-- Main content area with skip link target --}}
    <div id="main-content" tabindex="-1">
        {{-- Page Header with Bahasa Melayu --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Papan Pemuka Pentadbir') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Selamat datang ke sistem pengurusan ICTServe. Pantau aktiviti dan prestasi sistem di sini.') }}
            </p>
        </div>

        {{-- Header Widgets Section --}}
        @if ($this->getHeaderWidgets())
            <section aria-labelledby="header-widgets-title" class="mb-6">
                <h2 id="header-widgets-title" class="sr-only">
                    {{ __('Widget Maklumat Utama') }}
                </h2>
                <x-filament-widgets::widgets
                    :widgets="$this->getHeaderWidgets()"
                    :columns="$this->getHeaderWidgetsColumns()"
                />
            </section>
        @endif

        {{-- Main Dashboard Content --}}
        <main role="main" aria-labelledby="dashboard-content-title">
            <h2 id="dashboard-content-title" class="sr-only">
                {{ __('Kandungan Papan Pemuka') }}
            </h2>

            {{-- Widgets Grid --}}
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="$this->getColumns()"
            />
        </main>

        {{-- Footer Widgets Section --}}
        @if ($this->getFooterWidgets())
            <section aria-labelledby="footer-widgets-title" class="mt-6">
                <h2 id="footer-widgets-title" class="sr-only">
                    {{ __('Widget Maklumat Tambahan') }}
                </h2>
                <x-filament-widgets::widgets
                    :widgets="$this->getFooterWidgets()"
                    :columns="$this->getFooterWidgetsColumns()"
                />
            </section>
        @endif
    </div>

    {{-- Live Updates Status Indicator --}}
    <div class="fixed bottom-4 right-4 z-50" x-data="{ show: false }" x-show="show" x-transition>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-medium">{{ __('Data dikemas kini') }}</span>
            </div>
        </div>
    </div>

    {{-- JavaScript for Live Updates --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for Livewire updates
            document.addEventListener('livewire:updated', function() {
                // Show update indicator
                Alpine.store('updateIndicator', true);

                // Hide after 3 seconds
                setTimeout(() => {
                    Alpine.store('updateIndicator', false);
                }, 3000);
            });

            // Announce updates to screen readers
            const announcer = document.createElement('div');
            announcer.setAttribute('aria-live', 'polite');
            announcer.setAttribute('aria-atomic', 'true');
            announcer.className = 'sr-only';
            document.body.appendChild(announcer);

            document.addEventListener('livewire:updated', function() {
                announcer.textContent = '{{ __("Data papan pemuka telah dikemas kini") }}';
                setTimeout(() => {
                    announcer.textContent = '';
                }, 1000);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
