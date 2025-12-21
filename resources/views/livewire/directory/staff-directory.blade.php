{{--
    Staff Directory Component - MyDS Design System v2025.2
    @trace D12 §5.1 - Guest Layout
    @trace D13 §2.2-2.7 - MyDS Design Tokens
    @trace figma-ui-redesign Requirements 31
    @wcag SC 1.3.1 Info and Relationships (Level A)
    @wcag SC 2.4.6 Headings and Labels (Level AA)
--}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 theme-transition">
    {{-- Hero Section with MOTAC Branding --}}
    <div class="bg-primary-500 dark:bg-primary-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="{{ __('navigation.breadcrumb') }}" class="mb-4">
                <ol class="flex items-center gap-2 text-sm text-primary-100">
                    <li>
                        <a href="/"
                            class="hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 rounded transition-colors duration-200">
                            {{ __('navigation.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true">
                        <x-heroicon-s-chevron-right class="w-4 h-4" />
                    </li>
                    <li aria-current="page" class="font-medium text-white">
                        {{ __('directory.page_title') }}
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-heading font-bold">{{ __('directory.page_title') }}</h1>
            <p class="mt-2 text-primary-100 max-w-2xl">{{ __('directory.page_subtitle') }}</p>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="main-content">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Contact Cards --}}
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-xl font-heading font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('directory.contact_us') }}</h2>

                @foreach ($contacts as $category => $categoryContacts)
                    @foreach ($categoryContacts as $contact)
                        <article
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 border border-gray-200 dark:border-gray-700 hover:shadow-dropdown transition-shadow duration-200 theme-transition"
                            aria-labelledby="contact-{{ $category }}-name">
                            <div class="flex items-start gap-4">
                                {{-- Icon based on category - MyDS compliant --}}
                                <div class="shrink-0 p-3 bg-primary-50 dark:bg-primary-900/30 rounded-lg"
                                    aria-hidden="true">
                                    @switch($category)
                                        @case('helpdesk')
                                            <x-heroicon-o-lifebuoy class="w-6 h-6 text-primary-500 dark:text-primary-400" />
                                        @break

                                        @case('network')
                                            <x-heroicon-o-globe-alt class="w-6 h-6 text-primary-500 dark:text-primary-400" />
                                        @break

                                        @case('systems')
                                            <x-heroicon-o-server class="w-6 h-6 text-primary-500 dark:text-primary-400" />
                                        @break

                                        @case('assets')
                                            <x-heroicon-o-computer-desktop
                                                class="w-6 h-6 text-primary-500 dark:text-primary-400" />
                                        @break

                                        @default
                                            <x-heroicon-o-phone class="w-6 h-6 text-primary-500 dark:text-primary-400" />
                                    @endswitch
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 id="contact-{{ $category }}-name"
                                        class="text-lg font-heading font-semibold text-gray-900 dark:text-white">
                                        {{ $contact['name'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        {{ $contact['role'] }}</p>

                                    <dl class="space-y-2 text-sm">
                                        <div class="flex items-center gap-2">
                                            <dt class="sr-only">{{ __('directory.email') }}</dt>
                                            <x-heroicon-o-envelope
                                                class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0"
                                                aria-hidden="true" />
                                            <dd>
                                                <a href="mailto:{{ $contact['email'] }}"
                                                    class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800 rounded transition-colors duration-200">
                                                    {{ $contact['email'] }}
                                                </a>
                                            </dd>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <dt class="sr-only">{{ __('directory.phone') }}</dt>
                                            <x-heroicon-o-phone
                                                class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0"
                                                aria-hidden="true" />
                                            <dd class="flex items-center gap-2">
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['phone']) }}"
                                                    class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800 rounded transition-colors duration-200">
                                                    {{ $contact['phone'] }}
                                                </a>
                                                <span
                                                    class="text-gray-500 dark:text-gray-400">({{ __('directory.extension') }}
                                                    {{ $contact['extension'] }})</span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforeach
            </div>

            {{-- Sidebar: Office Hours & Location --}}
            <aside class="space-y-6" aria-label="{{ __('directory.sidebar_info') }}">
                {{-- Office Hours --}}
                <section
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 border border-gray-200 dark:border-gray-700 theme-transition">
                    <h2
                        class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5 text-primary-500 dark:text-primary-400" aria-hidden="true" />
                        {{ __('directory.office_hours') }}
                    </h2>
                    <dl class="space-y-3 text-sm">
                        @foreach ($officeHours as $key => $hours)
                            <div>
                                <dt class="sr-only">{{ __("directory.hours_{$key}_label") }}</dt>
                                <dd class="text-gray-700 dark:text-gray-300">{{ $hours }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Location --}}
                <section
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 border border-gray-200 dark:border-gray-700 theme-transition">
                    <h2
                        class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-heroicon-o-map-pin class="w-5 h-5 text-primary-500 dark:text-primary-400"
                            aria-hidden="true" />
                        {{ __('directory.location') }}
                    </h2>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $location['building'] }}</p>
                        <p>{{ $location['ministry'] }}</p>
                        <p>{{ $location['address'] }}</p>
                        <p>{{ $location['city'] }}</p>
                        <p>{{ $location['country'] }}</p>
                    </address>
                </section>

                {{-- Quick Links --}}
                <section
                    class="bg-primary-50 dark:bg-primary-900/30 rounded-lg p-6 border border-primary-200 dark:border-primary-800 theme-transition">
                    <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('directory.quick_links') }}</h2>
                    <nav aria-label="{{ __('directory.quick_links') }}" class="space-y-3">
                        <a href="{{ route('helpdesk.create') }}"
                            class="flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 text-sm font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800 rounded min-h-11 py-2 transition-colors duration-200">
                            <x-heroicon-o-ticket class="w-5 h-5 shrink-0" aria-hidden="true" />
                            {{ __('directory.submit_ticket') }}
                        </a>
                        <a href="{{ route('loan.wizard') }}"
                            class="flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 text-sm font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800 rounded min-h-11 py-2 transition-colors duration-200">
                            <x-heroicon-o-computer-desktop class="w-5 h-5 shrink-0" aria-hidden="true" />
                            {{ __('directory.apply_loan') }}
                        </a>
                        <a href="{{ route('status.check') }}"
                            class="flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 text-sm font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800 rounded min-h-11 py-2 transition-colors duration-200">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 shrink-0" aria-hidden="true" />
                            {{ __('directory.check_status') }}
                        </a>
                    </nav>
                </section>
            </aside>
        </div>
    </main>
</div>
