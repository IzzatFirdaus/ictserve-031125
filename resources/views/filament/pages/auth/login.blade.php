{{--
    Reverted admin login page to mimic the normal user login layout per user request.
    This view matches the public `resources/views/livewire/pages/auth/login.blade.php` structure
    while rendering Filament's form via `{{ $this->form }}` to maintain authentication logic.

    Keperluan: Bahasa Melayu exclusive; accessible; responsive; consistent with public login styling.
--}}

<x-theme-init />

@livewireStyles
<link rel="stylesheet" href="/css/app.css">

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script defer src="/vendor/livewire/livewire.js"></script>
<script defer src="/js/app.js"></script>

<div>
    {{-- Page Title (MyDS Typography - D13 §2.4) --}}
    <h1 class="text-3xl font-bold font-heading text-center text-slate-900 dark:text-white mb-6">
        {{ __('auth.login_title') }}
    </h1>

    <p class="text-center text-slate-600 dark:text-slate-400 mb-8 font-body leading-relaxed max-w-md mx-auto">
        {{ __('auth.login_subtitle') }}
    </p>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('sso_fallback'))
        <div class="mb-4 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-900" role="alert">
            <p class="font-semibold">{{ __('auth.google_sso_unavailable') }}</p>
            <p class="mt-1">{{ __('auth.sso_fallback_available') }}</p>
        </div>
    @endif

    <div class="space-y-6 mb-8">
        <x-auth.google-button />
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 font-body">
            <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
            <span class="uppercase tracking-wide font-medium px-3 bg-white dark:bg-slate-800 text-xs">{{ __('auth.or_separator') }}</span>
            <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
        </div>
    </div>

    <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6" id="login-form">
        @csrf

        <div class="space-y-2">
            <x-input-label for="email" :value="__('auth.email_or_username')" class="text-slate-900 dark:text-white font-medium font-body" />
            <input type="text" name="email" id="email" required autofocus autocomplete="username" placeholder="{{ __('auth.email_or_username_placeholder') }}" class="block w-full min-h-11 px-4 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 font-body transition-colors duration-200" />
            <p class="text-sm text-slate-600 dark:text-slate-400 font-body">{{ __('auth.flexible_login_hint') }}</p>
        </div>

        <div class="space-y-2">
            <x-input-label for="password" :value="__('auth.password')" class="text-slate-900 dark:text-white font-medium font-body" />
            <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="{{ __('auth.password_placeholder') }}" class="block w-full min-h-11 px-4 py-3 border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 font-body transition-colors duration-200" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <label for="remember" class="inline-flex items-center cursor-pointer min-h-11 py-2 group">
                <input id="remember" type="checkbox" name="remember" value="1" class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-600 text-primary-600 shadow-sm focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-slate-800 transition-colors duration-200" />
                <span class="ms-3 text-sm text-slate-600 dark:text-slate-400 font-body group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors duration-200">{{ __('auth.remember_me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-slate-800 rounded-lg min-h-11 inline-flex items-center px-2 font-body transition-colors duration-200" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
            @endif
        </div>

        <div>
            <button type="submit" class="w-full min-h-11 px-6 py-3 text-base font-medium font-body text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 rounded-lg shadow-button transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                {{ __('auth.login_button') }}
            </button>
        </div>
    </form>
