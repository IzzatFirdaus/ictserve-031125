{{--
/**
 * Skeleton Card Component
 *
 * Loading placeholder for dashboard statistics cards to improve perceived performance
 * and prevent Cumulative Layout Shift (CLS) during data loading.
 *
 * Features:
 * - Animated pulse effect
 * - Matches statistics card dimensions
 * - WCAG 2.2 AA compliant
 * - Prevents layout shift
 *
 * @see D12 §9 Performance optimization patterns
 * @see D13 §5 Loading states and skeleton screens
 *
 * @requirements 13.5 Core Web Vitals optimization
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-06
 *
 * @author Frontend Engineering Team
 */
--}}

<div class="bg-slate-900/70 backdrop-blur-sm border border-slate-800 overflow-hidden shadow rounded-lg animate-pulse">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="h-6 w-6 bg-slate-700 rounded"></div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <div class="space-y-3">
                    <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                    <div class="h-8 bg-slate-700 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-slate-800/50 px-5 py-3">
        <div class="h-4 bg-slate-700 rounded w-1/3"></div>
    </div>
</div>
