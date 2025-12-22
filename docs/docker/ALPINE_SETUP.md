# Alpine.js Setup in Docker

Complete guide for Alpine.js integration in ICTServe Docker environment.

## Overview

ICTServe uses Alpine.js for frontend interactivity, manually bundled from Livewire 3. This approach provides:

- Full control over Alpine.js initialization
- Ability to register custom Alpine components and plugins
- Proper integration with Livewire's reactive system
- Consistent behavior across development and production

## Architecture

### Manual Bundling Approach

Instead of using Livewire's automatic Alpine.js injection, ICTServe manually bundles Alpine.js:

```javascript
// resources/js/bootstrap.js
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Make Alpine available globally
window.Alpine = Alpine;

// Register custom Alpine components here
// (imported from alpine-components.js and alpine-patterns.js)

// Start Livewire (which also starts Alpine)
Livewire.start();
```

### Layout Configuration

The layout uses `@livewireScriptConfig` instead of `@livewireScripts`:

```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}
    
    {{-- Manual bundling configuration --}}
    @livewireScriptConfig
</body>
```

## Docker Setup

### Prerequisites

1. **Node.js in Container**: Dockerfile includes Node.js 18.x
2. **npm Dependencies**: Must be installed in container
3. **Dev Dependencies**: Required for service providers (Horizon, Breeze)

### Initial Setup

```bash
# 1. Install npm dependencies
docker compose exec app npm install

# 2. Install dev dependencies
docker compose exec app composer install --dev

# 3. Build assets with Alpine.js
docker compose exec app npm run build

# 4. Clear and cache configuration
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# 5. Restart containers
docker compose restart app nginx
```

### Verification

**Check npm installation**:

```bash
docker compose exec app npm --version
# Expected: 9.6.6 or higher
```

**Check Alpine.js in browser**:

1. Navigate to <http://localhost:8000>
2. Open browser dev tools (F12)
3. In console, type: `window.Alpine`
4. Expected: Alpine.js object with methods

**Check built assets**:

```bash
docker compose exec app ls -la public/build
# Expected: manifest.json, css/, js/ directories
```

## Custom Alpine Components

ICTServe includes custom Alpine components for:

- **Searchable Select**: Virtual scrolling for large option lists
- **Optimistic UI**: Immediate feedback with server rollback
- **Modal**: Focus management and keyboard navigation
- **Dropdown**: ARIA-compliant keyboard navigation
- **Tabs**: Accessible tab interface
- **Keyboard Shortcuts**: Global hotkey management

Components are defined in:

- `resources/js/alpine-components.js` - ICTServe-specific components
- `resources/js/alpine-patterns.js` - Reusable patterns

## Development Workflow

### Watch Mode (Hot Reload)

```bash
# Terminal 1: Start Vite dev server
docker compose exec app npm run dev

# Terminal 2: View app logs
docker compose logs -f app

# Access: http://localhost:8000
# Changes auto-reload
```

### Production Build

```bash
# Build optimized assets
docker compose exec app npm run build

# Cache Laravel config
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Restart app
docker compose restart app
```

## Troubleshooting

### Alpine is not defined

**Symptom**: `Alpine is not defined` error in browser console

**Cause**: Assets not built or npm dependencies missing

**Solution**:

```bash
docker compose exec app npm install
docker compose exec app npm run build
docker compose restart app nginx
```

### Alpine components not working

**Symptom**: Alpine directives (x-data, x-show, etc.) not functioning

**Cause**: Livewire not started or Alpine not initialized

**Solution**:

```bash
# Check browser console for errors
# Verify Livewire.start() is called in bootstrap.js
# Rebuild assets
docker compose exec app npm run build
docker compose restart app
```

### Service provider errors

**Symptom**: `Class "Laravel\Horizon\HorizonServiceProvider" not found`

**Cause**: Dev dependencies not installed

**Solution**:

```bash
docker compose exec app composer install --dev
docker compose exec app php artisan config:clear
docker compose restart app
```

### Assets not updating

**Symptom**: Changes to JavaScript/CSS not reflected in browser

**Cause**: Browser cache or Laravel view cache

**Solution**:

```bash
# Clear Laravel caches
docker compose exec app php artisan view:clear
docker compose exec app php artisan optimize:clear

# Rebuild assets
docker compose exec app npm run build

# Hard refresh browser (Ctrl+Shift+R or Ctrl+F5)
```

## Best Practices

### 1. Always Install Dependencies

After pulling changes or rebuilding containers:

```bash
docker compose exec app npm install
docker compose exec app composer install --dev
docker compose exec app npm run build
```

### 2. Use Watch Mode for Development

For active development with hot reload:

```bash
docker compose exec app npm run dev
```

### 3. Build for Production

Before deploying or testing production behavior:

```bash
docker compose exec app npm run build
docker compose exec app php artisan config:cache
```

### 4. Clear Caches After Changes

After modifying JavaScript or configuration:

```bash
docker compose exec app php artisan optimize:clear
docker compose restart app
```

### 5. Test in Browser

Always verify Alpine.js is working:

- Open browser dev tools
- Check for JavaScript errors
- Test `window.Alpine` in console
- Verify Alpine directives are functioning

## Integration with Livewire

### Accessing Livewire from Alpine

```blade
<div x-data="{ count: $wire.entangle('count') }">
    <button @click="count++">Increment</button>
    <span x-text="count"></span>
</div>
```

### Dispatching Livewire Events from Alpine

```blade
<button @click="$dispatch('refresh-component')">
    Refresh
</button>
```

### Using Alpine in Livewire Components

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" wire:click="handleClick">
        Content
    </div>
</div>
```

## Performance Considerations

### Asset Optimization

Vite configuration includes:

- **Code splitting**: Separate chunks for vendor and app code
- **Tree shaking**: Remove unused Alpine.js features
- **Minification**: Terser for JavaScript, Lightning CSS for styles
- **Caching**: Content-based hashing for cache busting

### Alpine.js Best Practices

1. **Use x-cloak**: Prevent flash of unstyled content
2. **Lazy load components**: Use Alpine.lazy for heavy components
3. **Minimize watchers**: Use x-effect sparingly
4. **Debounce inputs**: Use .debounce modifier for search inputs
5. **Cache DOM queries**: Store element references in Alpine data

## Related Documentation

- [SETUP.md](SETUP.md) - Complete Docker setup guide
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Common issues and solutions
- [Laravel Livewire Docs](https://livewire.laravel.com/docs/alpine) - Official Alpine integration
- [Alpine.js Docs](https://alpinejs.dev) - Alpine.js documentation

## Version Information

- **Alpine.js**: Bundled with Livewire 3.7.3
- **Livewire**: 3.7.3
- **Node.js**: 18.20.1 (in Docker container)
- **npm**: 9.6.6
- **Vite**: 6.4.1

## Support

For issues related to Alpine.js in Docker:

1. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
2. Verify npm dependencies are installed
3. Check browser console for errors
4. Review Vite build output for warnings
5. Test with `window.Alpine` in browser console
