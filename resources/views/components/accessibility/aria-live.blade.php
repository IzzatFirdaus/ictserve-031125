{{--
/**
 * Component name: ARIA Live Region
 * Description: WCAG 2.2 AA compliant ARIA live region for screen reader announcements with configurable politeness levels.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §7.7 (ARIA Live Regions)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §9 (Accessibility Standards)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@props([
    'id' => 'aria-live-' . uniqid(),
    'politeness' => 'polite', // polite, assertive, off
    'atomic' => true,
    'relevant' => 'additions text', // additions, removals, text, all
    'visuallyHidden' => true,
])

@php
    $ariaLive = $politeness;
    $ariaAtomic = $atomic ? 'true' : 'false';
    $ariaRelevant = $relevant;

    $classes = $visuallyHidden ? 'sr-only' : '';
@endphp

<div
    id="{{ $id }}"
    role="status"
    aria-live="{{ $ariaLive }}"
    aria-atomic="{{ $ariaAtomic }}"
    aria-relevant="{{ $ariaRelevant }}"
    {{ $attributes->merge(['class' => $classes]) }}
>
    {{ $slot }}
</div>

@push('scripts')
<script>
    // Helper function to announce to screen readers
    window.announceToScreenReader = function(message, elementId = '{{ $id }}', politeness = 'polite') {
        const liveRegion = document.getElementById(elementId);
        if (liveRegion) {
            liveRegion.setAttribute('aria-live', politeness);
            liveRegion.textContent = message;

            // Clear after 5 seconds to avoid cluttering
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 5000);
        }
    };

    // Listen for custom events
    document.addEventListener('screen-reader-announce', function(event) {
        const { message, politeness = 'polite' } = event.detail;
        window.announceToScreenReader(message, '{{ $id }}', politeness);
    });
</script>
@endpush
