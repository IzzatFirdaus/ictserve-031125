{{--
/**
 * Component: ARIA Landmarks Helper
 * Description: WCAG 2.2 AA compliant ARIA landmarks wrapper component
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1 Info and Relationships, SC 1.3.6 Identify Purpose)
 * @version 1.0.0
 * @created 2025-12-04
 *
 * Requirement 9.3: ARIA labels, landmarks, live regions
 *
 * Usage:
 * <x-accessibility.landmarks type="main" label="Main Content">
 *     Content here
 * </x-accessibility.landmarks>
 */
--}}

@props([
    'type' => 'region', // main, navigation, banner, contentinfo, complementary, search, form, region
    'label' => null,
    'labelledby' => null,
    'describedby' => null,
])

@php
    $tag = match ($type) {
        'main' => 'main',
        'navigation' => 'nav',
        'banner' => 'header',
        'contentinfo' => 'footer',
        'complementary' => 'aside',
        'search' => 'search',
        'form' => 'form',
        default => 'section',
    };

    $role = match ($type) {
        'main' => 'main',
        'navigation' => 'navigation',
        'banner' => 'banner',
        'contentinfo' => 'contentinfo',
        'complementary' => 'complementary',
        'search' => 'search',
        'form' => 'form',
        'region' => 'region',
        default => null,
    };
@endphp

<{{ $tag }} @if ($role && !in_array($tag, ['main', 'nav', 'header', 'footer', 'aside', 'search', 'form'])) role="{{ $role }}" @endif
    @if ($label) aria-label="{{ $label }}" @endif
    @if ($labelledby) aria-labelledby="{{ $labelledby }}" @endif
    @if ($describedby) aria-describedby="{{ $describedby }}" @endif {{ $attributes }}>
    {{ $slot }}
    </{{ $tag }}>
