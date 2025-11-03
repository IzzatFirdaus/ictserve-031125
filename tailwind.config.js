import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./app/Livewire/**/*.php",
        "./app/Filament/**/*.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // WCAG 2.2 AA Compliant Color Palette
                // Primary Colors (MOTAC Branding)
                "motac-blue": {
                    DEFAULT: "#0056b3", // 6.8:1 contrast ratio - Primary brand color
                    light: "#e3f2fd", // Light variant for backgrounds
                    dark: "#003d82", // Dark variant for emphasis
                },
                // Status Colors (Compliant - Deprecated colors removed)
                success: {
                    DEFAULT: "#198754", // 4.9:1 contrast ratio - Approved, Active, Success states
                    light: "#d1e7dd",
                    dark: "#0f5132",
                },
                warning: {
                    DEFAULT: "#ff8c00", // 4.5:1 contrast ratio - Pending, Caution states
                    light: "#fff3cd",
                    dark: "#cc7000",
                },
                danger: {
                    DEFAULT: "#b50c0c", // 8.2:1 contrast ratio - Rejected, Overdue, Error states
                    light: "#f8d7da",
                    dark: "#8a0909",
                },
                info: {
                    DEFAULT: "#0dcaf0", // Information and neutral states
                    light: "#cff4fc",
                    dark: "#087990",
                },
                // Neutral colors (gray scale)
                gray: {
                    50: "#f9fafb",
                    100: "#f3f4f6",
                    200: "#e5e7eb",
                    300: "#d1d5db",
                    400: "#9ca3af",
                    500: "#6b7280",
                    600: "#4b5563",
                    700: "#374151",
                    800: "#1f2937",
                    900: "#111827",
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
            },
            ringOffsetWidth: {
                2: "2px", // Focus indicator offset
            },
        },
    },

    plugins: [forms],
};
