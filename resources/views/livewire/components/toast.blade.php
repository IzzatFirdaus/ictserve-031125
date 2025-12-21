{{--
/**
 * Toast Notification View
 *
 * Displays toast notifications with animations and accessibility support.
 *
 * Features:
 * - slideInUp/slideOutDown animations using --motion-easeoutback (400ms) per D12 §6.10
 * - ARIA live region with aria-live="polite" per D14 §10.4
 * - 44×44px dismiss button touch target per D12 §4.1
 * - shadow-dropdown styling per D14 §7.5
 * - Respects prefers-reduced-motion per D12 §6.10
 *
 * @see D12 §6.4 Notification patterns
 * @see D14 §9.3 Toast notification guidelines
 * @see D14 §10.4 ARIA live regions
 *
 * @requirements 9.2, 30.1-30.5 Toast notification system
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 */
--}}

<div class="fixed bottom-4 right-4 z-50 flex flex-col-reverse gap-3 max-w-sm w-full pointer-events-none"
    aria-live="polite" aria-label="{{ __('Notifications') }}" role="region">

    @foreach ($toasts as $toast)
        <div wire:key="{{ $toast['id'] }}" x-data="{ show: true }" x-init="$nextTick(() => {
            show = true;
            @if($toast['duration'] > 0)
            setTimeout(() => { show = false;
                setTimeout(() => $wire.dismissToast('{{ $toast['id'] }}'), 300); }, {{ $toast['duration'] }});
            @endif
        })" x-show="show"
            x-transition:enter="transform transition ease-out duration-400"
            x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
            class="pointer-events-auto flex items-start gap-3 p-4 rounded-lg border shadow-lg {{ $this->getClassesForType($toast['type']) }}"
            style="--tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);" role="alert"
            aria-atomic="true">

            {{-- Icon --}}
            <div class="shrink-0" aria-hidden="true">
                @switch($toast['type'])
                    @case('success')
                        <svg class="w-5 h-5 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @case('error')
                        <svg class="w-5 h-5 text-danger-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @case('warning')
                        <svg class="w-5 h-5 text-warning-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @default
                        <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                clip-rule="evenodd" />
                        </svg>
                @endswitch
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                @if ($toast['title'])
                    <p class="text-sm font-semibold">{{ $toast['title'] }}</p>
                @endif
                <p class="text-sm {{ $toast['title'] ? 'mt-1' : '' }}">{{ $toast['message'] }}</p>
            </div>

            {{-- Dismiss Button - 44×44px touch target per D12 §4.1 --}}
            <button type="button"
                @click="show = false; setTimeout(() => $wire.dismissToast('{{ $toast['id'] }}'), 300)"
                class="shrink-0 inline-flex items-center justify-center min-w-11 min-h-11 -m-2 rounded-lg text-current opacity-60 hover:opacity-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-current transition-opacity"
                aria-label="{{ __('Dismiss notification') }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
    @endforeach
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {

        [x-transition\:enter],
        [x-transition\:leave] {
            transition-duration: 0.01ms !important;
        }
    }
</style>
