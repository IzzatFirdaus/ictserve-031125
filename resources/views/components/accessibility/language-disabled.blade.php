{{--
/**
 * Component: Language Disabled Notice
 * Description: WCAG 2.2 AA compliant notice for disabled language switcher (v3.6.0)
 * @author Pasukan BPM MOTAC
 * @trace D15 §1.1 (Bahasa Melayu Sahaja)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 3.1.1 Language of Page)
 * @version 1.0.0
 * @created 2025-12-16
 *
 * Requirements:
 * - 7.1: Bahasa Melayu exclusive interface
 * - 7.2: Disabled language switcher
 *
 * Usage:
 * <x-accessibility.language-disabled />
 */
--}}

@props([
    'showNotice' => false,
])

{{-- Hidden language indicator for screen readers --}}
<div class="sr-only" role="status" aria-live="polite">
    <span lang="ms">Antara muka sistem ini adalah dalam Bahasa Melayu sahaja.</span>
</div>

{{-- Optional visible notice for admin/debug purposes --}}
@if ($showNotice)
    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1" aria-hidden="true">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
            </path>
        </svg>
        <span>Bahasa Melayu</span>
    </div>
@endif
