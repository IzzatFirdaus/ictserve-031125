import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
	plugins: [
		laravel({
			input: [
				"resources/css/app.css",
				"resources/js/app.js",
				"resources/css/filament/admin/theme.css",
				"resources/css/filament-fixes.css",
			],
			refresh: true,
		}),
	],
	build: {
		// Image optimization settings
		assetsInlineLimit: 4096, // 4kb - inline small assets as base64
		chunkSizeWarningLimit: 1000, // 1MB warning threshold
		cssCodeSplit: true, // Split CSS per route
		rollupOptions: {
			output: {
				// Manual chunk splitting for better caching
				manualChunks: {
					// Vendor chunks
					"vendor-axios": ["axios"],
					"vendor-vitals": ["web-vitals"],
					// Portal chunks (code splitting by route)
					"portal-dashboard": ["./resources/js/performance-monitor.js"],
				},
				// Optimize chunk naming for better caching
				chunkFileNames: "js/[name]-[hash].js",
				entryFileNames: "js/[name]-[hash].js",
				assetFileNames: (assetInfo) => {
					// Organize assets by type
					if (assetInfo.name.endsWith(".css")) {
						return "css/[name]-[hash][extname]";
					}
					if (/\.(png|jpe?g|svg|gif|webp|avif)$/.test(assetInfo.name)) {
						return "images/[name]-[hash][extname]";
					}
					if (/\.(woff2?|eot|ttf|otf)$/.test(assetInfo.name)) {
						return "fonts/[name]-[hash][extname]";
					}
					return "assets/[name]-[hash][extname]";
				},
			},
		},
		// Minification settings
		minify: "terser",
		terserOptions: {
			compress: {
				drop_console: true, // Remove console.log in production
				drop_debugger: true,
				pure_funcs: ["console.log", "console.info"], // Remove specific console methods
			},
			format: {
				comments: false, // Remove comments
			},
		},
		cssMinify: "lightningcss", // Use Lightning CSS for better Tailwind 4.x compatibility
	},
	// Performance optimization
	optimizeDeps: {
		include: ["axios", "web-vitals"],
		exclude: [], // Exclude large dependencies from pre-bundling if needed
	},
	// Server configuration for development
	server: {
		host: process.env.VITE_DEV_SERVER_HOST || '0.0.0.0', // Use 0.0.0.0 for Docker, fallback for local
		port: process.env.VITE_DEV_SERVER_PORT ? parseInt(process.env.VITE_DEV_SERVER_PORT) : 5173,
		strictPort: false, // Allow fallback ports if 5173 is taken
		hmr: {
			host: process.env.VITE_HMR_HOST || 'localhost',
			overlay: true, // Show errors as overlay
		},
		watch: {
			usePolling: process.env.VITE_USE_POLLING === 'true', // Enable for Docker on some systems
		},
	},
	// Suppress false positive esbuild CSS warnings
	esbuild: {
		logOverride: {
			"unsupported-css-property": "silent", // Suppress CSS @property declaration warnings
		},
	},
});
