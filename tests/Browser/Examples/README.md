# Laravel Dusk Percy Visual Testing Examples

This directory contains example tests demonstrating Percy visual testing integration with Laravel Dusk for ICTServe v3.6.1.

## Overview

Laravel Dusk provides browser automation testing capabilities, and when combined with Percy, enables visual regression testing. These examples serve as a **redundancy layer** after the primary Playwright visual testing integration.

> **Note**: Primary visual testing is performed through Playwright. Dusk + Percy serves as a backup testing approach for scenarios where Playwright may not be suitable.

## Prerequisites

### 1. Install Laravel Dusk

```bash
composer require laravel/dusk --dev
php artisan dusk:install
```

### 2. Install Percy CLI and Dependencies

```bash
npm install -g @percy/cli
npm install @percy/selenium-webdriver
```

### 3. Set Percy Token

```bash
# Windows PowerShell
$env:PERCY_TOKEN = "your_percy_token_here"

# Windows CMD
set PERCY_TOKEN=your_percy_token_here

# Linux/macOS
export PERCY_TOKEN=your_percy_token_here
```

## Running Examples

### Run All Dusk Tests with Percy

```bash
npx percy exec -- php artisan dusk tests/Browser/Examples/PercyExampleTest.php
```

### Run Specific Test

```bash
npx percy exec -- php artisan dusk --filter=basic_homepage_snapshot
```

### Run Without Percy (Functional Tests Only)

```bash
php artisan dusk tests/Browser/Examples/PercyExampleTest.php
```

## Example Categories

### 1. Basic Percy Snapshot

- Simple page snapshot
- Waiting for content stabilization
- Descriptive naming conventions

### 2. Responsive Visual Testing

- Mobile viewport (375px)
- Tablet viewport (768px)
- Desktop viewport (1280px)
- Full responsive coverage

### 3. Form State Testing

- Empty form state
- Filled form state
- Validation error state

### 4. Hybrid Architecture Testing

- Guest user workflows
- Authenticated user workflows
- Admin panel workflows

### 5. Accessibility Visual Testing

- WCAG 2.2 AA compliance
- Focus indicator validation
- Keyboard navigation testing

### 6. Bahasa Melayu Interface Testing

- Homepage in Bahasa Melayu
- Form labels in Bahasa Melayu

### 7. Error Handling

- Graceful degradation
- Conditional Percy execution

### 8. Livewire Component Testing

- Initial component state
- Post-interaction state

### 9. Modal and Dialog Testing

- Modal dialog snapshots

### 10. Performance Considerations

- Quick development snapshots
- Comprehensive CI snapshots

## Using the Percy Dusk Trait

The `PercyDuskTrait` provides reusable methods for Percy integration:

```php
<?php

namespace Tests\Browser;

use Tests\Browser\Traits\PercyDuskTrait;
use Tests\DuskTestCase;

class MyVisualTest extends DuskTestCase
{
    use PercyDuskTrait;

    public function testHomepage(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            
            // Wait for content to stabilize
            $this->waitForStableContent($browser);
            
            // Take Percy snapshot
            $this->takePercySnapshot($browser, 'Homepage', [
                'widths' => [375, 768, 1280],
                'userType' => 'guest',
            ]);
        });
    }
}
```

### Available Trait Methods

| Method | Description |
|--------|-------------|
| `takePercySnapshot($browser, $name, $options)` | Take a Percy visual snapshot |
| `takeResponsivePercySnapshots($browser, $name, $options)` | Take snapshots at multiple viewports |
| `takeAccessibilityPercySnapshot($browser, $name, $options)` | Take accessibility-focused snapshot |
| `waitForLivewireStable($browser, $timeout)` | Wait for Livewire to stabilize |
| `waitForStableContent($browser, $timeout)` | Wait for all content to stabilize |
| `setResponsiveViewport($browser, $size)` | Set viewport to predefined size |
| `takePercySnapshotHideDynamic($browser, $name, $options)` | Snapshot with dynamic content hidden |
| `isPercyEnabled()` | Check if Percy is enabled |
| `getPercyBuildInfo()` | Get Percy build information |

## Configuration Options

### Snapshot Options

```php
$this->takePercySnapshot($browser, 'My Snapshot', [
    // Viewport widths to capture
    'widths' => [375, 768, 1280],
    
    // Minimum height for snapshot
    'minHeight' => 1024,
    
    // Enable JavaScript during capture
    'enableJavaScript' => true,
    
    // Custom CSS to inject
    'percyCSS' => '.dynamic { display: none; }',
    
    // Scope snapshot to specific element
    'scope' => '#main-content',
    
    // User type for Hybrid Architecture
    'userType' => 'guest', // 'guest', 'authenticated', 'admin'
]);
```

### Responsive Viewports

| Size | Width | Height |
|------|-------|--------|
| mobile | 375px | 667px |
| tablet | 768px | 1024px |
| desktop | 1280px | 800px |
| large | 1920px | 1080px |

## Best Practices

1. **Always wait for content stabilization** before taking snapshots
2. **Use descriptive snapshot names** that include context
3. **Specify userType** for proper Hybrid Architecture support
4. **Hide dynamic content** using custom Percy CSS
5. **Test all critical breakpoints** for responsive layouts
6. **Use graceful degradation** - tests should pass even without Percy

## Troubleshooting

### Percy Token Not Set

```
Percy snapshot 'X' skipped - PERCY_TOKEN not set
```

**Solution**: Set the `PERCY_TOKEN` environment variable.

### Livewire Components Not Loading

```php
// Wait for Livewire to initialize
$browser->waitFor('[wire\\:id]', 10);
$this->waitForLivewireStable($browser);
```

### Flaky Visual Tests

```php
// Increase wait times
$this->waitForStableContent($browser, 15);
$browser->pause(1000);
```

### Dynamic Content Causing Differences

```php
// Use custom CSS to hide dynamic content
$this->takePercySnapshotHideDynamic($browser, 'My Snapshot');
```

## File Structure

```
tests/Browser/
├── Examples/
│   ├── PercyExampleTest.php    # Comprehensive Percy examples
│   └── README.md               # This file
├── Traits/
│   └── PercyDuskTrait.php      # Reusable Percy trait
└── DuskTestCase.php            # Base Dusk test case
```

## Related Documentation

- [Percy Examples and Configuration Guide](../../../docs/percy/PERCY_EXAMPLES_AND_CONFIGURATION.md)
- [Playwright Percy Examples](../../e2e/examples/README.md)
- [Percy Integration Guide](../../e2e/PERCY_INTEGRATION_GUIDE.md)

## Version Information

- **ICTServe Version**: 3.6.1
- **Laravel**: 12.43.1
- **Livewire**: 3.7.3
- **Filament**: 4.3.1
- **Laravel Dusk**: (when installed)
- **Percy CLI**: 1.31.6

---

*Document created: December 26, 2025*
*Author: Pasukan Pembangunan BPM MOTAC*
