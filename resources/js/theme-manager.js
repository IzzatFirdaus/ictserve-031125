/**
 * ICTServe Theme Management System
 *
 * Global theme API that theme components depend on.
 * Provides centralized theme state management with localStorage persistence.
 *
 * @trace D12 §4 (Color System), D14 §6.1.2 (Theme Switcher)
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard)
 * @requirements SRS-UX-007 (Dark Mode Support)
 * @version 3.6.1
 */

class ICTServeThemeManager {
	constructor() {
		this.currentTheme = "light"; // Default per v3.6.0
		this.storageKey = "ictserve-theme";
		this.init();
	}

	/**
	 * Initialize theme manager
	 */
	init() {
		// Load theme from localStorage or default to light
		this.currentTheme = this.getStoredTheme() || "light";

		// Apply theme immediately
		this.applyTheme(this.currentTheme);

		// Listen for storage changes (multi-tab sync)
		window.addEventListener("storage", (e) => {
			if (e.key === this.storageKey && e.newValue) {
				this.currentTheme = e.newValue;
				this.applyTheme(this.currentTheme);
				this.dispatchThemeChange(this.currentTheme);
			}
		});

		// Listen for system theme changes
		if (window.matchMedia) {
			const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
			mediaQuery.addEventListener("change", (e) => {
				// Only auto-switch if no explicit theme is set
				if (!this.getStoredTheme()) {
					const systemTheme = e.matches ? "dark" : "light";
					this.set(systemTheme);
				}
			});
		}
	}

	/**
	 * Get current theme
	 * @returns {string} Current theme ('light' or 'dark')
	 */
	get() {
		return this.currentTheme;
	}

	/**
	 * Set theme
	 * @param {string} theme - Theme to set ('light' or 'dark')
	 */
	set(theme) {
		const normalizedTheme = theme === "dark" ? "dark" : "light";

		if (this.currentTheme !== normalizedTheme) {
			this.currentTheme = normalizedTheme;
			this.storeTheme(normalizedTheme);
			this.applyTheme(normalizedTheme);
			this.dispatchThemeChange(normalizedTheme);
		}
	}

	/**
	 * Toggle between light and dark themes
	 */
	toggle() {
		const newTheme = this.currentTheme === "dark" ? "light" : "dark";
		this.set(newTheme);
	}

	/**
	 * Get stored theme from localStorage
	 * @returns {string|null} Stored theme or null
	 */
	getStoredTheme() {
		try {
			return localStorage.getItem(this.storageKey);
		} catch (error) {
			// Handle localStorage errors (private browsing, etc.)
			console.warn("ICTServe Theme: localStorage not available", error);
			return null;
		}
	}

	/**
	 * Store theme in localStorage
	 * @param {string} theme - Theme to store
	 */
	storeTheme(theme) {
		try {
			localStorage.setItem(this.storageKey, theme);
		} catch (error) {
			// Handle localStorage errors (private browsing, etc.)
			console.warn("ICTServe Theme: Could not store theme", error);
		}
	}

	/**
	 * Apply theme to document
	 * @param {string} theme - Theme to apply
	 */
	applyTheme(theme) {
		const root = document.documentElement;

		if (theme === "dark") {
			root.classList.add("dark");
			root.setAttribute("data-theme", "dark");
		} else {
			root.classList.remove("dark");
			root.setAttribute("data-theme", "light");
		}

		// Update meta theme-color for mobile browsers
		this.updateMetaThemeColor(theme);
	}

	/**
	 * Update meta theme-color for mobile browsers
	 * @param {string} theme - Current theme
	 */
	updateMetaThemeColor(theme) {
		let metaThemeColor = document.querySelector('meta[name="theme-color"]');

		if (!metaThemeColor) {
			metaThemeColor = document.createElement("meta");
			metaThemeColor.name = "theme-color";
			document.head.appendChild(metaThemeColor);
		}

		// Use ICTServe brand colors per D14 style guide
		const colors = {
			light: "#ffffff", // Light theme background
			dark: "#1e293b", // Dark theme background (slate-800)
		};

		metaThemeColor.content = colors[theme] || colors.light;
	}

	/**
	 * Dispatch theme change event
	 * @param {string} theme - New theme
	 */
	dispatchThemeChange(theme) {
		// Dispatch custom event for other components
		window.dispatchEvent(
			new CustomEvent("theme-changed", {
				detail: { theme },
			})
		);

		// Also dispatch legacy event for backward compatibility
		window.dispatchEvent(
			new CustomEvent("themeChanged", {
				detail: { theme },
			})
		);
	}

	/**
	 * Get system preference
	 * @returns {string} System theme preference
	 */
	getSystemPreference() {
		if (
			window.matchMedia &&
			window.matchMedia("(prefers-color-scheme: dark)").matches
		) {
			return "dark";
		}
		return "light";
	}

	/**
	 * Reset to system preference
	 */
	resetToSystem() {
		try {
			localStorage.removeItem(this.storageKey);
		} catch (error) {
			console.warn("ICTServe Theme: Could not remove stored theme", error);
		}

		const systemTheme = this.getSystemPreference();
		this.currentTheme = systemTheme;
		this.applyTheme(systemTheme);
		this.dispatchThemeChange(systemTheme);
	}

	/**
	 * Check if dark mode is active
	 * @returns {boolean} True if dark mode is active
	 */
	isDark() {
		return this.currentTheme === "dark";
	}

	/**
	 * Check if light mode is active
	 * @returns {boolean} True if light mode is active
	 */
	isLight() {
		return this.currentTheme === "light";
	}
}

// Initialize theme manager and expose globally
window.ICTServeTheme = new ICTServeThemeManager();

// Export for module usage
export default window.ICTServeTheme;
