/**
 * Percy Configuration for ICTServe v3.6.1 Visual Testing
 *
 * This configuration supports:
 * - True Hybrid Architecture (guest + authenticated users)
 * - Bahasa Melayu interface validation
 * - Laravel 12.43.1 + Livewire 3.7.3 + Filament 4.3.1 stack
 * - Responsive design testing across multiple viewports
 * - WCAG 2.2 AA compliance visual validation
 */
module.exports = {
	version: 2,

	// Project configuration
	projectName: "ictserve-v3.6.1-visual-testing",

	// Discovery settings for local development
	discovery: {
		allowedHostnames: ["localhost", "127.0.0.1"],
		networkIdleTimeout: 100,
		disableCache: false,
	},

	// Default snapshot configuration
	snapshot: {
		// Responsive breakpoints for ICTServe v3.6.1
		widths: [375, 768, 1024, 1280, 1920],
		minHeight: 1024,

		// CSS to hide dynamic content that changes between snapshots
		percyCSS: `
      /* Hide dynamic timestamps and loading states */
      .dynamic-timestamp { display: none !important; }
      .loading-spinner { visibility: hidden !important; }
      .skeleton-loader { display: none !important; }
      
      /* Hide language switcher (v3.6.0+ Bahasa Melayu only) */
      .language-switcher { display: none !important; }
      
      /* Hide dynamic user-specific content */
      .user-avatar { visibility: hidden !important; }
      .last-login-time { display: none !important; }
      
      /* Hide real-time notifications and badges */
      .notification-badge { display: none !important; }
      .realtime-counter { display: none !important; }
      
      /* Hide dynamic form validation messages during snapshot */
      .validation-message { display: none !important; }
      
      /* Hide Livewire loading states */
      [wire\\:loading] { display: none !important; }
      .wire-loading { display: none !important; }
      
      /* Hide dynamic Filament admin content */
      .fi-loading { display: none !important; }
      .fi-notification { display: none !important; }
      
      /* Ensure consistent focus states */
      *:focus { outline: 2px solid #3b82f6 !important; }
    `,

		// Enable JavaScript for Livewire and dynamic content
		enableJavaScript: true,

		// Wait for network idle to ensure Livewire components are loaded
		waitForTimeout: 1000,
		waitForSelector: null,
	},

	// Upload configuration
	upload: {
		networkIdleTimeout: 750,
		requestHeaders: {},
		userAgent: null,
	},

	// Client configuration
	clientInfo: "ICTServe v3.6.1 Percy Integration",
	environmentInfo:
		"Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Playwright 1.56.1",
};
