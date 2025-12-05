{{--
/**
 * Component: Focus Indicator Enhancement
 * Description: WCAG 2.2 AA compliant focus indicator with 3px outline, 2px offset, and 3:1 contrast minimum
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D12 §6.13 (Focus Indicator Requirements)
 * @trace D13 §2.2 (MyDS Token System)
 * @trace D14 §9.1 (Focus States)
 * @wcag WCAG 2.2 Level AA (SC 2.4.7 Focus Visible, SC 2.4.11 Focus Not Obscured)
 * @version 1.0.0
 * @created 2025-12-05
 *
 * Requirements:
 * - 6.2: Visible focus indicators (3px outline, 2px offset, 3:1 contrast minimum)
 * - 9.5: Focus transitions with 200ms ease-out
 *
 * Usage:
 * 1. Include this component in your layout to apply global focus styles
 * 2. Use the x-focus-indicator directive on specific elements for enhanced focus
 * 3. Use data-focus-style="primary|danger|success" for different focus colors
 *
 * @example
 * <x-accessibility.focus-indicator />
 *
 * @example
 * <button x-data x-focus-indicator class="btn">Click me</button>
 *
 * @example
 * <input type="text" data-focus-style="danger" class="form-input" />
 */
--}}

@props([
    'global' => true,
    'color' => 'primary', // primary, danger, success, warning
])

@php
    $focusColors = [
        'primary' => [
            'ring' => 'ring-primary-500',
            'outline' => 'outline-primary-500',
            'css' => 'var(--color-primary-500, #0056B3)',
        ],
        'danger' => [
            'ring' => 'ring-danger-500',
            'outline' => 'outline-danger-500',
            'css' => 'var(--color-danger-500, #B3002D)',
        ],
        'success' => [
            'ring' => 'ring-success-500',
            'outline' => 'outline-success-500',
            'css' => 'var(--color-success-500, #1B7C54)',
        ],
        'warning' => [
            'ring' => 'ring-warning-500',
            'outline' => 'outline-warning-500',
            'css' => 'var(--color-warning-500, #CC7700)',
        ],
    ];
    $selectedColor = $focusColors[$color] ?? $focusColors['primary'];
@endphp

