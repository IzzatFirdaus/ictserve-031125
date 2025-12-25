{{--
    Component: Portal Footer
    Description: Footer for portal layouts with compliance badges.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §3
    Version: 1.0.0
    Updated: 2025-12-20
--}}

<footer id="portal-footer" class="border-t border-slate-800 bg-slate-950/80" role="contentinfo">
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <p>&copy; {{ now()->year }} {{ e(__('footer.ministry_name')) }}. {{ e(__('footer.all_rights_reserved')) }}.</p>
        <div class="flex items-center gap-4">
            <span>{{ e(__('footer.wcag_compliant')) }}</span>
            <span aria-hidden="true">•</span>
            <span>{{ e(__('footer.pdpa_compliant')) }}</span>
        </div>
    </div>
</footer>
