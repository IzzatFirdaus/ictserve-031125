/**
 * Keyboard Navigation and Shortcuts
 *
 * Implements keyboard shortcuts for common portal actions and ensures
 * logical tab order throughout the authenticated portal.
 *
 * @trace D12 §4.4, D14 §9 (WCAG 2.2 SC 2.1.1, 2.4.3)
 * @author dev-team@motac.gov.my
 * @created 2025-11-06
 */

/**
 * Keyboard shortcuts configuration
 */
const shortcuts = {
    // Navigation shortcuts (Alt + key)
    d: {
        action: "dashboard",
        url: "/staff/dashboard",
        description: "Go to Dashboard",
    },
    s: {
        action: "submissions",
        url: "/staff/submissions",
        description: "View Submissions",
    },
    p: {
        action: "profile",
        url: "/staff/profile",
        description: "Edit Profile",
    },
    h: {
        action: "helpdesk",
        url: "/helpdesk/authenticated/dashboard",
        description: "Helpdesk Dashboard",
    },
    l: {
        action: "loans",
        url: "/loan/authenticated/dashboard",
        description: "Loans Dashboard",
    },
    a: {
        action: "approvals",
        url: "/loan/approvals",
        description: "View Approvals (Approvers only)",
    },

    // Action shortcuts (Alt + Shift + key)
    n: {
        action: "new-ticket",
        url: "/helpdesk/authenticated/create",
        description: "New Helpdesk Ticket",
    },
    r: {
        action: "new-loan",
        url: "/loan/authenticated/create",
        description: "Request Asset Loan",
    },

    // Utility shortcuts
    "/": { action: "search", description: "Focus Search" },
    "?": { action: "help", description: "Show Keyboard Shortcuts" },
};

/**
 * Initialize keyboard navigation
 */
export function initKeyboardNavigation() {
    // Global keyboard event listener
    document.addEventListener("keydown", handleKeyboardShortcut);

    // Ensure skip links are visible on focus
    enhanceSkipLinks();

    // Add keyboard shortcut hints to help modal
    addKeyboardShortcutHelp();

    console.log("Keyboard navigation initialized");
}

/**
 * Handle keyboard shortcut
 *
 * @param {KeyboardEvent} event - Keyboard event
 */
function handleKeyboardShortcut(event) {
    // Ignore if user is typing in input/textarea
    const activeElement = document.activeElement;
    const isInputField =
        activeElement &&
        (activeElement.tagName === "INPUT" ||
            activeElement.tagName === "TEXTAREA" ||
            activeElement.isContentEditable);

    // Alt + key shortcuts (navigation)
    if (event.altKey && !event.shiftKey && !event.ctrlKey) {
        const key = event.key.toLowerCase();
        const shortcut = shortcuts[key];

        if (shortcut && shortcut.url) {
            event.preventDefault();
            navigateToUrl(shortcut.url, shortcut.description);
            return;
        }
    }

    // Alt + Shift + key shortcuts (actions)
    if (event.altKey && event.shiftKey && !event.ctrlKey) {
        const key = event.key.toLowerCase();
        const shortcut = shortcuts[key];

        if (shortcut && shortcut.url) {
            event.preventDefault();
            navigateToUrl(shortcut.url, shortcut.description);
            return;
        }
    }

    // Utility shortcuts (no modifiers, unless in input field)
    if (!event.altKey && !event.shiftKey && !event.ctrlKey && !isInputField) {
        const key = event.key;

        // / - Focus search
        if (key === "/") {
            event.preventDefault();
            focusSearch();
            return;
        }

        // ? - Show keyboard shortcuts help
        if (key === "?") {
            event.preventDefault();
            showKeyboardShortcutsHelp();
            return;
        }
    }

    // Escape - Close modals/dropdowns
    if (event.key === "Escape") {
        closeModalsAndDropdowns();
    }
}

/**
 * Navigate to URL using Livewire wire:navigate
 *
 * @param {string} url - Target URL
 * @param {string} description - Action description for screen readers
 */
function navigateToUrl(url, description) {
    // Announce navigation to screen readers
    if (window.ariaAnnounce) {
        window.ariaAnnounce(`Navigating to ${description}`);
    }

    // Use Livewire navigation if available
    if (window.Livewire) {
        window.Livewire.navigate(url);
    } else {
        window.location.href = url;
    }
}

