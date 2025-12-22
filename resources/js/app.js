import "./bootstrap";
import "./alpine-components";
import "./alpine-patterns";
import "./portal-mobile";
import "./portal-echo";
import "./submission-echo";
import "./aria-announcements";
import "./keyboard-navigation";
import "./performance-monitor";
import "./script-loader";

// Theme preference application function (used by event listeners)
function applyThemePreference(theme) {
	const normalized = theme === "dark" ? "dark" : "light";
	const root = document.documentElement;

	try {
		localStorage.setItem("theme", normalized);
	} catch (error) {
		// Ignore localStorage errors (private browsing, etc.)
	}

	if (normalized === "dark") {
		root.classList.add("dark");
		root.setAttribute("data-theme", "dark");
	} else {
		root.classList.remove("dark");
		root.setAttribute("data-theme", "light");
	}
}

// Livewire browser event (dispatched from ThemeToggle component)
window.addEventListener("theme-changed", (event) => {
	applyThemePreference(event?.detail?.theme);
});

// Backwards compatibility for any legacy dispatchers
window.addEventListener("themeChanged", (event) => {
	applyThemePreference(event?.detail?.theme);
});
