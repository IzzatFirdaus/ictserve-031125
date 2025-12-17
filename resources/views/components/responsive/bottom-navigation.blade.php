{{--
/**
 * Component: Bottom Navigation
 * Description: WCAG 2.2 AA compliant mobile bottom navigation bar
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.2 (Mobile Navigation)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.4.1 Bypass Blocks, SC 2.5.8 Target Size)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Fixed bottom position on mobile devices
 * - 44×44px minimum touch targets
 * - Active state indication
 * - Badge support for notifications
 * - Hidden on desktop (md:hidden)
 * - Safe area inset support for notched devices
 *
 * Usage:
 * <x-responsive.bottom-navigation :items="[
 *     ['label' => 'Laman Utama', 'href' => '/', 'icon' => 'home', 'active' => true],
 *     ['label' => 'Tiket', 'href' => '/helpdesk', 'icon' => 'ticket', 'badge' => 3],
 *     ['label' => 'Pinjaman', 'href' => '/loans', 'icon' => 'clipboard'],
 *     ['label' => 'Profil', 'href' => '/profile', 'icon' => 'user'],
 * ]" />
 */
--}}

@props([
    'items' => [],
    'maxItems' => 5,
])

<nav {{ $attributes->merge([
    'class' =>
        'fixed bottom-0 inset-x-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg md:hidden',
    'style' => 'padding-bottom: env(safe-area-inset-bottom, 0px);',
]) }}
    role="navigation" aria-label="{{ __('Navigasi utama mudah alih') }}">
    <div class="flex items-center justify-around h-16 px-2">
        @foreach (array_slice($items, 0, $maxItems) as $item)
            @php
                $isActive = $item['active'] ?? false;
                $badge = $item['badge'] ?? null;
                $href = $item['href'] ?? '#';
                $label = $item['label'] ?? '';
                $icon = $item['icon'] ?? 'home';
            @endphp

            <a href="{{ $href }}"
                class="flex flex-col items-center justify-center min-w-[64px] min-h-11 px-2 py-1 rounded-lg transition-colors duration-200 {{ $isActive
                    ? 'text-primary-600 dark:text-primary-400'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                @if ($isActive) aria-current="page" @endif>
                <span class="relative">
                    @switch($icon)
                        @case('home')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @break

                        @case('ticket')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        @break

                        @case('clipboard')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        @break

                        @case('user')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @break

                        @case('bell')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        @break

                        @default
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                    @endswitch

                    {{-- Badge --}}
                    @if ($badge)
                        <span
                            class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-bold text-white bg-danger-500 rounded-full"
                            aria-label="{{ $badge }} {{ __('pemberitahuan baharu') }}">
                            {{ $badge > 99 ? '99+' : $badge }}
                        </span>
                    @endif
                </span>
                <span class="mt-1 text-xs font-medium truncate max-w-[64px]">{{ $label }}</span>
            </a>
        @endforeach
    </div>
</nav>

{{-- Spacer to prevent content from being hidden behind bottom nav --}}
<div class="h-16 md:hidden" aria-hidden="true"></div>
