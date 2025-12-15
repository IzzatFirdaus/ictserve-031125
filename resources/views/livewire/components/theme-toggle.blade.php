{{--
    Theme Toggle Component - Bedrock Chat Style
    @component ThemeToggle
    @description Simple sun/moon toggle for light/dark themes (v3.6.0)
    @trace D12 §6.10, D14 §6.1.2, D14 §8.1, D00-PREPLANNING §2.1-2.4
    @wcag SC 1.4.3 Contrast, SC 2.1.1 Keyboard, SC 2.4.7 Focus Visible
    @requirements 25.4, 25.5
    @version 3.6.0 - Light mode immutable default, no system preference
    @note Uses unique IDs per instance to support multiple toggles on same page
--}}
@php
    $isDark = $theme === 'dark';
@endphp
<button type="button"
    aria-label="Tukar tema"
    wire:click="toggleTheme"
    wire:loading.attr="disabled"
    class="p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-gray-500 dark:text-gray-200 min-h-11 min-w-11 flex items-center justify-center ring-1 ring-black/5 dark:ring-white/10">
    <x-heroicon-o-sun class="w-5 h-5 {{ $isDark ? 'hidden' : '' }}" />
    <x-heroicon-o-moon class="w-5 h-5 {{ $isDark ? '' : 'hidden' }}" />
</button>
