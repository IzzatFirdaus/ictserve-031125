# Component Testing Framework

**Date**: 2025-11-03  
**Framework**: PHPUnit 11, Laravel Dusk, axe DevTools  
**Standards**: WCAG 2.2 Level AA, Core Web Vitals  
**Coverage Target**: 80% overall, 95% critical paths

## Testing Strategy

### Testing Pyramid

```
                    /\
                   /  \
                  / E2E \          End-to-End Tests (10%)
                 /______\          - Laravel Dusk
                /        \         - Full user workflows
               / Integration\
              /______________\     Integration Tests (20%)
             /                \    - Livewire component tests
            /   Unit Tests     \   - Component interaction tests
           /____________________\
                                   Unit Tests (70%)
                                   - Component rendering
                                   - Props validation
                                   - Accessibility checks
```

## 1. Automated Accessibility Testing

### axe DevTools Integration

#### Installation

```bash
npm install --save-dev @axe-core/cli
```

#### Configuration

```json
// package.json
{
    "scripts": {
        "test:a11y": "axe http://localhost:8000 --tags wcag2aa,wcag22aa --exit",
        "test:a11y:ci": "axe http://localhost:8000 --tags wcag2aa,wcag22aa --exit --save axe-results.json"
    }
}
```

#### Usage

```bash
# Test single page
npm run test:a11y

# Test multiple pages
axe http://localhost:8000 http://localhost:8000/services --tags wcag2aa,wcag22aa
```

### Lighthouse CI Integration

#### Installation

```bash
npm install --save-dev @lhci/cli
```

#### Configuration

```javascript
// lighthouserc.js
module.exports = {
    ci: {
        collect: {
            url: [
                "http://localhost:8000/",
                "http://localhost:8000/services",
                "http://localhost:8000/accessibility",
            ],
            numberOfRuns: 3,
        },
        assert: {
            assertions: {
                "categories:performance": ["error", { minScore: 0.9 }],
                "categories:accessibility": ["error", { minScore: 1.0 }],
                "categories:best-practices": ["error", { minScore: 1.0 }],
                "categories:seo": ["error", { minScore: 1.0 }],
            },
        },
        upload: {
            target: "temporary-public-storage",
        },
    },
};
```

#### Usage

```bash
# Run Lighthouse CI
npx lhci autorun
```

## 2. Component Unit Testing

### Blade Component Tests

#### Test Structure

```php
<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use Illuminate\View\Component;

class ButtonComponentTest extends TestCase
{
    /** @test */
    public function it_renders_primary_button_with_correct_classes()
    {
        $view = $this->blade(
            '<x-ui.button type="submit">Submit</x-ui.button>'
        );

        $view->assertSee('Submit');
        $view->assertSee('type="submit"', false);
        $view->assertSee('bg-motac-blue', false);
        $view->assertSee('min-h-[44px]', false);
        $view->assertSee('min-w-[44px]', false);
    }

    /** @test */
    public function it_applies_focus_indicators()
    {
        $view = $this->blade(
            '<x-ui.button>Click Me</x-ui.button>'
        );

        $view->assertSee('focus:ring-4', false);
        $view->assertSee('focus:ring-motac-blue', false);
        $view->assertSee('focus:ring-offset-2', false);
    }

    /** @test */
    public function it_handles_disabled_state()
    {
        $view = $this->blade(
            '<x-ui.button :disabled="true">Disabled</x-ui.button>'
        );

        $view->assertSee('disabled', false);
        $view->assertSee('opacity-50', false);
        $view->assertSee('cursor-not-allowed', false);
    }

    /** @test */
    public function it_meets_touch_target_requirements()
    {
        $view = $this->blade(
            '<x-ui.button>Touch Target</x-ui.button>'
        );

        // Verify WCAG 2.5.8 compliance (44×44px minimum)
        $view->assertSee('min-h-[44px]', false);
        $view->assertSee('min-w-[44px]', false);
    }
}
```

### Form Component Tests

```php
<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class InputComponentTest extends TestCase
{
    /** @test */
    public function it_renders_input_with_label()
    {
        $view = $this->blade(
            '<x-form.input name="email" label="Email Address" />'
        );

        $view->assertSee('Email Address');
        $view->assertSee('name="email"', false);
        $view->assertSee('id="email"', false);
        $view->assertSee('for="email"', false);
    }

    /** @test */
    public function it_displays_validation_errors()
    {
        $this->withViewErrors(['email' => 'The email field is required.']);

        $view = $this->blade(
            '<x-form.input name="email" label="Email Address" />'
        );

        $view->assertSee('The email field is required.');
        $view->assertSee('aria-invalid="true"', false);
        $view->assertSee('aria-describedby="email-error"', false);
    }

    /** @test */
    public function it_meets_minimum_height_requirement()
    {
        $view = $this->blade(
            '<x-form.input name="test" label="Test" />'
        );

        $view->assertSee('min-h-[44px]', false);
    }
}
```

