{{--
    Component: Portal Breadcrumb
    Description: Wrapper for breadcrumb navigation in portal pages.
    Author: Pasukan BPM MOTAC
    Trace: D12 §9, D14 §9
    Version: 1.0.0
    Updated: 2025-12-20
--}}

@props([
    'items' => [],
])

@if (!empty($items))
    <div class="rounded-lg border border-slate-800 bg-slate-900/40 px-4 py-3">
        <x-navigation.breadcrumbs :items="$items" class="text-slate-300" />
    </div>
@endif
