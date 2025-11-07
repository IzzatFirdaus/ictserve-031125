import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Enable class-based dark mode
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./app/Livewire/**/*.php",
        "./app/Filament/**/*.php",
        "./app/View/Components/**/*.php",
    ],

    safelist: [
        // WCAG 2.2 AA Focus indicators
        'focus:ring-2',
        'focus:ring-4',
        'focus:ring-blue-300',
        'focus:ring-green-300',
        'focus:ring-amber-300',
        'focus:ring-red-300',
        'focus:ring-gray-300',
        'focus:ring-offset-2',
        'focus:outline-none',
        // Dynamic color classes for components
        'bg-primary-50',
        'bg-secondary-50',
        'bg-success-50',
        'bg-warning-50',
        'bg-danger-50',
        'text-primary-600',
        'text-secondary-600',
        'text-success-600',
        'text-warning-600',
        'text-danger-600',
        'border-primary-200',
        'border-secondary-200',
        'border-success-200',
        'border-warning-200',
        'border-danger-200',
        // Layout utilities
        'min-h-[44px]',
        'min-w-[44px]',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // WCAG 2.2 AA Compliant Color Palette
                // Primary Colors (MOTAC Branding)
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb", // 4.5:1 contrast ratio
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                },
                secondary: {
                    50: "#f9fafb",
                    100: "#f3f4f6",
                    200: "#e5e7eb",
                    300: "#d1d5db",
                    400: "#9ca3af",
                    500: "#6b7280",
                    600: "#4b5563", // 4.5:1 contrast ratio
                    700: "#374151",
                    800: "#1f2937",
                    900: "#111827",
                },
                success: {
                    50: "#f0fdf4",
                    100: "#dcfce7",
                    200: "#bbf7d0",
                    300: "#86efac",
                    400: "#4ade80",
                    500: "#22c55e",
                    600: "#16a34a", // 4.5:1 contrast ratio
                    700: "#15803d",
                    800: "#166534",
                    900: "#14532d",
                },
                warning: {
                    50: "#fffbeb",
                    100: "#fef3c7",
                    200: "#fde68a",
                    300: "#fcd34d",
                    400: "#fbbf24",
                    500: "#f59e0b",
                    600: "#d97706", // 4.5:1 contrast ratio
                    700: "#b45309",
                    800: "#92400e",
                    900: "#78350f",
                },
                danger: {
                    50: "#fef2f2",
                    100: "#fee2e2",
                    200: "#fecaca",
                    300: "#fca5a5",
                    400: "#f87171",
                    500: "#ef4444",
                    600: "#dc2626", // 4.5:1 contrast ratio
                    700: "#b91c1c",
                    800: "#991b1b",
                    900: "#7f1d1d",
                },
                // MOTAC Brand Colors (legacy support)
                "motac-blue": {
                    DEFAULT: "#2563eb",
                    light: "#dbeafe",
                    dark: "#1e40af",
                },
            },
            minHeight: {
                44: "44px", // WCAG 2.5.8 minimum touch target
            },
            minWidth: {
                44: "44px", // WCAG 2.5.8 minimum touch target
            },
            ringWidth: {
                3: "3px", // Focus indicator width
                4: "4px", // Enhanced focus indicator
            },
            ringOffsetWidth: {
                2: "2px", // Focus indicator offset
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [
        forms,
        function({ addUtilities }) {
            const newUtilities = {
                '.focus-visible-only': {
                    '&:focus:not(:focus-visible)': {
                        outline: 'none',
                        'box-shadow': 'none',
                    },
                },
                '.sr-only-focusable': {
                    '&:focus': {
                        position: 'static',
                        width: 'auto',
                        height: 'auto',
                        padding: '0',
                        margin: '0',
                        overflow: 'visible',
                        clip: 'auto',
                        'white-space': 'normal',
                    },
                },
            };
            addUtilities(newUtilities);
        },
    ],
};
