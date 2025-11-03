{{--
/**
 * Uncategorized - Input Error Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Input Error
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
 * <x-uncategorized.input-error.blade />
 */
--}}

{{--
/**
 * Component name: Input Error
 * Description: Displays form validation error messages with accessible styling for screen readers
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-011.5 (Profile Management)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-danger dark:text-red-400 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
