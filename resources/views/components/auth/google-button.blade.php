{{--
/**
 * Component: Google SSO Button
 * Description: Google OAuth sign-in button with @motac.gov.my validation
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.3 (Google SSO)
 * @trace Requirements 15.6, 15.7
 * @version 3.5.0
 * @created 2025-12-07
 */
--}}

@props(['redirect' => null])

<div {{ $attributes->merge(['class' => 'w-full']) }} x-data="{ loading: false }">
    <a
        href="{{ route('auth.google.redirect', ['redirect' => $redirect]) }}"
        x-on:click="loading = true"
        :aria-disabled="loading"
        :class="loading ? 'pointer-events-none opacity-70 cursor-not-allowed' : ''"
        class="flex items-center justify-center gap-3 w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 transition-colors duration-150"
        aria-label="{{ __('auth.google_sign_in') }}"
    >
        {{-- Google Logo --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>

        <span class="text-sm font-medium text-gray-700 dark:text-gray-200" x-show="!loading">
            {{ __('auth.google_sign_in') }}
        </span>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 inline-flex items-center gap-2" x-show="loading">
            <svg class="animate-spin h-4 w-4 text-primary-600" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('auth.sso_loading') }}
        </span>
    </a>

    <p class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
        {{ __('auth.google_sign_in_description') }}
    </p>
</div>
