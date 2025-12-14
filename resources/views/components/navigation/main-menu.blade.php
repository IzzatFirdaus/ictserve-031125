@php
    $navItems = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' =>
                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'label' => __('staff.search.global_search'),
            'route' => 'portal.search',
            'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        ],
        [
            'label' => 'My Tickets',
            'route' => 'tickets.index',
            'icon' =>
                'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        ],
        [
            'label' => 'My Loans',
            'route' => 'loans.index',
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        ],
    ];
@endphp

@foreach ($navItems as $item)
    <a href="{{ route($item['route']) }}"
        class="{{ request()->routeIs($item['route']) ? 'bg-primary-50 text-primary-600 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md min-h-11">
        <svg class="{{ request()->routeIs($item['route']) ? 'text-primary-600 dark:text-white' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300' }} mr-3 shrink-0 h-6 w-6"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
        </svg>
        {{ $item['label'] }}
    </a>
@endforeach
