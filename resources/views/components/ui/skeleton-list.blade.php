{{--
/**
 * Skeleton List Component
 *
 * Loading placeholder for recent activity lists to improve perceived performance
 * and prevent Cumulative Layout Shift (CLS) during data loading.
 *
 * Props:
 * - $items: Number of skeleton items to display (default: 5)
 *
 * Features:
 * - Animated pulse effect
 * - Matches activity list item dimensions
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

@props(['items' => 5])

<ul role="list" class="divide-y divide-slate-800">
    @for ($i = 0; $i < $items; $i++)
        <li class="py-4 animate-pulse">
            <div class="flex space-x-3">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="h-4 bg-slate-700 rounded w-1/3"></div>
                        <div class="h-6 bg-slate-700 rounded w-20"></div>
                    </div>
                    <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-700 rounded w-1/4"></div>
                </div>
            </div>
        </li>
    @endfor
</ul>
