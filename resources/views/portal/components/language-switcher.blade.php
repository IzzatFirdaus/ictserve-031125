{{--
    Component: Language Switcher (Disabled)
    Description: Displays Bahasa Melayu only indicator per D15 directive.
    Author: Pasukan BPM MOTAC
    Trace: D15 §1.1, D12 §9
    Version: 1.0.0
    Updated: 2025-12-20
--}}

<div class="flex items-center gap-2">
    <span
        class="inline-flex items-center min-h-11 px-3 py-2 rounded-lg border border-slate-800 bg-slate-900/60 text-xs font-semibold uppercase tracking-wide text-slate-200"
        aria-hidden="true">
        BM
    </span>
    <x-accessibility.language-disabled :showNotice="false" />
</div>
