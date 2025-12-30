{{--
    ICTServe Base Widget Card Component - MyDS Design System v2025.2
    
    Provides consistent styling for all dashboard widgets with:
    - MyDS shadow-card elevation
    - WCAG 2.2 AA accessibility compliance
    - Dark mode support
    - Proper ARIA labels
    - Loading state skeleton
    
    @props
    - heading: Widget title (optional)
    - description: Widget description (optional)
    - icon: Heroicon name (optional)
    - color: Widget accent color (primary, success, warning, danger)
    - loading: Show loading skeleton (boolean)
    - actions: Slot for action buttons
    
    @trace D12 §6.9 (Shadow System), D13 §2.2-2.7 (MyDS), D14 §4 (MOTAC Branding)
    @version 3.6.1
--}}

@props([
    'heading' => null,
    'description' => null,
    'icon' => null,
    'color' => 'primary',
    'loading' => false,
    'columnSpan' => null,
])

@php
    $colorClasses = match ($color) {
        'success' => 'text-success-600 dark:text-success-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'info' => 'text-info-600 dark:text-info-400',
        default => 'text-primary-600 dark:text-primary-400',
    };

    $iconBgClasses = match ($color) {
        'success' => 'bg-success-50 dark:bg-success-900/20',
        'warning' => 'bg-warning-50 dark:bg-warning-900/20',
        'danger' => 'bg-danger-50 dark:bg-danger-900/20',
        'info' => 'bg-info-50 dark:bg-info-900/20',
        default => 'bg-primary-50 dark:bg-primary-900/20',
    };
@endphp

<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-gray-800 rounded-lg p-6 theme-transition',
    'style' => 'box-shadow: var(--shadow-card);',
    'role' => 'region',
]) }}
    @if ($heading) aria-labelledby="widget-heading-{{ Str::slug($heading) }}" @endif
    @if ($loading) aria-busy="true" @endif>

    {{-- Loading Skeleton --}}
    @if ($loading)
        <div class="animate-pulse" aria-label="{{ __('filament.widget.loading') ?? 'Memuatkan...' }}">
            {{-- Header Skeleton --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div>
                        <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                        <div class="h-3 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
                <div class="h-8 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>

            {{-- Content Skeleton --}}
            <div class="space-y-3">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            </div>
        </div>
    @else
        {{-- Widget Header --}}
        @if ($heading || $icon || isset($actions))
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    {{-- Icon --}}
                    @if ($icon)
                        <div class="p-2 rounded-lg {{ $iconBgClasses }}">
                            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 {{ $colorClasses }}"
                                aria-hidden="true" />
                        </div>
                    @endif

                    {{-- Title & Description --}}
                    <div>
                        @if ($heading)
                            <h3 id="widget-heading-{{ Str::slug($heading) }}"
                                class="text-xl font-semibold text-gray-900 dark:text-white font-heading">
                                {{ $heading }}
                            </h3>
                        @endif

                        @if ($description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-body mt-0.5">
                                {{ $description }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Actions Slot --}}
                @if (isset($actions))
                    <div class="flex items-center gap-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Widget Content --}}
        <div class="space-y-4">
            {{ $slot }}
        </div>
    @endif
</div>