/**
 * Focus search input
 */
function focusSearch() {
    const searchInput = document.querySelector(
        'input[type="search"], input[name="search"], input[placeholder*="Search"]'
    );

    if (searchInput) {
        searchInput.focus();
        searchInput.select();

        if (window.ariaAnnounce) {
            window.ariaAnnounce("Search focused");
        }
    }
}

/**
 * Show keyboard shortcuts help modal
 */
function showKeyboardShortcutsHelp() {
    // Dispatch Livewire event to show help modal
    if (window.Livewire) {
        window.Livewire.dispatch("show-keyboard-shortcuts");
    }

    // Fallback: create simple modal
    if (!document.getElementById("keyboard-shortcuts-modal")) {
        createKeyboardShortcutsModal();
    }
}

/**
 * Create keyboard shortcuts help modal
 */
function createKeyboardShortcutsModal() {
    const modal = document.createElement("div");
    modal.id = "keyboard-shortcuts-modal";
    modal.className =
        "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50";
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-labelledby", "keyboard-shortcuts-title");
    modal.setAttribute("aria-modal", "true");

    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 id="keyboard-shortcuts-title" class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Keyboard Shortcuts
                </h2>
                <button
                    onclick="document.getElementById('keyboard-shortcuts-modal').remove()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1"
                    aria-label="Close"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Navigation (Alt + key)</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + D</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Dashboard</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + S</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Submissions</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + P</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Profile</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + H</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Helpdesk</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + L</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Loans</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + A</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Approvals</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Actions (Alt + Shift + key)</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + Shift + N</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">New Ticket</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Alt + Shift + R</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Request Loan</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Utilities</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">/</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Focus Search</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">?</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Show This Help</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Esc</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">Close Modals</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-center">
                <button
                    onclick="document.getElementById('keyboard-shortcuts-modal').remove()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Close
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Focus close button
    setTimeout(() => {
        const closeButton = modal.querySelector('button[aria-label="Close"]');
        if (closeButton) {
            closeButton.focus();
        }
    }, 100);

    // Close on click outside
    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    });
}

/**
 * Close all modals and dropdowns
 */
function closeModalsAndDropdowns() {
    // Dispatch Livewire event
    if (window.Livewire) {
        window.Livewire.dispatch("close-modal");
        window.Livewire.dispatch("close-dropdown");
    }

    // Close keyboard shortcuts modal
    const shortcutsModal = document.getElementById("keyboard-shortcuts-modal");
    if (shortcutsModal) {
        shortcutsModal.remove();
    }
}

/**
 * Enhance skip links visibility on focus
 */
function enhanceSkipLinks() {
    const skipLinks = document.querySelectorAll(
        '.skip-to-content, [href="#main-content"]'
    );

    skipLinks.forEach((link) => {
        link.addEventListener("focus", () => {
            link.classList.add("not-sr-only");
            link.style.position = "fixed";
            link.style.top = "0";
            link.style.left = "0";
            link.style.zIndex = "9999";
        });

        link.addEventListener("blur", () => {
            link.classList.remove("not-sr-only");
            link.style.position = "";
            link.style.top = "";
            link.style.left = "";
            link.style.zIndex = "";
        });
    });
}

/**
 * Add keyboard shortcut help button to navigation
 */
function addKeyboardShortcutHelp() {
    // Add help button to user menu or navigation
    const userMenu = document.getElementById("user-menu");

    if (userMenu && !document.getElementById("keyboard-shortcuts-help-btn")) {
        const helpButton = document.createElement("button");
        helpButton.id = "keyboard-shortcuts-help-btn";
        helpButton.className =
            "text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-2";
        helpButton.setAttribute(
            "aria-label",
            "Keyboard shortcuts (Press ? for help)"
        );
        helpButton.setAttribute("title", "Keyboard shortcuts (?)");
        helpButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;
        helpButton.addEventListener("click", showKeyboardShortcutsHelp);

        userMenu.insertBefore(helpButton, userMenu.firstChild);
    }
}

// Initialize on DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initKeyboardNavigation);
} else {
    initKeyboardNavigation();
}

// Export for manual initialization
export default initKeyboardNavigation;
