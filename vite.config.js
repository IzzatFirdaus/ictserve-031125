import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
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
                    "portal-dashboard": [
                        "./resources/js/performance-monitor.js",
                    ],
                },
                // Optimize chunk naming for better caching
                chunkFileNames: "js/[name]-[hash].js",
                entryFileNames: "js/[name]-[hash].js",
                assetFileNames: (assetInfo) => {
                    // Organize assets by type
                    if (assetInfo.name.endsWith(".css")) {
                        return "css/[name]-[hash][extname]";
                    }
                    if (
                        /\.(png|jpe?g|svg|gif|webp|avif)$/.test(assetInfo.name)
                    ) {
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
    },
    // Performance optimization
    optimizeDeps: {
        include: ["axios", "web-vitals"],
        exclude: [], // Exclude large dependencies from pre-bundling if needed
    },
    // Server configuration for development
    server: {
        hmr: {
            overlay: true, // Show errors as overlay
        },
    },
});
