{{--
/**
 * Component: Offline Indicator
 * Description: WCAG 2.2 AA compliant offline status indicator for PWA support
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.4 (Mobile Performance)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Automatic online/offline detection
 * - ARIA live region for screen reader announcements
 * - Smooth slide-in animation
 * - Reduced motion support
 * - Dismissible notification
 *
 * Usage:
 * <x-responsive.offline-indicator />
 */
--}}

@props([
    'offlineMessage' => __('Anda sedang di luar talian. Sesetengah ciri mungkin tidak tersedia.'),
    'onlineMessage' => __('Sambungan dipulihkan.'),
])

<div x-data="{
    online: navigator.onLine,
    showOnlineMessage: false,
    dismissed: false,

    init() {
        window.addEventListener('online', () => {
            this.online = true;
            this.showOnlineMessage = true;
            this.dismissed = false;
            setTimeout(() => {
                this.showOnlineMessage = false;
            }, 3000);
        });

        window.addEventListener('offline', () => {
            this.online = false;
            this.dismissed = false;
        });
    },

    dismiss() {
        this.dismissed = true;
    }
}" {{ $attributes }}>

    {{-- Offline Banner --}}
    <div x-show="!online && !dismissed" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
        x-transition:leave-end="-translate-y-full" class="fixed top-0 inset-x-0 z-9999 bg-warning-500 text-white"
        role="alert" aria-live="assertive" x-cloak>
        <div class="flex items-center justify-between px-4 py-2 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                {{-- Offline icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
                </svg>
                <span class="text-sm font-medium">{{ $offlineMessage }}</span>
            </div>

            {{-- Dismiss button --}}
            <button type="button" @click="dismiss()"
                class="shrink-0 p-1 rounded-full hover:bg-warning-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-warning-500"
                aria-label="{{ __('Tutup pemberitahuan') }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Online Restored Banner --}}
    <div x-show="showOnlineMessage" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
        x-transition:leave-end="-translate-y-full" class="fixed top-0 inset-x-0 z-9999 bg-success-500 text-white"
        role="status" aria-live="polite" x-cloak>
        <div class="flex items-center justify-center px-4 py-2 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                {{-- Online icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                </svg>
                <span class="text-sm font-medium">{{ $onlineMessage }}</span>
            </div>
        </div>
    </div>

    {{-- Screen reader only status --}}
    <div class="sr-only" aria-live="polite" aria-atomic="true">
        <span x-show="!online">{{ $offlineMessage }}</span>
        <span x-show="showOnlineMessage">{{ $onlineMessage }}</span>
    </div>
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {

        [x-transition\:enter],
        [x-transition\:leave] {
            transition-duration: 0.01ms !important;
        }
    }
</style>