{{-- Global Focus Styles --}}
@if ($global)
    <style>
        /* WCAG 2.2 AA Focus Indicator - 3px outline, 2px offset, 3:1 contrast minimum */
        /* D12 §6.13, D14 §9.1 */

        :root {
            --focus-ring-width: 3px;
            --focus-ring-offset: 2px;
            --focus-ring-color: {{ $selectedColor['css'] }};
            --focus-transition-duration: var(--duration-short, 200ms);
            --focus-transition-timing: var(--motion-easeout, cubic-bezier(0.33, 1, 0.68, 1));
        }

        /* Base focus-visible styles for all interactive elements */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible,
        [role="button"]:focus-visible,
        [role="link"]:focus-visible,
        [role="menuitem"]:focus-visible,
        [role="tab"]:focus-visible,
        [role="checkbox"]:focus-visible,
        [role="radio"]:focus-visible,
        [role="switch"]:focus-visible,
        [role="option"]:focus-visible,
        [role="treeitem"]:focus-visible {
            outline: var(--focus-ring-width) solid var(--focus-ring-color);
            outline-offset: var(--focus-ring-offset);
            transition: outline-color var(--focus-transition-duration) var(--focus-transition-timing),
                outline-offset var(--focus-transition-duration) var(--focus-transition-timing);
        }

        /* Remove default browser outline when using custom focus */
        a:focus,
        button:focus,
        input:focus,
        select:focus,
        textarea:focus,
        [tabindex]:focus {
            outline: none;
        }

        /* Ensure focus is visible even when element has outline: none */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: var(--focus-ring-width) solid var(--focus-ring-color) !important;
            outline-offset: var(--focus-ring-offset) !important;
        }

        /* Focus styles for form inputs with error state */
        input[aria-invalid="true"]:focus-visible,
        select[aria-invalid="true"]:focus-visible,
        textarea[aria-invalid="true"]:focus-visible,
        [data-focus-style="danger"]:focus-visible {
            --focus-ring-color: var(--color-danger-500, #B3002D);
        }

        /* Focus styles for success state */
        [data-focus-style="success"]:focus-visible {
            --focus-ring-color: var(--color-success-500, #1B7C54);
        }

        /* Focus styles for warning state */
        [data-focus-style="warning"]:focus-visible {
            --focus-ring-color: var(--color-warning-500, #CC7700);
        }

        /* High contrast mode support */
        @media (forced-colors: active) {

            a:focus-visible,
            button:focus-visible,
            input:focus-visible,
            select:focus-visible,
            textarea:focus-visible,
            [tabindex]:focus-visible {
                outline: 3px solid CanvasText !important;
                outline-offset: 2px !important;
            }
        }

        /* Reduced motion support - instant focus without transition */
        @media (prefers-reduced-motion: reduce) {

            a:focus-visible,
            button:focus-visible,
            input:focus-visible,
            select:focus-visible,
            textarea:focus-visible,
            [tabindex]:focus-visible {
                transition: none;
            }
        }

        /* Focus within for composite widgets */
        .focus-within-indicator:focus-within {
            outline: var(--focus-ring-width) solid var(--focus-ring-color);
            outline-offset: var(--focus-ring-offset);
        }

        /* Skip link specific focus (enhanced visibility) */
        .skip-link:focus-visible {
            position: fixed !important;
            top: 1rem !important;
            left: 1rem !important;
            z-index: 9999 !important;
            padding: 0.75rem 1.5rem !important;
            background-color: white !important;
            color: var(--color-primary-600, #0056B3) !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
            outline: var(--focus-ring-width) solid var(--focus-ring-color) !important;
            outline-offset: var(--focus-ring-offset) !important;
        }

        /* Focus indicator for cards and larger interactive areas */
        .card-focus:focus-visible,
        [role="article"]:focus-visible,
        [role="listitem"]:focus-visible {
            outline: var(--focus-ring-width) solid var(--focus-ring-color);
            outline-offset: calc(var(--focus-ring-offset) + 2px);
            border-radius: var(--radius-l, 12px);
        }

        /* Focus indicator for inline elements */
        .inline-focus:focus-visible {
            outline: 2px solid var(--focus-ring-color);
            outline-offset: 1px;
            border-radius: var(--radius-xs, 4px);
        }
    </style>
@endif

{{-- Alpine.js Focus Indicator Directive --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('focus-indicator', (el, {
            value,
            modifiers,
            expression
        }, {
            Alpine,
            effect,
            cleanup
        }) => {
            const focusColor = el.dataset.focusStyle || 'primary';
            const colors = {
                primary: 'var(--color-primary-500, #0056B3)',
                danger: 'var(--color-danger-500, #B3002D)',
                success: 'var(--color-success-500, #1B7C54)',
                warning: 'var(--color-warning-500, #CC7700)',
            };

            const applyFocus = () => {
                el.style.outline = `3px solid ${colors[focusColor] || colors.primary}`;
                el.style.outlineOffset = '2px';
                el.style.transition =
                    'outline-color 200ms cubic-bezier(0.33, 1, 0.68, 1), outline-offset 200ms cubic-bezier(0.33, 1, 0.68, 1)';
            };

            const removeFocus = () => {
                el.style.outline = '';
                el.style.outlineOffset = '';
            };

            el.addEventListener('focus', applyFocus);
            el.addEventListener('blur', removeFocus);

            cleanup(() => {
                el.removeEventListener('focus', applyFocus);
                el.removeEventListener('blur', removeFocus);
            });
        });
    });
</script>

{{-- Focus Indicator Utility Classes --}}
@once
    @push('styles')
        <style>
            /* Utility classes for focus indicators */
            .focus-primary:focus-visible {
                --focus-ring-color: var(--color-primary-500, #0056B3);
            }

            .focus-danger:focus-visible {
                --focus-ring-color: var(--color-danger-500, #B3002D);
            }

            .focus-success:focus-visible {
                --focus-ring-color: var(--color-success-500, #1B7C54);
            }

            .focus-warning:focus-visible {
                --focus-ring-color: var(--color-warning-500, #CC7700);
            }

            /* Focus ring width variants */
            .focus-ring-2:focus-visible {
                --focus-ring-width: 2px;
            }

            .focus-ring-3:focus-visible {
                --focus-ring-width: 3px;
            }

            .focus-ring-4:focus-visible {
                --focus-ring-width: 4px;
            }

            /* Focus offset variants */
            .focus-offset-1:focus-visible {
                --focus-ring-offset: 1px;
            }

            .focus-offset-2:focus-visible {
                --focus-ring-offset: 2px;
            }

            .focus-offset-4:focus-visible {
                --focus-ring-offset: 4px;
            }
        </style>
    @endpush
@endonce
