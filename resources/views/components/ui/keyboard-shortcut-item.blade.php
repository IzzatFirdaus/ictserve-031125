{{--
    Keyboard Shortcut Item Component (x-ui.keyboard-shortcut-item)

    Single shortcut display row for the keyboard shortcuts modal.

    @props
    - key: The keyboard shortcut key combination (e.g., "Alt + D")
    - label: Description of what the shortcut does

    @usage
    <x-ui.keyboard-shortcut-item key="Alt + D" :label="__('portal.keyboard_shortcuts.dashboard')" />

    @trace Task 2.5.8, Requirement 24.2
--}}

@props([
'key' => '',
'label' => '',
])

<div
    {{ $attributes->merge(['class' => 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md']) }}
    role="listitem">
    <span class="text-sm text-gray-700 dark:text-gray-300">
        {{ $label }}
    </span>
    <kbd class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-sm shadow-sm">
        @php
        $keys = explode(' + ', $key);
        @endphp
        @foreach($keys as $index => $k)
        <span class="px-1">{{ $k }}</span>
        @if($index < count($keys) - 1)
            <span class="text-gray-400 dark:text-gray-500">+</span>
            @endif
            @endforeach
    </kbd>
</div>