## 3. Livewire Component Testing

### Livewire Component Tests

```php
<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\GuestTicketForm;

class GuestTicketFormTest extends TestCase
{
    /** @test */
    public function it_renders_guest_ticket_form()
    {
        Livewire::test(GuestTicketForm::class)
            ->assertStatus(200)
            ->assertSee('Submit Helpdesk Ticket')
            ->assertSee('Full Name')
            ->assertSee('Email Address');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::test(GuestTicketForm::class)
            ->set('form.name', '')
            ->set('form.email', '')
            ->call('submitTicket')
            ->assertHasErrors(['form.name', 'form.email']);
    }

    /** @test */
    public function it_submits_ticket_successfully()
    {
        Livewire::test(GuestTicketForm::class)
            ->set('form.name', 'John Doe')
            ->set('form.email', 'john@motac.gov.my')
            ->set('form.phone', '+60123456789')
            ->set('form.category_id', 1)
            ->set('form.subject', 'Test Issue')
            ->set('form.description', 'Test description with minimum 10 characters')
            ->call('submitTicket')
            ->assertHasNoErrors()
            ->assertDispatched('ticket-submitted');
    }

    /** @test */
    public function it_implements_real_time_validation()
    {
        Livewire::test(GuestTicketForm::class)
            ->set('form.email', 'invalid-email')
            ->assertHasErrors(['form.email' => 'email']);
    }
}
```

## 4. End-to-End Testing with Laravel Dusk

### Dusk Test Examples

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class GuestTicketSubmissionTest extends DuskTestCase
{
    /** @test */
    public function guest_can_submit_helpdesk_ticket()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/helpdesk/create')
                    ->assertSee('Submit Helpdesk Ticket')
                    ->type('name', 'John Doe')
                    ->type('email', 'john@motac.gov.my')
                    ->type('phone', '+60123456789')
                    ->select('category_id', '1')
                    ->type('subject', 'Network Issue')
                    ->type('description', 'Unable to connect to network')
                    ->press('Submit Ticket')
                    ->waitForText('Ticket submitted successfully')
                    ->assertSee('HD2025');
        });
    }

    /** @test */
    public function form_has_proper_focus_indicators()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/helpdesk/create')
                    ->keys('input[name="name"]', '{tab}')
                    ->assertFocused('input[name="email"]')
                    ->assertPresent('input[name="email"]:focus');
        });
    }

    /** @test */
    public function form_is_keyboard_accessible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/helpdesk/create')
                    ->keys('body', '{tab}') // Skip link
                    ->keys('body', '{tab}') // Name field
                    ->type('name', 'John Doe')
                    ->keys('body', '{tab}') // Email field
                    ->type('email', 'john@motac.gov.my')
                    ->keys('body', '{tab}') // Phone field
                    ->type('phone', '+60123456789')
                    ->keys('body', '{tab}') // Category select
                    ->keys('body', '{down}') // Select first option
                    ->keys('body', '{tab}') // Subject field
                    ->type('subject', 'Test Issue')
                    ->keys('body', '{tab}') // Description field
                    ->type('description', 'Test description')
                    ->keys('body', '{tab}') // Submit button
                    ->keys('body', '{enter}') // Submit form
                    ->waitForText('Ticket submitted successfully');
        });
    }
}
```

### Accessibility Testing with Dusk

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AccessibilityTest extends DuskTestCase
{
    /** @test */
    public function page_has_proper_aria_landmarks()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('[role="banner"]')
                    ->assertPresent('[role="navigation"]')
                    ->assertPresent('[role="main"]')
                    ->assertPresent('[role="contentinfo"]');
        });
    }

    /** @test */
    public function skip_links_are_functional()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->keys('body', '{tab}') // Focus skip link
                    ->keys('body', '{enter}') // Activate skip link
                    ->assertFocused('#main-content');
        });
    }

    /** @test */
    public function all_images_have_alt_text()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->script('
                        const images = document.querySelectorAll("img");
                        const missingAlt = Array.from(images).filter(img => !img.alt);
                        return missingAlt.length;
                    ');

            $this->assertEquals(0, $browser->driver->executeScript('return arguments[0]', []));
        });
    }

    /** @test */
    public function touch_targets_meet_minimum_size()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->script('
                        const buttons = document.querySelectorAll("button, a");
                        const tooSmall = Array.from(buttons).filter(el => {
                            const rect = el.getBoundingClientRect();
                            return rect.width < 44 || rect.height < 44;
                        });
                        return tooSmall.length;
                    ');

            $this->assertEquals(0, $browser->driver->executeScript('return arguments[0]', []));
        });
    }
}
```

