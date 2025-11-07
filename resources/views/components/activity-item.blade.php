{{--
    name: activity-item.blade.php
    description: Reusable activity item for recent activity feeds
    author: dev-team@motac.gov.my
    trace: D03 SRS-FR-006, D12 §3, D14 §9 (Requirement 1.2, 10.1, WCAG 2.2 AA)
    last-updated: 2025-11-06

    @props:
    - type (string): Activity type (created, updated, submitted, approved, rejected, etc.)
    - title (string): Activity title
    - description (optional, string): Additional description
    - timestamp (string): Human-readable timestamp
    - url (optional, string): Link to the subject
--}}

@props([
    'type',
    'title',
    'description' => null,
    'timestamp',
    'url' => null,
])

@php
    $iconClasses = [
        'created' => 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
        'updated' => 'bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400',
        'submitted' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400',
        'approved' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
        'rejected' => 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400',
        'commented' => 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400',
        'assigned' => 'bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-400',
        'resolved' => 'bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400',
    ];

    $typeIcons = [
        'created' => '<path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />',
        'updated' => '<path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" />',
        'submitted' => '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />',
        'approved' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
        'rejected' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />',
        'commented' => '<path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />',
        'assigned' => '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />',
        'resolved' => '<path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start']) }}>
    {{-- Icon --}}
    <div class="flex-shrink-0">
        <div class="w-8 h-8 {{ $iconClasses[$type] ?? $iconClasses['updated'] }} rounded-full flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                {!! $typeIcons[$type] ?? $typeIcons['updated'] !!}
            </svg>
        </div>
    </div>

    {{-- Content --}}
    <div class="ml-3 flex-1 min-w-0">
        @if($url)
            <a
                href="{{ $url }}"
                class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
            >
                {{ $title }}
            </a>
        @else
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $title }}
            </p>
        @endif

        @if($description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $description }}
            </p>
        @endif

        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ $timestamp }}
        </p>
    </div>
</div>
