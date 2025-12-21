{{--
    Impersonation Banner Component

    Displays a fixed notification banner when an admin is impersonating a user.
    Shows the impersonator's name, the impersonated user, and a stop button.

    WCAG 2.2 AA Compliant:
    - High contrast colors (yellow background, dark text)
    - Proper ARIA labels for screen readers
    - Keyboard accessible stop button
    - Focus visible indicators

    @trace D03-FR-002.5 (Impersonation Security)
    @trace D04 §5.0.4 (Impersonation Banner)
    @trace D12 §8 (Accessibility Standards)

    @version 1.0.0
    @author Pasukan BPM MOTAC
    @created 2025-11-26
--}}

@php
    use App\Models\User;
    use Illuminate\Support\Facades\Session;

    $isImpersonating = Session::has('impersonator_id');
    $impersonatorId = Session::get('impersonator_id');
    $impersonator = $impersonatorId ? User::find($impersonatorId) : null;
    $currentUser = auth()->user();
@endphp

@if($isImpersonating && $impersonator && $currentUser)
    <div
        role="alert"
        aria-live="polite"
        class="fixed top-0 left-0 right-0 z-50 bg-warning-400 border-b-2 border-warning-600 shadow-lg print:hidden"
        style="z-index: 9999;"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-2">
                {{-- Left: Impersonation indicator --}}
                <div class="flex items-center gap-3">
                    {{-- Icon --}}
                    <span class="shrink-0" aria-hidden="true">
                        <svg class="h-5 w-5 text-warning-800" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>

                    {{-- Message --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                        <span class="text-sm font-bold text-warning-900">
                            {{ __('impersonation.impersonation_active') }}
                        </span>
                        <span class="hidden sm:inline text-warning-800" aria-hidden="true">•</span>
                        <span class="text-sm text-warning-800">
                            {{ __('impersonation.impersonating_user', ['name' => $currentUser->name]) }}
                        </span>
                        <span class="hidden md:inline text-warning-800" aria-hidden="true">•</span>
                        <span class="hidden md:inline text-xs text-warning-700">
                            {{ __('impersonation.logged_in_as_admin', ['admin' => $impersonator->name]) }}
                        </span>
                    </div>
                </div>

                {{-- Right: Stop button --}}
                <div class="shrink-0 ml-4">
                    <a
                        href="{{ route('impersonate.stop') }}"
                        class="inline-flex items-center gap-2 px-3 py-1.5 min-h-11 text-sm font-semibold text-white bg-warning-800 hover:bg-warning-900 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-offset-amber-400 focus-visible:ring-white transition-colors duration-150"
                        role="button"
                        aria-label="{{ __('impersonation.stop_impersonation') }} - {{ __('impersonation.return_to_admin') }}"
                    >
                        {{-- Stop icon --}}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="hidden sm:inline">{{ __('impersonation.stop_impersonation') }}</span>
                        <span class="sm:hidden">{{ __('impersonation.return_to_admin') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Spacer to prevent content from being hidden behind the banner --}}
    <div class="h-12 print:hidden" aria-hidden="true"></div>
@endif

