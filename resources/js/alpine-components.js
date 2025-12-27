/**
 * Global Alpine.js Component Definins
 *
 * These components are registered globally so they can be used
 * in any Blade template without needing @push('scripts').
 *
 * @trace D03-FR-011 (Optimistic UI), Task 3.1.10 (Searchable Select)
 */

const registerAlpineComponents = () => {
	const Alpine = window.Alpine;
	if (!Alpine || registerAlpineComponents.hasRun) {
		return;
	}
	registerAlpineComponents.hasRun = true;
	/**
	 * Searchable Select Alpine.js Component
	 *
	 * Provides virtual scrolling and keyboard navigation for large option lists.
	 * WCAG 2.2 AA compliant with proper ARIA attributes.
	 */
	Alpine.data("searchableSelect", (config) => ({
		// Configuration
		options: config.options || [],
		placeholder: config.placeholder || "Pilih pilihan",
		searchPlaceholder: config.searchPlaceholder || "Cari...",
		name: config.name || "",
		wireModel: config.wireModel || "",

		// State
		isOpen: false,
		searchQuery: "",
		selectedValue: config.selected,
		selectedLabel: "",
		focusedIndex: -1,

		// IDs for ARIA
		labelId: "label-" + Math.random().toString(36).substring(2, 11),
		listboxId: "listbox-" + Math.random().toString(36).substring(2, 11),

		// Computed: Filtered options based on search query
		get filteredOptions() {
			if (!this.searchQuery) {
				return this.options;
			}
			const query = this.searchQuery.toLowerCase();
			return this.options.filter((option) =>
				option.name.toLowerCase().includes(query)
			);
		},

		// Initialize component
		init() {
			// Set initial selected label
			if (this.selectedValue) {
				const selected = this.options.find((o) => o.id == this.selectedValue);
				if (selected) {
					this.selectedLabel = selected.name;
				}
			}

			// Watch for external changes to selected value
			this.$watch("selectedValue", (value) => {
				const selected = this.options.find((o) => o.id == value);
				this.selectedLabel = selected ? selected.name : "";

				// Update Livewire if wireModel is set
				if (this.wireModel && this.$wire) {
					this.$wire.set(this.wireModel, value);
				}
			});

			// Listen for Livewire updates to sync back
			if (this.wireModel && this.$wire) {
				this.$wire.$watch(this.wireModel, (value) => {
					if (this.selectedValue != value) {
						this.selectedValue = value;
						const selected = this.options.find((o) => o.id == value);
						this.selectedLabel = selected ? selected.name : "";
					}
				});
			}
		},

		// Toggle dropdown
		toggle() {
			this.isOpen ? this.close() : this.open();
		},

		// Open dropdown
		open() {
			this.isOpen = true;
			this.focusedIndex = -1;
			this.$nextTick(() => {
				this.$refs.searchInput?.focus();
			});
		},

		// Close dropdown
		close() {
			this.isOpen = false;
			this.searchQuery = "";
			this.focusedIndex = -1;
		},

		// Select an option
		selectOption(option) {
			this.selectedValue = option.id;
			this.selectedLabel = option.name;
			this.close();

			// Update Livewire immediately
			if (this.wireModel && this.$wire) {
				this.$wire.set(this.wireModel, option.id);
			}

			// Dispatch change event for other listeners
			this.$dispatch("change", {
				value: option.id,
				label: option.name,
			});

			// Also dispatch input event for form compatibility
			this.$dispatch("input", option.id);
		},

		// Focus navigation
		focusFirstOption() {
			this.focusedIndex = 0;
			this.scrollToFocused();
		},

		focusLastOption() {
			this.focusedIndex = this.filteredOptions.length - 1;
			this.scrollToFocused();
		},

		focusNextOption() {
			if (this.focusedIndex < this.filteredOptions.length - 1) {
				this.focusedIndex++;
				this.scrollToFocused();
			}
		},

		focusPreviousOption() {
			if (this.focusedIndex > 0) {
				this.focusedIndex--;
				this.scrollToFocused();
			}
		},

		selectFocusedOption() {
			if (
				this.focusedIndex >= 0 &&
				this.focusedIndex < this.filteredOptions.length
			) {
				this.selectOption(this.filteredOptions[this.focusedIndex]);
			}
		},

		// Scroll to keep focused option visible
		scrollToFocused() {
			this.$nextTick(() => {
				const listbox = this.$refs.listbox;
				const focused = listbox?.querySelector(
					`[id="option-${this.filteredOptions[this.focusedIndex]?.id}"]`
				);
				if (focused && listbox) {
					focused.scrollIntoView({
						block: "nearest",
					});
				}
			});
		},
	}));

	/**
	 * Optimistic UI State Management for Helpdesk Form
	 *
	 * Provides immediate visual feedback while server processes submission.
	 * Handles rollback on errors with smooth transitions.
	 *
	 * @trace R09 (Optimistic UI), D03-FR-011
	 */
	Alpine.data("optimisticHelpdeskForm", () => ({
		// State tracking
		isOptimistic: false,
		ticketNumber: "",
		errorMessage: "",

		/**
		 * Handle optimistic submission start
		 * Shows immediate success state while server processes
		 */
		handleOptimisticStart(detail) {
			this.isOptimistic = true;
			this.ticketNumber = detail.ticketNumber;

			// Scroll to success message smoothly
			this.$el.scrollIntoView({
				behavior: "smooth",
				block: "start",
			});

			// Announce to screen readers
			this.announceToScreenReader("Sedang memproses penghantaran tiket anda...");
		},

		/**
		 * Handle confirmed submission
		 * Updates with actual ticket number from server
		 */
		handleSubmissionConfirmed(detail) {
			this.isOptimistic = false;
			this.ticketNumber = detail.ticketNumber;

			// Announce success to screen readers
			this.announceToScreenReader(
				"Tiket berjaya dihantar. Nombor tiket anda ialah " + detail.ticketNumber
			);
		},

		/**
		 * Handle submission rollback on error
		 * Returns to form state with error message
		 */
		handleSubmissionRollback(detail) {
			this.isOptimistic = false;
			this.errorMessage = detail.message;

			// Scroll to error message
			this.$el.scrollIntoView({
				behavior: "smooth",
				block: "start",
			});

			// Announce error to screen readers
			this.announceToScreenReader("Penghantaran gagal. " + detail.message);
		},

		/**
		 * Announce message to screen readers via ARIA live region
		 */
		announceToScreenReader(message) {
			const liveRegion = document.getElementById("optimistic-ui-announcer");
			if (liveRegion) {
				liveRegion.textContent = message;
			}
		},
	}));

	/**
	 * FAQ Bot Widget Alpine.js Component
	 *
	 * Handles widget state sync, focus management, and accessibility behaviors.
	 *
	 * @trace D03-FR-AI-001 (FAQ Bot Widget)
	 */
	Alpine.data("faqBotWidget", (isOpen, isMinimized, announcement) => ({
		isOpen,
		isMinimized,
		announcement,

		init() {
			this.$watch("announcement", (value) => {
				if (value) {
					this.$dispatch("announce", { message: value });
				}
			});

			this.$watch("isOpen", (open) => {
				if (open) {
					this.focusFirst();
				} else {
					this.$nextTick(() => this.$refs.toggleButton?.focus());
				}
			});
		},

		focusableEls() {
			return [
				...(this.$refs.panel?.querySelectorAll(
					'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
				) || []),
			].filter((el) => !el.disabled && el.offsetParent !== null);
		},

		trapFocus(event) {
			if (!this.isOpen || this.isMinimized) {
				return;
			}
			const els = this.focusableEls();
			if (!els.length) {
				return;
			}
			const first = els[0];
			const last = els[els.length - 1];
			const active = document.activeElement;
			if (event.shiftKey && active === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && active === last) {
				event.preventDefault();
				first.focus();
			}
		},

		focusFirst() {
			this.$nextTick(() => {
				const target =
					this.$refs.panel?.querySelector("[data-initial-focus]") ||
					this.focusableEls()[0];
				target?.focus();
			});
		},
	}));
};

if (window.Alpine) {
	registerAlpineComponents();
} else {
	document.addEventListener("alpine:init", registerAlpineComponents);
}

window.registerAlpineComponents = registerAlpineComponents;

document.addEventListener("livewire:init", () => {
	window.Livewire?.on("announce", () => {
		const container = document.querySelector('[x-ref="messagesContainer"]');
		if (container) {
			setTimeout(() => {
				container.scrollTop = container.scrollHeight;
			}, 100);
		}
	});

	document.addEventListener("keydown", (event) => {
		if (event.key === "Escape") {
			const widget = document.querySelector('[x-data*="isOpen"]');
			if (widget && widget.__x?.$data?.isOpen) {
				window.Livewire?.dispatch("closeWidget");
			}
		}
	});
});
