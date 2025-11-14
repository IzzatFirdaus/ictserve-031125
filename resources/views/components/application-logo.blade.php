{{--
/**
 * Component: Application Logo
 * Description: MOTAC logotype wrapper for layout components and Filament brand usage.
 *
 * @trace D03-FR-014.1 (MOTAC Branding)
 * @trace D04 A6.1 (Component Architecture)
 * @trace D10 A7 (Component Documentation Standards)
 * @trace D12 A9 (UI Component Standards)
 * @trace D14 A2 (MOTAC Branding Standards)
 *
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * @version 1.1.0
 * @updated 2025-11-12
 */
--}}

{{-- # trace: .kiro/specs/frontend-pages-redesign/design.md §Brand Assets --}}
<img src="{{ asset('images/motac-logo.jpeg') }}" alt="{{ __('common.motac_logo') }}" width="120" height="120"
    loading="lazy" decoding="async" {{ $attributes->merge(['class' => 'h-10 w-auto']) }} />
