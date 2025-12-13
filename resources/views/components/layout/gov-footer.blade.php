{{--
    GovFooter Component

    Official government footer with Jata Negara (inverted for dark background) and MOTAC branding.
    Used on all pages (guest, authenticated, admin).

    Requirements: 21.9, 22.5
    Design Reference: D12 §9, D13 §6, D14 §8
    MyGOV DSS v2.1.0 Compliance
    WCAG 2.2 AA Compliance: SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
--}}

<footer class="bg-gray-900 text-white py-8" role="contentinfo" aria-label="{{ __('footer.footer_navigation') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between">
            {{-- Ministry Branding with Jata Negara (inverted for dark background) --}}
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                {{-- Jata Negara (Malaysian Coat of Arms) - Inverted filter for dark background --}}
                {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
                <img src="{{ asset('images/jata-negara.svg') }}"
                     alt="{{ __('common.jata_negara') }}"
                     class="h-10 w-auto brightness-0 invert p-1"
                     width="40"
                     height="40"
                     loading="lazy"
                     decoding="async"
                     aria-label="{{ __('common.jata_negara') }}">

                <div>
                    {{-- Ministry Full Name from translation --}}
                    <p class="font-semibold text-sm sm:text-base">
                        {{ __('common.motac_full_name') }}
                    </p>
                    {{-- Government Disclaimer - "Sistem Rasmi Kerajaan Malaysia" --}}
                    <p class="text-sm text-gray-400">
                        {{ __('common.gov_disclaimer') }}
                    </p>
                </div>
            </div>

            {{-- Copyright with BPM name --}}
            <div class="text-center md:text-right">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} {{ __('common.bpm_full_name') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('common.all_rights_reserved') }}
                </p>
            </div>
        </div>
    </div>
</footer>
