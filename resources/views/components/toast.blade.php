{{--
    name: toast.blade.php
    description: Toast notification component with auto-dismiss and ARIA live region
    author: dev-team@motac.gov.my
    trace: D12 §3 (Component Library); D14 §9 (WCAG 2.2 AA)
    requirements: 4.1, 4.5, 6.3
    last-updated: 2025-01-06
--}}

@props([
    'type' => 'success',  // success|error|warning|info
    'message',
    'duration' => 5000,
])

<div
    x-data="{
        show: true,
        init() {
            setTimeout(() => this.show = false, {{ $duration }})
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="show = false"
    role="alert"
    aria-live="polite"
    aria-atomic="true"
    {{ $attributes->merge([
        'class' => 'fixed top-4 right-4 p-4 rounded-lg shadow-lg cursor-pointer z-50 max-w-md ' .
                   match($type) {
                       'success' => 'bg-green-600 text-white',
                       'error' => 'bg-red-600 text-white',
                       'warning' => 'bg-amber-600 text-white',
                       'info' => 'bg-blue-600 text-white',
                       default => 'bg-gray-800 text-white'
                   }
    ]) }}
>
    <div class="flex items-center gap-3">
        <span class="text-2xl flex-shrink-0" aria-hidden="true">
            @if($type === 'success') ✓
            @elseif($type === 'error') ✕
            @elseif($type === 'warning') ⚠
            @else ℹ
            @endif
        </span>
        <span class="flex-1">{{ $message }}</span>
        <button
            @click.stop="show = false"
            class="flex-shrink-0 ml-2 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white rounded"
            aria-label="{{ __('Close notification') }}"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
</div>
