{{--
/**
 * Component name: Bilingual Email Section
 * Description: Displays content in both Bahasa Melayu (primary) and English (secondary) for email templates.
 * @author Pasukan BPM MOTAC
 * @trace D15 (Bilingual Support)
 * @trace Requirements 13.2
 * @version 1.0.0
 * @created 2025-12-05
 */
--}}
@props([
    'msContent' => null,
    'enContent' => null,
    'showDivider' => true,
])

<div class="bilingual-section">
    {{-- Bahasa Melayu (Primary) --}}
    @if ($msContent)
        <div class="bilingual-ms" lang="ms">
            {!! $msContent !!}
        </div>
    @endif

    {{-- Divider between languages --}}
    @if ($showDivider && $msContent && $enContent)
        <hr style="border: 0; border-top: 1px dashed #D1D5DB; margin: 16px 0;">
    @endif

    {{-- English (Secondary) --}}
    @if ($enContent)
        <div class="bilingual-en" lang="en" style="color: #6B7280; font-size: 14px;">
            {!! $enContent !!}
        </div>
    @endif
</div>
