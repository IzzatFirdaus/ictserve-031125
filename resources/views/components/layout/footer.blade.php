{{--
/**
 * Component name: Footer Layout
 * Description: Site-wide footer layout component with MOTAC branding, copyright information, and accessible navigation links.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Layout structure)
 * @trace D03-FR-018.1 (Branding)
 * @trace D04 §6.1 (Layout)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

{{--
/**
 * Component: Guest Layout Footer
 * Description: WCAG 2.2 AA compliant footer for guest-accessible pages with MOTAC branding
 * Author: Pasukan BPM MOTAC
 * Requirements: 5.1, 6.1, 6.2, 6.3, 14.1, 19.5
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2)
 * Version: 1.0.0
 * Created: 2025-11-03
 * Last Updated: 2025-11-03
 */
--}}

<footer class="bg-gray-900 text-white mt-auto" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- About Section --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('footer.about_ictserve') }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    {{ __('footer.about_description') }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('footer.quick_links') }}</h3>
                <nav aria-label="{{ __('footer.footer_navigation') }}">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('welcome') }}"
                                class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors inline-block">
                                {{ __('footer.home') }}
                            </a>
                        </li>
                        @php
                            $helpdeskRouteName = collect(['helpdesk.create'])->first(fn (string $name) => Route::has($name));
                            $loanRouteName = collect(['loans.create', 'loan.guest.create'])->first(fn (string $name) => Route::has($name));
                        @endphp
                        @if ($helpdeskRouteName)
                            <li>
                                <a href="{{ route($helpdeskRouteName) }}"
                                    class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors inline-block">
                                    {{ __('footer.helpdesk') }}
                                </a>
                            </li>
                        @endif
                        @if ($loanRouteName)
                            <li>
                                <a href="{{ route($loanRouteName) }}"
                                    class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors inline-block">
                                    {{ __('footer.asset_loan') }}
                                </a>
                            </li>
                        @endif
                        @auth
                            <li>
                                <a href="{{ route('staff.dashboard') }}"
                                    class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors inline-block">
                                    {{ __('footer.staff_portal') }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}"
                                    class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors inline-block">
                                    {{ __('footer.staff_login') }}
                                </a>
                            </li>
                        @endauth
                    </ul>
                </nav>
            </div>

            {{-- Contact Information --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('footer.contact_us') }}</h3>
                <address class="text-gray-400 text-sm not-italic space-y-2">
                    <p>
                        <strong>{{ __('footer.ministry') }}:</strong><br>
                        {{ __('footer.ministry_name') }}
                    </p>
                    <p>
                        <strong>{{ __('footer.email') }}:</strong><br>
                        <a href="mailto:support@motac.gov.my"
                            class="hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors">
                            support@motac.gov.my
                        </a>
                    </p>
                    <p>
                        <strong>{{ __('footer.phone') }}:</strong><br>
                        <a href="tel:+60380009999"
                            class="hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-gray-900 rounded-md transition-colors">
                            +603 8000 9999
                        </a>
                    </p>
                </address>
            </div>
        </div>

        {{-- Copyright and Compliance --}}
        <div class="mt-8 pt-8 border-t border-gray-800">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} {{ __('footer.ministry_name') }}.
                    {{ __('footer.all_rights_reserved') }}.
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-400">
                    <span>{{ __('footer.wcag_compliant') }}</span>
                    <span aria-hidden="true">|</span>
                    <span>{{ __('footer.pdpa_compliant') }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
