{{--
/**
 * Component: Site Footer
 * Description: WCAG 2.2 AA compliant footer with accessibility links and MOTAC branding
 * Author: Pasukan BPM MOTAC
 * Requirements: 5.1, 6.1, 6.2, 14.1, 14.5
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7)
 * Version: 1.0.0
 * Created: 2025-11-03
 * Last Updated: 2025-11-03
 */
--}}

<footer class="bg-gray-800 text-white mt-auto" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- About Section --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('About ICTServe') }}</h3>
                <p class="text-gray-300 text-sm leading-relaxed">
                    {{ __('ICTServe is the official ICT service management system for MOTAC BPM, providing helpdesk support and asset loan management services.') }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Quick Links') }}</h3>
                <nav aria-label="{{ __('Footer navigation') }}">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('welcome') }}"
                                class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                                {{ __('Home') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('helpdesk.create') }}"
                                class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                                {{ __('Submit Helpdesk Ticket') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('loans.create') }}"
                                class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                                {{ __('Apply for Asset Loan') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}"
                                class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                                {{ __('Staff Login') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            {{-- Contact & Accessibility --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Contact & Support') }}</h3>
                <ul class="space-y-2 text-sm">
                    <li class="text-gray-300">
                        <span class="font-medium">{{ __('Email:') }}</span>
                        <a href="mailto:support@motac.gov.my"
                            class="text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-1">
                            support@motac.gov.my
                        </a>
                    </li>
                    <li class="text-gray-300">
                        <span class="font-medium">{{ __('Phone:') }}</span>
                        <a href="tel:+60380009999"
                            class="text-blue-400 hover:text-blue-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-1">
                            +603 8000 9999
                        </a>
                    </li>
                    <li class="mt-4">
                        <a href="#"
                            class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                            {{ __('Accessibility Statement') }}
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-md px-2 py-1 inline-block transition-colors duration-200">
                            {{ __('Privacy Policy') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Copyright & Compliance --}}
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
            <p>
                &copy; {{ date('Y') }} {{ __('Ministry of Tourism, Arts and Culture Malaysia (MOTAC)') }}.
                {{ __('All rights reserved.') }}
            </p>
            <p class="mt-2">
                {{ __('WCAG 2.2 Level AA Compliant') }} |
                {{ __('PDPA 2010 Compliant') }} |
                {{ __('MyGOV Digital Service Standards v2.1.0') }}
            </p>
        </div>
    </div>
</footer>
