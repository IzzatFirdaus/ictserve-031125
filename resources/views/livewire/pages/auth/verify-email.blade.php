{{--
/**
 * Component name: Verify Email Page (Volt)
 * Description: WCAG 2.2 AA compliant bilingual email verification page
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1 (Email Verification)
 * @trace D03-FR-022.1 (Authentication Flow)
 * @trace D04 §6.1 (Security)
 * @trace D10 §7 (Source Code Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (Style Guide)
 * @trace Requirements 15.4, 15.5 (Email Verification Flow)
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 2.0.0
 * @created 2025-11-03
 * @updated 2025-12-02 - Task 13.2: Enhanced with bilingual messages and improved UX
 */
--}}
<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Track if verification email was recently sent
     */
    public bool $emailSent = false;

    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        $this->emailSent = true;
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    /**
     * Get the user's email address (masked for privacy)
     */
    public function getMaskedEmail(): string
    {
        $email = Auth::user()->email;
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return $email;
        }

        $local = $parts[0];
        $domain = $parts[1];

        // Mask local part, showing first 2 and last 2 characters
        if (strlen($local) > 4) {
            $masked = substr($local, 0, 2) . str_repeat('*', strlen($local) - 4) . substr($local, -2);
        } else {
            $masked = substr($local, 0, 1) . str_repeat('*', strlen($local) - 1);
        }

        return $masked . '@' . $domain;
    }
}; ?>

<div>
    {{-- Page Header --}}
    <div class="mb-6 text-center">
        <div class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('auth.verify_email_title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('auth.verify_email_subtitle') }}
        </p>
    </div>

    {{-- Verification Message --}}
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    {{ __('auth.verify_email_message') }}
                </p>
                <p class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                    <strong>{{ __('auth.email') }}:</strong> {{ $this->getMaskedEmail() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('status') == 'verification-link-sent' || $emailSent)
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg" role="alert" aria-live="polite">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ __('auth.verify_email_sent') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="space-y-4">
        {{-- Resend Verification Button --}}
        <x-primary-button
            wire:click="sendVerification"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="w-full justify-center">
            <span wire:loading.remove wire:target="sendVerification" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ __('auth.resend_verification_button') }}
            </span>
            <span wire:loading wire:target="sendVerification" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('common.processing') }}
            </span>
        </x-primary-button>

        {{-- Logout Button --}}
        <button
            wire:click="logout"
            type="button"
            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {{ __('auth.logout') }}
        </button>
    </div>

    {{-- Help Text --}}
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ __('auth.need_help') }}
            <a href="{{ route('contact') }}" class="text-primary-600 hover:text-primary-500 underline">
                {{ __('auth.contact_support') }}
            </a>
        </p>
    </div>
</div>