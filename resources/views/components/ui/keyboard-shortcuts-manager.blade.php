{{--
/**
 * Component: Keyboard Shortcuts Manager
 * Description: WCAG 2.2 AA compliant keyboard shortcuts manager with Alpine.js @keydown.window handlers
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-024.1-24.5 (Keyboard Shortcuts for Power Users)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D14 §10.2 (Keyboard Accessibility)
 * @wcag WCAG 2.2 Level AA (SC 2.1.1 Keyboard, SC 2.1.4 Character Key Shortcuts)
 * @version 1.0.0
 * @created 2025-12-05
 *
 * Requirements:
 * - 24.1: Keyboard shortcuts manager using Alpine.js @keydown.window handlers
 * - 24.2: Shortcuts: Alt+N (new ticket), Alt+D (dashboard), Alt+H (help), Alt+L (loans), ? (help modal)
 * - 24.3: Keyboard shortcuts help modal triggered by ? key
 * - 24.4: Alt key modifier to avoid conflicts with browser and screen reader shortcuts
 * - 24.5: Keyboard shortcuts do not interfere with assistive technology navigation
 *
 * Keyboard Action Table (D12 §6.11):
 * - Tab: Move to next focusable element
 * - Shift+Tab: Move to previous focusable element
 * - Enter/Space: Activate focused element
 * - Escape: Close modal/dropdown, cancel action
 * - Arrow Keys: Navigate within components (menus, tabs, etc.)
 *
 * Usage:
 * Include this component once in your main layout (app.blade.php or guest.blade.php)
 *
 * @example
 * <x-ui.keyboard-shortcuts-manager />
 */
--}}

@props([
    'enabled' => true,
    'showHelpOnLoad' => false,
])

