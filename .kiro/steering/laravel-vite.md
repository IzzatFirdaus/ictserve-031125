---
inclusion:
  always: true
  fileMatchPattern:
    - 'vite.config.js'
    - 'resources/js/**/*.js'
    - 'resources/css/**/*.css'
    - 'package.json'
  applyWhen:
    - Frontend asset compilation
    - JavaScript bundling
    - CSS processing
    - Hot module replacement
---

# Laravel Vite Asset Bundling Guidelines

Laravel Vite provides fast frontend asset compilation with hot module replacement (HMR).

**Version**: 7.3.0 (ICTServe v3.6.1)

## Configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['axios', 'alpinejs'],
                    'echo': ['laravel-echo', 'pusher-js'],
                },
            },
        },
    },
});
```

## Development Commands

```bash
# Start dev server with HMR
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Blade Integration

```blade
<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Content -->
</body>
</html>
```

## Asset URLs

```blade
{{-- Image assets --}}
<img src="{{ Vite::asset('resources/images/logo.png') }}">

{{-- In JavaScript --}}
import logo from '@/images/logo.png';
```

## Environment Variables

```javascript
// Access in JavaScript
const apiUrl = import.meta.env.VITE_API_URL;
const appName = import.meta.env.VITE_APP_NAME;
```

```env
# .env
VITE_API_URL=http://localhost:8000
VITE_APP_NAME=ICTServe
```

## ICTServe Configuration

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: [
                'resources/views/**',
                'app/Livewire/**',
                'app/Filament/**',
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '@css': '/resources/css',
            '@images': '/resources/images',
        },
    },
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['axios'],
                    'alpine': ['alpinejs'],
                    'echo': ['laravel-echo', 'pusher-js'],
                },
            },
        },
    },
});
```

## Production Build

```bash
# Build assets
npm run build

# Verify build
ls -la public/build
```

## Hot Module Replacement

Vite automatically reloads when files change:

- Blade templates
- Livewire components
- JavaScript files
- CSS files

## Code Splitting

```javascript
// Dynamic imports for code splitting
const Dashboard = () => import('./components/Dashboard.vue');
```

## CSS Processing

Vite automatically processes:
- Tailwind CSS (via PostCSS)
- CSS imports
- CSS modules
- Sass/SCSS (if installed)

## Best Practices

1. Use `npm run dev` during development
2. Always run `npm run build` before deployment
3. Commit `public/build` to version control (optional)
4. Use environment variables for configuration
5. Leverage code splitting for large apps

## Troubleshooting

### Vite Not Found

```bash
npm install
```

### Port Already in Use

```javascript
// vite.config.js
export default defineConfig({
    server: {
        port: 5174,
    },
});
```

### Assets Not Loading

```bash
# Clear cache
npm run build
php artisan optimize:clear
```

## Windows Specific

On Windows, if HMR doesn't work:

```javascript
// vite.config.js
export default defineConfig({
    server: {
        watch: {
            usePolling: true,
        },
    },
});
```