## 5. Visual Regression Testing

### Percy Integration (Optional)

```bash
npm install --save-dev @percy/cli @percy/puppeteer
```

```javascript
// tests/visual/homepage.test.js
const puppeteer = require("puppeteer");
const percySnapshot = require("@percy/puppeteer");

describe("Homepage Visual Tests", () => {
    let browser;
    let page;

    beforeAll(async () => {
        browser = await puppeteer.launch();
        page = await browser.newPage();
    });

    afterAll(async () => {
        await browser.close();
    });

    it("captures homepage snapshot", async () => {
        await page.goto("http://localhost:8000");
        await percySnapshot(page, "Homepage");
    });

    it("captures mobile viewport", async () => {
        await page.setViewport({ width: 375, height: 667 });
        await page.goto("http://localhost:8000");
        await percySnapshot(page, "Homepage - Mobile");
    });
});
```

## 6. Performance Testing

### Core Web Vitals Testing

```javascript
// tests/performance/core-web-vitals.test.js
const puppeteer = require("puppeteer");

describe("Core Web Vitals", () => {
    let browser;
    let page;

    beforeAll(async () => {
        browser = await puppeteer.launch();
        page = await browser.newPage();
    });

    afterAll(async () => {
        await browser.close();
    });

    it("measures LCP (Largest Contentful Paint)", async () => {
        await page.goto("http://localhost:8000", { waitUntil: "networkidle0" });

        const lcp = await page.evaluate(() => {
            return new Promise((resolve) => {
                new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    resolve(lastEntry.renderTime || lastEntry.loadTime);
                }).observe({ entryTypes: ["largest-contentful-paint"] });
            });
        });

        expect(lcp).toBeLessThan(2500); // LCP < 2.5s
    });

    it("measures CLS (Cumulative Layout Shift)", async () => {
        await page.goto("http://localhost:8000", { waitUntil: "networkidle0" });

        const cls = await page.evaluate(() => {
            return new Promise((resolve) => {
                let clsValue = 0;
                new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            clsValue += entry.value;
                        }
                    }
                    resolve(clsValue);
                }).observe({ entryTypes: ["layout-shift"] });

                setTimeout(() => resolve(clsValue), 5000);
            });
        });

        expect(cls).toBeLessThan(0.1); // CLS < 0.1
    });
});
```

## 7. Testing Checklist

### Component Testing Checklist

- [ ] **Rendering Tests**

  - [ ] Component renders without errors
  - [ ] Props are applied correctly
  - [ ] Slots are rendered properly
  - [ ] Conditional rendering works

- [ ] **Accessibility Tests**

  - [ ] ARIA attributes present and correct
  - [ ] Keyboard navigation works
  - [ ] Focus indicators visible (4px ring, 2px offset, 6.8:1 contrast)
  - [ ] Touch targets meet 44×44px minimum
  - [ ] Screen reader compatibility verified

- [ ] **Responsive Tests**

  - [ ] Mobile viewport (320px-414px)
  - [ ] Tablet viewport (768px-1024px)
  - [ ] Desktop viewport (1280px-1920px)
  - [ ] No horizontal scrolling
  - [ ] Touch targets maintained across viewports

- [ ] **Performance Tests**

  - [ ] LCP < 2.5 seconds
  - [ ] FID < 100 milliseconds
  - [ ] CLS < 0.1
  - [ ] TTFB < 600 milliseconds

- [ ] **Visual Tests**
  - [ ] Compliant color palette used
  - [ ] MOTAC branding consistent
  - [ ] Typography scales properly
  - [ ] Spacing is consistent

## 8. Continuous Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/frontend-tests.yml
name: Frontend Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest

        steps:
            - uses: actions/checkout@v3

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.2"

            - name: Install Dependencies
              run: |
                  composer install
                  npm install

            - name: Run PHPUnit Tests
              run: vendor/bin/phpunit

            - name: Run Accessibility Tests
              run: npm run test:a11y

            - name: Run Lighthouse CI
              run: npx lhci autorun

            - name: Upload Test Results
              uses: actions/upload-artifact@v3
              with:
                  name: test-results
                  path: |
                      axe-results.json
                      .lighthouseci/
```

## 9. Testing Commands

```bash
# Unit Tests
vendor/bin/phpunit tests/Unit/Components

# Feature Tests
vendor/bin/phpunit tests/Feature/Livewire

# Browser Tests
php artisan dusk

# Accessibility Tests
npm run test:a11y

# Lighthouse CI
npx lhci autorun

# All Tests
composer test
```

## 10. Coverage Reports

```bash
# Generate coverage report
vendor/bin/phpunit --coverage-html coverage

# View coverage report
open coverage/index.html
```

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-03  
**Author**: Frontend Engineering Team  
**Status**: ✅ READY - Comprehensive testing framework established
