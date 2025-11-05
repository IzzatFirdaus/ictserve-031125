{{--
/**
 * Component: Sidebar Navigation
 * Description: Role-based collapsible sidebar navigation for authenticated portal with WCAG 2.2 AA compliance
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-018.3 (Sidebar Navigation Component)
 * @trace D03-FR-025.3 (ARIA Landmarks)
 * @trace D04 §6.2 (Navigation Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 2.1.1, 2.4.1, 2.4.7, 2.5.8)
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @created 2025-11-05
 */
--}}

@props(['user'])

@php
    // Define navigation items based on user role
    $navigationItems = [
        // Staff role (all users)
        [
            'label' => __('common.dashboard'),
            'route' => 'staff.dashboard',
            'icon' => 'home',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],
        [
            'label' => __('common.my_tickets'),
            'route' => 'staff.tickets.index',
            'icon' => 'ticket',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],
        [
            'label' => __('common.my_loans'),
            'route' => 'staff.loans.index',
            'icon' => 'briefcase',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],
        [
            'label' => __('common.submission_history'),
            'route' => 'staff.history',
            'icon' => 'clock',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],
        [
            'label' => __('common.claim_submissions'),
            'route' => 'staff.claim-submissions',
            'icon' => 'link',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],
        [
            'label' => __('common.profile'),
            'route' => 'staff.profile',
            'icon' => 'user',
            'roles' => ['staff', 'approver', 'admin', 'superuser'],
        ],

        // Approver role (Grade 41+)
        [
            'label' => __('common.approvals'),
            'route' => 'staff.approvals.index',
            'icon' => 'check-circle',
            'roles' => ['approver', 'admin', 'superuser'],
        ],

        // Admin role
        [
            'label' => __('common.admin_panel'),
            'route' => 'filament.admin.pages.dashboard',
            'icon' => 'cog',
            'roles' => ['admin', 'superuser'],
            'divider' => true,
        ],
    ];

    // Filter navigation items based on user role
    $visibleItems = collect($navigationItems)->filter(function ($item) use ($user) {
        return in_array($user->role, $item['roles']);
    });
@endphp

<aside id="sidebar-navigation" role="navigation" aria-label="{{ __('common.sidebar_navigation') }}" x-data="{ collapsed: false, mobile: false }"
    :class="{ 'w-64': !collapsed, 'w-20': collapsed }"
    class="hidden lg:flex flex-col bg-white border-r border-gray-200 transition-all duration-300">

    {{-- Collapse Toggle Button --}}
    <div class="flex items-center justify-end p-4 border-b border-gray-200">
        <button type="button" @click="collapsed = !collapsed"
            class="p-2 text-gray-600 hover:text-motac-blue hover:bg-gray-100 rounded-md focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition-all"
            :aria-label="collapsed ? '{{ __('common.expand_sidebar') }}' : '{{ __('common.collapse_sidebar') }}'">
            <svg class="h-6 w-6 transition-transform" :class="{ 'rotate-180': collapsed }" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        @foreach ($visibleItems as $item)
            @if (isset($item['divider']) && $item['divider'])
                <div class="my-4 border-t border-gray-200"></div>
            @endif

            <a href="{{ route($item['route']) }}"
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all min-h-[44px]
                      {{ request()->routeIs($item['route'])
                          ? 'bg-motac-blue text-white'
                          : 'text-gray-700 hover:bg-gray-100 hover:text-motac-blue' }}
                      focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2"
                :class="{ 'justify-center': collapsed }"
                aria-current="{{ request()->routeIs($item['route']) ? 'page' : 'false' }}">

                {{-- Icon --}}
                <span class="flex-shrink-0">
                    @switch($item['icon'])
                        @case('home')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @break

                        @case('ticket')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        @break

                        @case('briefcase')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        @break

                        @case('clock')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @break

                        @case('link')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        @break

                        @case('check-circle')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @break

                        @case('user')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @break

                        @case('cog')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        @break
                    @endswitch
                </span>

                {{-- Label --}}
                <span x-show="!collapsed" x-transition class="ml-3">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- User Info (Collapsed State) --}}
    <div class="p-4 border-t border-gray-200">
        <div x-show="!collapsed" x-transition class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-full bg-motac-blue text-white flex items-center justify-center font-semibold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ ucfirst($user->role) }}</p>
            </div>
        </div>

        <div x-show="collapsed" x-transition class="flex justify-center">
            <div class="h-10 w-10 rounded-full bg-motac-blue text-white flex items-center justify-center font-semibold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Sidebar Overlay --}}
<div x-data="{ open: false }" @keydown.escape.window="open = false" class="lg:hidden">
    {{-- Mobile Menu Button --}}
    <button type="button" @click="open = true"
        class="fixed bottom-4 right-4 z-40 p-3 bg-motac-blue text-white rounded-full shadow-lg hover:bg-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[56px] min-w-[56px] flex items-center justify-center"
        aria-label="{{ __('common.open_navigation_menu') }}">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Mobile Sidebar --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50" @click="open = false" aria-hidden="true"></div>

    <aside x-show="open" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl flex flex-col" role="dialog" aria-modal="true"
        aria-label="{{ __('common.mobile_navigation') }}">

        {{-- Mobile Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <span class="text-lg font-semibold text-gray-900">{{ __('common.menu') }}</span>
            <button type="button" @click="open = false"
                class="p-2 text-gray-600 hover:text-motac-blue hover:bg-gray-100 rounded-md focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px] flex items-center justify-center"
                aria-label="{{ __('common.close_menu') }}">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation Menu --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @foreach ($visibleItems as $item)
                @if (isset($item['divider']) && $item['divider'])
                    <div class="my-4 border-t border-gray-200"></div>
                @endif

                <a href="{{ route($item['route']) }}" @click="open = false"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all min-h-[44px]
                          {{ request()->routeIs($item['route'])
                              ? 'bg-motac-blue text-white'
                              : 'text-gray-700 hover:bg-gray-100 hover:text-motac-blue' }}
                          focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2"
                    aria-current="{{ request()->routeIs($item['route']) ? 'page' : 'false' }}">

                    {{-- Icon (same as desktop) --}}
                    <span class="flex-shrink-0">
                        @switch($item['icon'])
                            @case('home')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            @break

                            @case('ticket')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            @break

                            @case('briefcase')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @break

                            @case('clock')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @break

                            @case('link')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            @break

                            @case('check-circle')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @break

                            @case('user')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @break

                            @case('cog')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @break
                        @endswitch
                    </span>

                    {{-- Label --}}
                    <span class="ml-3">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Mobile User Info --}}
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div
                    class="h-10 w-10 rounded-full bg-motac-blue text-white flex items-center justify-center font-semibold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </div>
    </aside>
</div>
