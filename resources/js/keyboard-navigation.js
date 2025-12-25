/**
 * Keyboard Navigation Enhancement for ICTServe
 *
 * Provides enhanced keyboard navigation support for better accessibility.
 * Ensures WCAG 2.2 AA compliance for keyboard-only users.
 *
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §10.2 (Keyboard Navigation)
 * @wcag SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 * @version 3.6.1
 */

class KeyboardNavigationEnhancer {
	constructor() {
		this.focusableElements = [
			"a[href]",
			"button:not([disabled])",
			"input:not([disabled])",
			"select:not([disabled])",
			"textarea:not([disabled])",
			'[tabindex]:not([tabindex="-1"])',
			'[contenteditable="true"]',
		].join(", ");

		this.init();
	}

	/**
	 * Initialize keyboard navigation enhancements
	 */
	init() {
		this.setupFocusManagement();
		this.setupSkipLinks();
		this.setupModalFocusTrap();
		this.setupDropdownNavigation();
	}

	/**
	 * Setup focus management
	 */
	setupFocusManagement() {
		// Track focus for debugging (development only)
		if (import.meta.env.DEV) {
			document.addEventListener("focusin", (e) => {
				console.log("Focus:", e.target);
			});
		}

		// Ensure focus is visible
		document.addEventListener("keydown", (e) => {
			if (e.key === "Tab") {
				document.body.classList.add("keyboard-navigation");
			}
		});

		// Remove keyboard navigation class on mouse use
		document.addEventListener("mousedown", () => {
			document.body.classList.remove("keyboard-navigation");
		});
	}

	/**
	 * Setup skip links for main content
	 */
	setupSkipLinks() {
		// Create skip link if it doesn't exist
		let skipLink = document.getElementById("skip-to-main");

		if (!skipLink) {
			skipLink = document.createElement("a");
			skipLink.id = "skip-to-main";
			skipLink.href = "#main-content";
			skipLink.textContent = "Langkau ke kandungan utama";
			skipLink.className =
				"sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-md focus:shadow-lg";

			// Insert at the beginning of body
			document.body.insertBefore(skipLink, document.body.firstChild);
		}

		// Handle skip link activation
		skipLink.addEventListener("click", (e) => {
			e.preventDefault();
			const mainContent =
				document.getElementById("main-content") ||
				document.querySelector("main");

			if (mainContent) {
				mainContent.focus();
				mainContent.scrollIntoView({ behavior: "smooth" });
			}
		});
	}

	/**
	 * Setup modal focus trap
	 */
	setupModalFocusTrap() {
		document.addEventListener("keydown", (e) => {
			if (e.key === "Escape") {
				// Close any open modals/dropdowns
				const openModals = document.querySelectorAll(
					'[role="dialog"][aria-hidden="false"], .modal.show, [x-show="true"]'
				);

				openModals.forEach((modal) => {
					// Try different methods to close modal
					if (modal.hasAttribute("x-show")) {
						// Alpine.js modal
						modal.dispatchEvent(new CustomEvent("close-modal"));
					} else if (modal.classList.contains("modal")) {
						// Bootstrap-style modal
						modal.classList.remove("show");
					}
				});
			}

			// Trap focus in modals
			if (e.key === "Tab") {
				const activeModal = document.querySelector(
					'[role="dialog"][aria-hidden="false"]'
				);

				if (activeModal) {
					this.trapFocus(e, activeModal);
				}
			}
		});
	}

	/**
	 * Trap focus within an element
	 * @param {KeyboardEvent} e - Keyboard event
	 * @param {Element} container - Container to trap focus within
	 */
	trapFocus(e, container) {
		const focusableElements = container.querySelectorAll(
			this.focusableElements
		);
		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];

		if (e.shiftKey) {
			// Shift + Tab
			if (document.activeElement === firstElement) {
				e.preventDefault();
				lastElement.focus();
			}
		} else {
			// Tab
			if (document.activeElement === lastElement) {
				e.preventDefault();
				firstElement.focus();
			}
		}
	}

	/**
	 * Setup dropdown navigation
	 */
	setupDropdownNavigation() {
		document.addEventListener("keydown", (e) => {
			const dropdown = e.target.closest('[role="listbox"], [role="menu"]');

			if (!dropdown) return;

			const options = dropdown.querySelectorAll(
				'[role="option"], [role="menuitem"]'
			);
			const currentIndex = Array.from(options).indexOf(document.activeElement);

			switch (e.key) {
				case "ArrowDown":
					e.preventDefault();
					const nextIndex =
						currentIndex < options.length - 1 ? currentIndex + 1 : 0;
					options[nextIndex].focus();
					break;

				case "ArrowUp":
					e.preventDefault();
					const prevIndex =
						currentIndex > 0 ? currentIndex - 1 : options.length - 1;
					options[prevIndex].focus();
					break;

				case "Home":
					e.preventDefault();
					options[0].focus();
					break;

				case "End":
					e.preventDefault();
					options[options.length - 1].focus();
					break;

				case "Enter":
				case " ":
					e.preventDefault();
					document.activeElement.click();
					break;
			}
		});
	}

	/**
	 * Focus first focusable element in container
	 * @param {Element} container - Container to search within
	 */
	focusFirst(container) {
		const firstFocusable = container.querySelector(this.focusableElements);
		if (firstFocusable) {
			firstFocusable.focus();
		}
	}

	/**
	 * Get all focusable elements in container
	 * @param {Element} container - Container to search within
	 * @returns {NodeList} Focusable elements
	 */
	getFocusableElements(container) {
		return container.querySelectorAll(this.focusableElements);
	}
}

// Initialize and expose globally
window.KeyboardNavigationEnhancer = new KeyboardNavigationEnhancer();

// Export for module usage
export default window.KeyboardNavigationEnhancer;
