{{--
/**
 * Uncategorized - Action Message Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Action Message
 * @description Legacy component - consider categorization
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-11-03
 *
 * Requirements: 6.1, 14.1
 * WCAG Level: AA (SC 1.4.3, 2.1.1)
 * Standards: D04 §6.1, D10 §7, D12 §9, D14 §8
 * Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * Usage:
 * <x-uncategorized.action-message.blade />
 */
--}}

{{--
/**
 * Component: Action Message
 * Description: Flash message component for temporary feedback on user actions
 *
 * @trace D03-FR-016.3 (UI Feedback)
 * @trace D04 §6.1 (Component Architecture)
 * @trace D10 §7 (Component Documentation Standards)
 * @trace D12 §9 (UI Component Standards)
 * @trace D14 §8 (MOTAC UI Standards)
 *
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 * @updated 2025-11-03
 */
--}}
@props(['on'])

<div x-data="{ shown: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
     x-show.transition.out.opacity.duration.1500ms="shown"
     x-transition:leave.opacity.duration.1500ms
     style="display: none;"
    {{ $attributes->merge(['class' => 'text-sm text-gray-600 dark:text-gray-400']) }}>
    {{ $slot->isEmpty() ? __('Saved.') : $slot }}
</div>
