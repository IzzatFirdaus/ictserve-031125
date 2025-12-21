# Laravel Vite — Asset Bundling

## Overview

Laravel Vite provides fast hot module replacement (HMR) during development and optimized production builds with code splitting, tree shaking, and asset optimization.

**Version**: Laravel 12.x compatible  
**Purpose**: Frontend asset bundling and optimization

## Installation

Vite is included in Laravel by default:

```bash
npm install
```

## Configuration

### Basic Setup

`vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
```

### SPA Configuration

For SPAs (Inertia), import CSS via JavaScript:

```javascript
// resources/js/app.js
import './bootstrap';
import '../css/app.css';
```

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel(['resources/js/app.js']),
    ],
});
```

## Secure Development Server

### Auto-Detection (Herd/Valet)

Laravel automatically detects TLS certificates from Herd or Valet.

### Custom Host

```javascript
export default defineConfig({
    plugins: [
        laravel({
            detectTls: 'my-app.test',
        }),
    ],
});
```

### Manual Certificate

```javascript
import fs from 'fs';

const host = 'my-app.test';

export default defineConfig({
    server: {
        host,
        hmr: { host },
        https: {
            key: fs.readFileSync(`/path/to/${host}.key`),
            cert: fs.readFileSync(`/path/to/${host}.crt`),
        },
    },
});
```

## WSL2 Configuration

For Laravel Sail on WSL2:

```javascript
export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

If file changes aren't reflected, enable polling:

```javascript
export default defineConfig({
    server: {
        watch: {
            usePolling: true,
        },
    },
});
```

## Loading Assets

### Blade Directive

```blade
<!DOCTYPE html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

### JavaScript-Only Entry

```blade
<!DOCTYPE html>
<head>
    @vite('resources/js/app.js')
</head>
```

### Custom Build Path

```blade
<!doctype html>
<head>
    @vite('resources/js/app.js', 'vendor/courier/build')
</head>
```

## Inline Assets

Include raw asset content:

```blade
@use('Illuminate\Support\Facades\Vite')

<!doctype html>
<head>
    <style>
        {!! Vite::content('resources/css/app.css') !!}
    </style>
    <script>
        {!! Vite::content('resources/js/app.js') !!}
    </script>
</head>
```

## Running Vite

### Development Server

```bash
npm run dev
```

### Production Build

```bash
npm run build
```

## JavaScript Configuration

### Aliases

Default alias:

```javascript
{
    '@' => '/resources/js'
}
```

Custom alias:

```javascript
export default defineConfig({
    plugins: [
        laravel(['resources/ts/app.tsx']),
    ],
    resolve: {
        alias: {
            '@': '/resources/ts',
        },
    },
});
```

### Vue Integration

Install plugin:

```bash
npm install --save-dev @vitejs/plugin-vue
```

Configure:

```javascript
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

### React Integration

Install plugin:

```bash
npm install --save-dev @vitejs/plugin-react
```

Configure:

```javascript
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel(['resources/js/app.jsx']),
        react(),
    ],
});
```

Blade directives:

```blade
@viteReactRefresh
@vite('resources/js/app.jsx')
```

## Security Features

### Content Security Policy (CSP)

Generate nonce attributes:

```php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Vite;

class AddContentSecurityPolicyHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        return $next($request)->withHeaders([
            'Content-Security-Policy' => "script-src 'nonce-".Vite::cspNonce()."'",
        ]);
    }
}
```

Use in Blade:

```blade
@routes(nonce: Vite::cspNonce())
```

Custom nonce:

```php
Vite::useCspNonce($nonce);
```

### Subresource Integrity (SRI)

Install plugin:

```bash
npm install --save-dev vite-plugin-manifest-sri
```

Configure:

```javascript
import manifestSRI from 'vite-plugin-manifest-sri';

export default defineConfig({
    plugins: [
        laravel(['resources/css/app.css', 'resources/js/app.js']),
        manifestSRI(),
    ],
});
```

Custom integrity key:

```php
Vite::useIntegrityKey('custom-integrity-key');
```

Disable SRI:

```php
Vite::useIntegrityKey(false);
```

### Arbitrary Attributes

Add custom attributes:

```php
Vite::useScriptTagAttributes([
    'data-turbo-track' => 'reload',
    'async' => true,
    'integrity' => false,
]);

Vite::useStyleTagAttributes([
    'data-turbo-track' => 'reload',
]);
```

Conditional attributes:

```php
Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $src === 'resources/js/app.js' ? 'reload' : false,
]);
```

## Advanced Configuration

### Custom Paths

```blade
<!doctype html>
<head>
    {{
        Vite::useHotFile(storage_path('vite.hot'))
            ->useBuildDirectory('bundle')
            ->useManifestFilename('assets.json')
            ->withEntryPoints(['resources/js/app.js'])
            ->createAssetPathsUsing(function (string $path, ?bool $secure) {
                return "https://cdn.example.com/{$path}";
            })
    }}
</head>
```

Vite config:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'storage/vite.hot',
            buildDirectory: 'bundle',
            input: ['resources/js/app.js'],
        }),
    ],
    build: {
        manifest: 'assets.json',
    },
});
```

## CORS Configuration

### Allowed Origins (Default)

- `::1`
- `127.0.0.1`
- `localhost`
- `*.test`
- `*.localhost`
- `APP_URL` from `.env`

### Custom Origins

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
    ],
    server: {
        cors: {
            origin: [
                'https://backend.laravel',
                'http://admin.laravel:8566',
            ],
        },
    },
});
```

### Regex Patterns

```javascript
export default defineConfig({
    server: {
        cors: {
            origin: [
                /^https?:\/\/.*\.laravel(:\d+)?$/,
            ],
        },
    },
});
```

## Plugin URL Transformation

For plugins like `vite-imagetools`:

```javascript
import { imagetools } from 'vite-imagetools';

export default defineConfig({
    plugins: [
        laravel({
            transformOnServe: (code, devServerUrl) => 
                code.replaceAll('/@imagetools', devServerUrl+'/@imagetools'),
        }),
        imagetools(),
    ],
});
```

Result:

```html
<!-- Before -->
<img src="/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">

<!-- After -->
<img src="http://[::1]:5173/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">
```

## ICTServe Configuration

Recommended setup:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import manifestSRI from 'vite-plugin-manifest-sri';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        manifestSRI(),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

## Best Practices

1. **Development**: Use `npm run dev` for HMR
2. **Production**: Always run `npm run build` before deployment
3. **Security**: Enable CSP nonce and SRI in production
4. **Performance**: Use code splitting for large applications
5. **Assets**: Import CSS via JavaScript for SPAs

## Troubleshooting

### Assets Not Loading

Check:

1. Vite dev server is running: `npm run dev`
2. `@vite` directive is in `<head>`
3. Entry points match `vite.config.js`

### HMR Not Working

Solutions:

1. Check `server.hmr.host` configuration
2. Enable polling for WSL2: `server.watch.usePolling`
3. Verify firewall allows port 5173

### Build Errors

Solutions:

1. Clear cache: `rm -rf node_modules/.vite`
2. Reinstall dependencies: `npm install`
3. Check for syntax errors in entry files

## References

- Official Documentation: <https://laravel.com/docs/12.x/vite>
- Vite Documentation: <https://vitejs.dev>
- GitHub Repository: <https://github.com/laravel/vite-plugin>
