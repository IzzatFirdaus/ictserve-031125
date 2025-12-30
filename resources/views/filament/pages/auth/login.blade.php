{{--
    ICTServe Admin Login Page - MyDS Design System v2025.2
    
    Implements:
    - MOTAC branding with logo and theme switcher
    - MyDS shadow-card elevation
    - WCAG 2.2 AA accessibility compliance
    - Focus indicators: 3px ring with 2px offset
    - Minimum 44px touch targets
    - Bahasa Melayu exclusive interface
    
    @trace D03-FR-001.1 (Authentication), D12 §9 (WCAG 2.2 AA)
    @trace D13 §2.2-2.7 (MyDS), D14 §4 (MOTAC Branding)
    @version 3.6.1
--}}

<x-theme-init />

@livewireStyles
<link rel="stylesheet" href="/css/app.css">

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script defer src="/vendor/livewire/livewire.js"></script>
<script defer src="/js/app.js"></script>

{{-- Main Container - Centered Layout with MyDS Background --}}
<div class="min-h-screen flex items-center justify-center bg-washed dark:bg-gray-900 px-4 py-8 sm:px-6 lg:px-8"
    role="main" aria-labelledby="login-heading">

    <div class="w-full max-w-md space-y-8">
        {{-- MOTAC Branding Section (D14 §4 MOTAC Branding) --}}
        <div class="text-center">
            {{-- MOTAC Logo --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/motac-logo.png') }}"
                    alt="Logo MOTAC - Kementerian Pelancongan, Seni dan Budaya Malaysia"
                    class="h-16 w-auto object-contain" loading="eager">
            </div>

            {{-- Page Title (MyDS Typography - D13 §2.4) --}}
            <h1 id="login-heading" class="text-2xl sm:text-3xl font-bold font-heading text-gray-900 dark:text-white">
                {{ __('auth.login_title') }}
            </h1>

            <p class="mt-3 text-base text-gray-600 dark:text-gray-400 font-body leading-relaxed">
                {{ __('auth.login_subtitle') }}
            </p>
        </div>

        {{-- Login Card with MyDS Shadow (D14 §7.5) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 sm:p-8 theme-transition"
            style="box-shadow: var(--shadow-card);" role="region"
            aria-label="{{ __('filament.accessibility.login_form') ?? 'Borang Log Masuk' }}">

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- SSO Fallback Warning --}}
            @if (session('sso_fallback'))
                <div class="mb-6 rounded-lg border border-warning-200 dark:border-warning-700 bg-warning-50 dark:bg-warning-900/20 px-4 py-3 text-sm"
                    role="alert" aria-live="polite">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle
                            class="w-5 h-5 text-warning-600 dark:text-warning-400 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <p class="font-semibold text-warning-900 dark:text-warning-200">
                                {{ __('auth.google_sso_unavailable') }}</p>
                            <p class="mt-1 text-warning-700 dark:text-warning-300">
                                {{ __('auth.sso_fallback_available') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Google SSO Button --}}
            <div class="space-y-6 mb-6">
                <x-auth.google-button />

                {{-- Divider --}}
                <div class="flex items-center gap-4" role="separator" aria-orientation="horizontal">
                    <span class="flex-1 border-t border-gray-200 dark:border-gray-700"></span>
                    <span
                        class="text-xs uppercase tracking-wide font-medium text-gray-500 dark:text-gray-400 px-3 font-body">
                        {{ __('auth.or_separator') }}
                    </span>
                    <span class="flex-1 border-t border-gray-200 dark:border-gray-700"></span>
                </div>
            </div>

            {{-- Login Form --}}
            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6" id="login-form"
                aria-describedby="login-help">
                @csrf

                {{-- Email/Username Field --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white font-body">
                        {{ __('auth.email_or_username') }}
                        <span class="text-danger-600 dark:text-danger-400" aria-hidden="true">*</span>
                        <span class="sr-only">{{ __('filament.accessibility.required_field') ?? 'Medan wajib' }}</span>
                    </label>
                    <input type="text" name="email" id="email" required autofocus autocomplete="username"
                        placeholder="{{ __('auth.email_or_username_placeholder') }}" aria-describedby="email-help"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="block w-full min-h-11 px-4 py-3 
                                  border border-gray-300 dark:border-gray-600 
                                  bg-white dark:bg-gray-700 
                                  text-gray-900 dark:text-white 
                                  rounded-lg 
                                  focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                  dark:focus-visible:ring-offset-gray-800
                                  font-body text-base
                                  transition-colors duration-200
                                  @error('email') border-danger-600 dark:border-danger-400 @enderror" />

                    <p id="email-help" class="text-sm text-gray-600 dark:text-gray-400 font-body">
                        {{ __('auth.flexible_login_hint') }}
                    </p>

                    @error('email')
                        <p id="email-error"
                            class="flex items-center gap-2 text-sm text-danger-600 dark:text-danger-400 font-body"
                            role="alert">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" aria-hidden="true" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white font-body">
                        {{ __('auth.password') }}
                        <span class="text-danger-600 dark:text-danger-400" aria-hidden="true">*</span>
                        <span class="sr-only">{{ __('filament.accessibility.required_field') ?? 'Medan wajib' }}</span>
                    </label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        placeholder="{{ __('auth.password_placeholder') }}"
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        class="block w-full min-h-11 px-4 py-3 
                                  border border-gray-300 dark:border-gray-600 
                                  bg-white dark:bg-gray-700 
                                  text-gray-900 dark:text-white 
                                  rounded-lg 
                                  focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                  dark:focus-visible:ring-offset-gray-800
                                  font-body text-base
                                  transition-colors duration-200
                                  @error('password') border-danger-600 dark:border-danger-400 @enderror" />

                    @error('password')
                        <p id="password-error"
                            class="flex items-center gap-2 text-sm text-danger-600 dark:text-danger-400 font-body"
                            role="alert">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4 shrink-0" aria-hidden="true" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between pt-2">
                    {{-- Remember Me Checkbox --}}
                    <label for="remember" class="inline-flex items-center cursor-pointer min-h-11 py-2 group"
                        aria-label="{{ __('auth.remember_me') }}">
                        <input id="remember" type="checkbox" name="remember" value="1"
                            class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 
                                      text-primary-600 
                                      focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                      dark:focus-visible:ring-offset-gray-800
                                      transition-colors duration-200" />
                        <span
                            class="ms-3 text-sm text-gray-600 dark:text-gray-400 font-body 
                                     group-hover:text-gray-900 dark:group-hover:text-gray-200 
                                     transition-colors duration-200">
                            {{ __('auth.remember_me') }}
                        </span>
                    </label>

                    {{-- Forgot Password Link --}}
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-primary-600 dark:text-primary-400 
                                  hover:text-primary-700 dark:hover:text-primary-300 
                                  underline underline-offset-2
                                  focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                  dark:focus-visible:ring-offset-gray-800
                                  rounded-lg min-h-11 inline-flex items-center px-2 
                                  font-body transition-colors duration-200"
                            aria-label="{{ __('auth.forgot_password') }}">
                            {{ __('auth.forgot_password') }}
                        </a>
                    @endif
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full min-h-11 px-6 py-3 
                                   text-base font-medium font-body text-white 
                                   bg-primary-600 hover:bg-primary-700 
                                   focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                   dark:focus-visible:ring-offset-gray-800
                                   rounded-lg 
                                   transition-all duration-200 
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                        style="box-shadow: var(--shadow-button);">
                        {{ __('auth.login_button') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer Links --}}
        <div class="text-center text-sm text-gray-500 dark:text-gray-400 font-body">
            <p>
                {{ __('auth.need_help') ?? 'Perlukan bantuan?' }}
                <a href="{{ route('contact') ?? '#' }}"
                    class="text-primary-600 dark:text-primary-400 hover:underline 
                          focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 
                          rounded">
                    {{ __('auth.contact_support') ?? 'Hubungi Sokongan' }}
                </a>
            </p>
        </div>
    </div>
</div>
