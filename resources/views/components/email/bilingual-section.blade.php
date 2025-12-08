{{--
/**
 * Component name: Bilingual Email Section
 * Description: Displays content in Bahasa Melayu for email templates.
 * @author Pasukan BPM MOTAC
 * @trace D15 (Language Support)
 * @trace Requirements 13.2
 * @version 2.0.0 (v3.6.0 - Bahasa Melayu only)
 * @created 2025-12-05
 * @updated 2025-12-09
 *
 * @deprecated v3.6.0 English content is no longer displayed. This component now only
 *             renders Bahasa Melayu content. The enContent prop is ignored.
 */
--}}
@props([
    'msContent' => null,
    'enContent' => null, {{-- DEPRECATED v3.6.0: Ignored - Bahasa Melayu only --}}
    'showDivider' => false, {{-- DEPRECATED v3.6.0: No divider needed --}}
])

<div class="bilingual-section">
    {{-- Bahasa Melayu (Primary and Only Language - v3.6.0) --}}
    @if ($msContent)
        <div class="content-ms" lang="ms">
            {!! $msContent !!}
        </div>
    @endif

    {{-- v3.6.0: English content is no longer displayed
         The enContent prop is retained for backward compatibility but ignored --}}
</div>