@if ($enabled)
    <div x-data="keyboardShortcutsManager()" x-init="init()" @keydown.window="handleKeydown($event)" class="hidden"
        aria-hidden="true" data-keyboard-shortcuts-manager>
        {{-- Screen reader announcement for shortcut activation --}}
        <div x-ref="announcement" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>
    </div>

    <script>
        function keyboardShortcutsManager() {
            return {
                shortcuts: {},
                enabled: true,
                modalOpen: false,

                init() {
                    // Register default shortcuts per D12 §6.11 and Requirements 24.1-24.5
                    this.registerShortcuts();

                    // Check if user prefers reduced shortcuts (accessibility consideration)
                    this.checkAccessibilityPreferences();

                    @if ($showHelpOnLoad)
                        // Show help modal on first load if configured
                        this.$nextTick(() => {
                            this.showShortcutsModal();
                        });
                    @endif
                },

                registerShortcuts() {
                    // Navigation shortcuts (Alt + key to avoid conflicts with assistive tech)
                    this.shortcuts = {
                        // Alt+D: Go to Dashboard
                        'alt+d': {
                            action: () => this.navigateTo('{{ route('dashboard') }}'),
                            description: '{{ __('portal.keyboard_shortcuts.dashboard') }}',
                            category: 'navigation',
                        },
                        // Alt+N: New Ticket
                        'alt+n': {
                            action: () => this.navigateTo('{{ route('helpdesk.create') }}'),
                            description: '{{ __('portal.keyboard_shortcuts.new_ticket') }}',
                            category: 'actions',
                        },
                        // Alt+L: New Loan Application
                        'alt+l': {
                            action: () => this.navigateTo('{{ route('loan.wizard') }}'),
                            description: '{{ __('portal.keyboard_shortcuts.new_loan') }}',
                            category: 'actions',
                        },
                        // Alt+H: Help/Support
                        'alt+h': {
                            action: () => this.showShortcutsModal(),
                            description: '{{ __('portal.keyboard_shortcuts.help') }}',
                            category: 'navigation',
                        },
                        // Alt+S: Submissions/History
                        'alt+s': {
                            action: () => this.navigateTo('{{ route('staff.history') }}'),
                            description: '{{ __('portal.keyboard_shortcuts.submissions') }}',
                            category: 'navigation',
                        },
                        // Alt+P: Profile Settings
                        'alt+p': {
                            action: () => this.navigateTo('{{ route('profile.edit') }}'),
                            description: '{{ __('portal.keyboard_shortcuts.profile') }}',
                            category: 'navigation',
                        },
                        // ? (Question mark): Show shortcuts modal
                        '?': {
                            action: () => this.showShortcutsModal(),
                            description: '{{ __('portal.keyboard_shortcuts.show_shortcuts') }}',
                            category: 'general',
                            requiresShift: true,
                        },
                        // Escape: Close modal/dropdown
                        'escape': {
                            action: () => this.closeActiveModal(),
                            description: '{{ __('portal.keyboard_shortcuts.close_modal') }}',
                            category: 'general',
                        },
                    };
                },

                checkAccessibilityPreferences() {
                    // Respect user preferences for reduced motion/shortcuts
                    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                    // Check if user has disabled shortcuts in their profile
                    const userDisabledShortcuts = localStorage.getItem('keyboard_shortcuts_disabled') === 'true';

                    if (userDisabledShortcuts) {
                        this.enabled = false;
                    }
                },

                handleKeydown(event) {
                    // Don't handle shortcuts if disabled
                    if (!this.enabled) return;

                    // Don't handle shortcuts when typing in form fields (except Escape)
                    const isTyping = this.isTypingInFormField(event.target);
                    if (isTyping && event.key !== 'Escape') return;

                    // Don't handle shortcuts when a modal is open (except Escape)
                    if (this.isModalOpen() && event.key !== 'Escape') return;

                    // Build the shortcut key string
                    const shortcutKey = this.buildShortcutKey(event);

                    // Check if this shortcut exists
                    const shortcut = this.shortcuts[shortcutKey];
                    if (shortcut) {
                        // Prevent default browser behavior
                        event.preventDefault();
                        event.stopPropagation();

                        // Execute the shortcut action
                        shortcut.action();

                        // Announce to screen readers
                        this.announceShortcut(shortcut.description);
                    }
                },

                buildShortcutKey(event) {
                    const parts = [];

                    if (event.altKey) parts.push('alt');
                    if (event.ctrlKey) parts.push('ctrl');
                    if (event.shiftKey && event.key !== '?') parts.push('shift');
                    if (event.metaKey) parts.push('meta');

                    // Handle special keys
                    let key = event.key.toLowerCase();

                    // Handle question mark (Shift + /)
                    if (event.key === '?' || (event.shiftKey && event.key === '/')) {
                        return '?';
                    }

                    parts.push(key);
                    return parts.join('+');
                },

                isTypingInFormField(target) {
                    const tagName = target.tagName.toLowerCase();
                    const isEditable = target.isContentEditable;
                    const isInput = ['input', 'textarea', 'select'].includes(tagName);
                    const isSearchInput = target.type === 'search';

                    // Allow shortcuts in search inputs for better UX
                    if (isSearchInput) return false;

                    return isInput || isEditable;
                },

                isModalOpen() {
                    // Check for open modals using various indicators
                    const hasOpenModal = document.querySelector('[aria-modal="true"][x-show="true"]');
                    const hasOpenDialog = document.querySelector('dialog[open]');
                    const hasAlpineModal = document.querySelector('[x-data*="open: true"]');

                    return hasOpenModal || hasOpenDialog || this.modalOpen;
                },

                navigateTo(url) {
                    // Use Livewire navigation if available, otherwise standard navigation
                    if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                        Livewire.navigate(url);
                    } else {
                        window.location.href = url;
                    }
                },

                showShortcutsModal() {
                    // Dispatch event to show the keyboard shortcuts modal
                    window.dispatchEvent(new CustomEvent('show-shortcuts-modal'));
                    this.modalOpen = true;
                },

                closeActiveModal() {
                    // Dispatch event to close any open modal
                    window.dispatchEvent(new CustomEvent('close-shortcuts-modal'));
                    window.dispatchEvent(new CustomEvent('close-modal'));
                    this.modalOpen = false;
                },

                announceShortcut(description) {
                    // Announce the shortcut action to screen readers
                    const announcement = this.$refs.announcement;
                    if (announcement) {
                        announcement.textContent = description;
                        // Clear after announcement
                        setTimeout(() => {
                            announcement.textContent = '';
                        }, 1000);
                    }
                },

                // Public method to enable/disable shortcuts
                toggleShortcuts(enabled) {
                    this.enabled = enabled;
                    localStorage.setItem('keyboard_shortcuts_disabled', !enabled);
                },

                // Public method to get all registered shortcuts
                getShortcuts() {
                    return this.shortcuts;
                },
            };
        }
    </script>

    {{-- Include the keyboard shortcuts modal --}}
    <x-ui.keyboard-shortcuts-modal />
@endif
