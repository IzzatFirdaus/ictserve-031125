{{--
/**
 * Component name: Focus Trap
 * Description: WCAG 2.2 AA compliant focus trap for modal dialogs with automatic focus management and keyboard navigation.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D03-FR-006.3 (Focus Management)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §7.6 (Modal Focus Trap)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §9 (Accessibility Standards)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@props([
    'id' => 'focus-trap-' . uniqid(),
    'active' => false,
    'returnFocus' => true,
])

<div
    x-data="{
        active: @js($active),
        previousFocus: null,
        focusableElements: [],
        firstFocusable: null,
        lastFocusable: null,

        init() {
            this.\$watch('active', (value) => {
                if (value) {
                    this.trapFocus();
                } else {
                    this.releaseFocus();
                }
            });

            if (this.active) {
                this.\$nextTick(() => this.trapFocus());
            }
        },

        trapFocus() {
            // Store the currently focused element
            this.previousFocus = document.activeElement;

            // Get all focusable elements
            this.updateFocusableElements();

            // Focus the first element
            if (this.firstFocusable) {
                this.firstFocusable.focus();
            }
        },

        releaseFocus() {
            // Return focus to the previously focused element
            if (@js($returnFocus) && this.previousFocus) {
                this.previousFocus.focus();
            }
        },

        updateFocusableElements() {
            const container = this.\$el;
            const focusableSelectors = [
                'a[href]:not([disabled])',
                'button:not([disabled])',
                'textarea:not([disabled])',
                'input:not([disabled])',
                'select:not([disabled])',
                '[tabindex]:not([tabindex=\"-1\"]):not([disabled])'
            ];

            this.focusableElements = Array.from(
                container.querySelectorAll(focusableSelectors.join(','))
            ).filter(el => {
                return el.offsetParent !== null &&
                       window.getComputedStyle(el).visibility !== 'hidden';
            });

            this.firstFocusable = this.focusableElements[0];
            this.lastFocusable = this.focusableElements[this.focusableElements.length - 1];
        },

        handleTabKey(event) {
            if (!this.active || event.key !== 'Tab') return;

            this.updateFocusableElements();

            if (this.focusableElements.length === 0) {
                event.preventDefault();
                return;
            }

            if (event.shiftKey) {
                // Shift + Tab
                if (document.activeElement === this.firstFocusable) {
                    event.preventDefault();
                    this.lastFocusable.focus();
                }
            } else {
                // Tab
                if (document.activeElement === this.lastFocusable) {
                    event.preventDefault();
                    this.firstFocusable.focus();
                }
            }
        }
    }"
    @keydown.tab="handleTabKey($event)"
    {{ $attributes->merge(['class' => '']) }}
    id="{{ $id }}"
>
    {{ $slot }}
</div>

@push('scripts')
<script>
    // Global helper to activate/deactivate focus trap
    window.activateFocusTrap = function(elementId) {
        const element = document.getElementById(elementId);
        if (element && element.__x) {
            element.__x.\$data.active = true;
        }
    };

    window.deactivateFocusTrap = function(elementId) {
        const element = document.getElementById(elementId);
        if (element && element.__x) {
            element.__x.\$data.active = false;
        }
    };
</script>
@endpush
