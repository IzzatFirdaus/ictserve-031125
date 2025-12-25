{{-- 
    Custom Filament Admin Login Page (aligned with user login branding)
--}} 

@push('styles')
    <x-theme-init />
@endpush

@push('styles')
    @vite('resources/css/app.css')
@endpush

@push('scripts')
    @vite('resources/js/app.js')
@endpush

<div class="min-h-screen flex flex-col font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 theme-transition">
    {{-- Skip Link for Accessibility (WCAG 2.4.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2">
        {{ __('common.skip_to_content') }}
    </a>

    {{-- Theme Switcher (Top-right) --}}
    <div class="fixed top-4 right-4 z-50">
        <livewire:components.theme-toggle-unified />
    </div>

    {{-- Main Content --}}
    <main id="main-content"
        class="flex-1 flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pb-12 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
        tabindex="-1">
        {{-- Logo + Brand --}}
        <div class="mb-12">
            <a href="/" wire:navigate aria-label="{{ __('common.home') }}"
                class="flex flex-col items-center gap-6 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg p-4 min-h-11 min-w-11 transition-all duration-200 hover:scale-105">
                <x-application-logo
                    class="w-20 h-20 fill-current text-primary-600 dark:text-primary-400 transition-colors duration-200" />
                <span
                    class="text-2xl font-bold font-heading text-slate-900 dark:text-white tracking-tight">{{ config('app.name', 'ICTServe') }}</span>
            </a>
        </div>

        {{-- Login Card --}}
        <div
            class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-slate-800 shadow-card overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 theme-transition">

            <h1 class="text-3xl font-bold font-heading text-center text-slate-900 dark:text-white mb-6">
                Log Masuk Pentadbir
            </h1>

            <p class="text-center text-slate-600 dark:text-slate-400 mb-8 font-body leading-relaxed max-w-md mx-auto">
                Sila log masuk untuk mengakses papan pemuka pentadbir
            </p>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-success-800" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('sso_fallback'))
                <div class="mb-4 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-warning-800" role="alert">
                    <p class="font-semibold">{{ __('auth.google_sso_unavailable') }}</p>
                    <p class="mt-1">{{ __('auth.sso_fallback_available') }}</p>
                </div>
            @endif

            {{-- Google SSO --}}
            <div class="space-y-6 mb-8">
                <x-auth.google-button redirect="{{ route('filament.admin.pages.admin-dashboard') }}" />
                <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 font-body">
                    <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
                    <span
                        class="uppercase tracking-wide font-medium px-3 bg-white dark:bg-slate-800 text-xs">{{ __('auth.or_separator') }}</span>
                    <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
                </div>
            </div>

            {{-- Login Form --}}
            <form wire:submit="authenticate" class="space-y-6">
                {{ $this->form }}

                <div class="pt-4">
                    <button type="submit"
                        class="w-full min-h-11 px-6 py-3 text-base font-medium font-body text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 rounded-lg shadow-button transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary-600"
                        wire:loading.attr="disabled" wire:target="authenticate">
                        <span wire:loading.remove wire:target="authenticate" class="flex items-center justify-center">
                            {{ __('filament-panels::pages/auth/login.form.actions.authenticate.label') }}
                        </span>
                        <span wire:loading.flex wire:target="authenticate" class="hidden items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('auth.logging_in') }}
                        </span>
                    </button>
                </div>
            </form>

            {{-- Footer / Help --}}
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white dark:bg-slate-800 px-2 text-slate-500 dark:text-slate-400">
                            Perlukan bantuan?
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid gap-4">
                    <a href="{{ route('contact') }}"
                        class="flex w-full min-h-11 items-center justify-center gap-3 rounded-lg bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-100 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200">
                        <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        <span class="text-sm font-medium">Hubungi Meja Bantuan</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer Links --}}
        <div class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400 font-body">
            <p>{{ __('auth.need_help') }}
                <a href="{{ route('contact') }}"
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg min-h-11 inline-flex items-center px-2 transition-colors duration-200">
                    {{ __('auth.contact_support') }}
                </a>
            </p>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-4 text-center text-sm text-slate-500 dark:text-slate-400 theme-transition">
        <p>&copy; {{ date('Y') }} {{ __('common.motac_full_name') }}. {{ __('common.all_rights_reserved') }}</p>
    </footer>

    {{-- FAQ Bot Widget - Floating Chat Bot --}}
    @if (config('ollama.enabled', false))
        <div data-component="faq-bot-widget">
            <livewire:ollama.faq-bot-widget />
        </div>
    @endif
</div>
