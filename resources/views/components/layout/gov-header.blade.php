{{--
    GovHeader Component

    Official government header with Jata Negara and MOTAC branding.
    Used on all public-facing pages (guest forms, status check, approval pages).

    Requirements: 21.1, 21.2, 21.9
    Design Reference: D12 §2, D13 §3, D14 §1
    MyGOV DSS v2.1.0 Compliance
--}}

<header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center space-x-4">
            {{-- Jata Negara (Malaysian Coat of Arms) - Minimum 48x48 pixels per MyGOV DSS v2.1.0 --}}
            {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
            <img src="{{ asset('images/jata-negara.svg') }}"
                 alt="{{ __('common.jata_negara') }}"
                 class="h-12 w-auto p-1"
                 width="48"
                 height="48"
                 aria-label="{{ __('common.jata_negara') }}">

            {{-- MOTAC Logo - 40x40 with proper spacing (minimum 8px clear space) per D14 §1, Requirement 22.2 --}}
            <img src="{{ asset('images/motac-logo.png') }}"
                 alt="{{ __('common.motac_logo') }}"
                 class="h-10 w-auto p-1"
                 width="40"
                 height="40"
                 aria-label="{{ __('common.motac_logo') }}">

            {{-- Ministry Name - Responsive layout (hide text on mobile, show on sm+) --}}
            <div class="hidden sm:block">
                <p class="text-sm font-semibold text-gray-900">
                    {{ __('common.motac_full_name') }}
                </p>
                <p class="text-xs text-gray-600">
                    {{ __('common.bpm_full_name') }}
                </p>
            </div>
        </div>
    </div>
</header>
