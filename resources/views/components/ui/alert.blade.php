@props([
    'variant' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
    $variants = [
        'info' => [
            'container' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-800',
            'icon' => 'text-blue-400 dark:text-blue-300',
            'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'success' => [
            'container' => 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-800',
            'icon' => 'text-green-400 dark:text-green-300',
            'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'warning' => [
            'container' => 'bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-800',
            'icon' => 'text-yellow-400 dark:text-yellow-300',
            'icon_path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
        ],
        'danger' => [
            'container' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-800',
            'icon' => 'text-red-400 dark:text-red-300',
            'icon_path' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
    ];

    $config = $variants[$variant] ?? $variants['info'];
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    class="rounded-md border p-4 {{ $config['container'] }}"
    role="alert"
    {{ $attributes }}
>
    <div class="flex">
        <div class="shrink-0">
            <svg class="h-5 w-5 {{ $config['icon'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon_path'] }}" />
            </svg>
        </div>
        <div class="ml-3 flex-1 md:flex md:justify-between">
            <div>
                @if($title)
                    <h3 class="text-sm font-medium">{{ $title }}</h3>
                @endif
                <div class="text-sm {{ $title ? 'mt-2' : '' }}">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        @click="show = false"
                        type="button" 
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $config['container'] }} hover:bg-opacity-75"
                        aria-label="Dismiss"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
