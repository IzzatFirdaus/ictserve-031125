{{--
/**
 * Component name: Welcome Navigation
 * Description: Landing page navigation with authentication links for unauthenticated users
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1 (Authentication)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D15 v3.6.0 (Bahasa Melayu)
 * @wcag SC 2.5.8 (Touch Targets), SC 2.4.7 (Focus Visible)
 * @version 3.6.0
 * @created 2025-11-03
 * @updated 2025-12-15
 */
--}}
<nav class="flex flex-1 items-center justify-end gap-2" role="navigation" aria-label="{{ __('navigation.main') }}">
    {{-- Theme Toggle (v3.6.1) --}}
    <livewire:components.theme-toggle-unified />

    @auth
        <a href="{{ url('/dashboard') }}"
            class="rounded-m px-4 py-2.5 min-h-11 flex items-center text-sm font-medium text-gray-700 dark:text-gray-200
                   bg-white/80 dark:bg-gray-800/80 shadow-button
                   ring-1 ring-gray-200 dark:ring-gray-700
                   transition-colors duration-200
                   hover:bg-gray-100 dark:hover:bg-gray-700
                   focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
            {{ __('Dashboard') }}
        </a>
    @else
        <a href="{{ route('login') }}"
            class="rounded-m px-4 py-2.5 min-h-11 flex items-center text-sm font-medium text-gray-700 dark:text-gray-200
                   bg-white/80 dark:bg-gray-800/80 shadow-button
                   ring-1 ring-gray-200 dark:ring-gray-700
                   transition-colors duration-200
                   hover:bg-gray-100 dark:hover:bg-gray-700
                   focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
            {{ __('Log Masuk') }}
        </a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="rounded-m px-4 py-2.5 min-h-11 flex items-center text-sm font-medium text-white
                       bg-primary-600 shadow-button
                       transition-colors duration-200
                       hover:bg-primary-700
                       focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                {{ __('Daftar') }}
            </a>
        @endif
    @endauth
</nav>
