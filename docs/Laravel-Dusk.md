# Laravel Dusk — Browser Testing & Automation

## Overview

Laravel Dusk provides an expressive, easy-to-use browser automation and testing API. By default, Dusk does not require you to install JDK or Selenium on your local computer. Instead, Dusk uses a standalone ChromeDriver installation.

> **Note**: For new projects, consider using Pest for browser testing as it offers better performance and usability compared to Laravel Dusk.

## Installation

```bash
composer require laravel/dusk --dev
```

**Important**: If you are manually registering Dusk's service provider, you should never register it in your production environment, as doing so could lead to arbitrary users being able to authenticate with your application.

### Initial Setup

```bash
php artisan dusk:install
```

This command will:

- Create a `tests/Browser` directory
- Generate an example Dusk test
- Install the Chrome Driver binary for your operating system

### Environment Configuration

Set the `APP_URL` environment variable in your `.env` file:

```env
APP_URL=http://localhost:8000
```

This value should match the URL you use to access your application in a browser.

### Laravel Sail Integration

If you are using Laravel Sail to manage your local development environment, please consult the Sail documentation on configuring and running Dusk tests.

## Managing ChromeDriver

### Installation Commands

```bash
# Install the latest version of ChromeDriver for your OS
php artisan dusk:chrome-driver

# Install a specific version of ChromeDriver
php artisan dusk:chrome-driver 86

# Install for all supported operating systems
php artisan dusk:chrome-driver --all

# Auto-detect and install matching Chrome/Chromium version
php artisan dusk:chrome-driver --detect
```

### Permissions

Ensure ChromeDriver binaries are executable:

```bash
chmod -R 0755 vendor/laravel/dusk/bin/
```

## Using Other Browsers

By default, Dusk uses Google Chrome and a standalone ChromeDriver installation. However, you may start your own Selenium server and run your tests against any browser.

### Configuration

In `tests/DuskTestCase.php`:

```php
/**
 * Prepare for Dusk test execution.
 *
 * @beforeClass
 */
public static function prepare(): void
{
    // static::startChromeDriver();
}
```

### Custom WebDriver

```php
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Create the RemoteWebDriver instance.
 */
protected function driver(): RemoteWebDriver
{
    return RemoteWebDriver::create(
        'http://localhost:4444/wd/hub',
        DesiredCapabilities::phantomjs()
    );
}
```

## Getting Started

### Generating Tests

```bash
php artisan dusk:make LoginTest
```

Generated tests are placed in the `tests/Browser` directory.

### Database Management

**Important**: Dusk tests should never use the `RefreshDatabase` trait. The `RefreshDatabase` trait leverages database transactions which will not be applicable or available across HTTP requests.

#### Using Database Migrations

```php
<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ...
}
```

**Note**: SQLite in-memory databases may not be used when executing Dusk tests. Since the browser executes within its own process, it will not be able to access the in-memory databases of other processes.

#### Using Database Truncation

```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseTruncation;

    // ...
}
```

**Customizing Truncation**:

```php
/**
 * Indicates which tables should be truncated.
 *
 * @var array
 */
protected $tablesToTruncate = ['users'];

/**
 * Indicates which tables should be excluded from truncation.
 *
 * @var array
 */
protected $exceptTables = ['users'];

/**
 * Indicates which connections should have their tables truncated.
 *
 * @var array
 */
protected $connectionsToTruncate = ['mysql'];
```

**Lifecycle Hooks**:

```php
/**
 * Perform any work that should take place before the database has started truncating.
 */
protected function beforeTruncatingDatabase(): void
{
    // ...
}

/**
 * Perform any work that should take place after the database has finished truncating.
 */
protected function afterTruncatingDatabase(): void
{
    // ...
}
```

## Running Tests

### Basic Execution

```bash
# Run all Dusk tests
php artisan dusk

# Re-run only failed tests
php artisan dusk:fails
```

### Test Filtering

```bash
# Run specific group
php artisan dusk --group=foo

# Run specific test file
php artisan dusk tests/Browser/LoginTest.php
```

### Laravel Sail

If you are using Laravel Sail, consult the Sail documentation on configuring and running Dusk tests.

## Manual ChromeDriver Management

### Starting ChromeDriver Manually

If automatic ChromeDriver startup doesn't work for your system, you can start it manually.

In `tests/DuskTestCase.php`:

```php
/**
 * Prepare for Dusk test execution.
 *
 * @beforeClass
 */
public static function prepare(): void
{
    // static::startChromeDriver();
}
```

### Custom Port Configuration

```php
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Create the RemoteWebDriver instance.
 */
protected function driver(): RemoteWebDriver
{
    return RemoteWebDriver::create(
        'http://localhost:9515',
        DesiredCapabilities::chrome()
    );
}
```

## Environment Handling

### Dusk-Specific Environment Files

Create a `.env.dusk.{environment}` file in the root of your project:

```bash
# For local environment
.env.dusk.local

# For testing environment
.env.dusk.testing
```

### Environment Backup

When running tests, Dusk will:

1. Back up your `.env` file
2. Rename your Dusk environment to `.env`
3. Restore your `.env` file after tests complete

## Browser Basics

### Creating Browsers

```php
<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function test_basic_example(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel');
        });
    }
}
```

### Multiple Browsers

```php
public function test_multiple_browsers(): void
{
    $this->browse(function (Browser $first, Browser $second) {
        $first->loginAs(User::find(1))
              ->visit('/home')
              ->waitForText('Dashboard');

        $second->loginAs(User::find(2))
               ->visit('/home')
               ->waitForText('Dashboard');
    });
}
```

## Navigation

### Basic Navigation

```php
$browser->visit('/login');
$browser->visitRoute('profile', ['id' => 1]);
$browser->back();
$browser->forward();
$browser->refresh();
```

### Authentication

```php
use App\Models\User;

$browser->loginAs(User::find(1));
$browser->loginAs(1);
$browser->logout();
```

## Interacting with Elements

### Clicking

```php
$browser->click('.selector');
$browser->clickLink('Link Text');
$browser->clickAtXPath('//button[@type="submit"]');
$browser->clickAtPoint(100, 200);
```

### Text Input

```php
$browser->type('email', 'taylor@laravel.com');
$browser->typeSlowly('mobile', '1234567890', 100);
$browser->append('notes', 'Additional text');
$browser->clear('email');
```

### Dropdowns

```php
$browser->select('size', 'Large');
$browser->selectByValue('size', 'lg');
```

### Checkboxes

```php
$browser->check('terms');
$browser->uncheck('terms');
```

### Radio Buttons

```php
$browser->radio('version', 'php8');
```

### File Uploads

```php
$browser->attach('photo', '/path/to/photo.jpg');
```

## Assertions

### Page Assertions

```php
$browser->assertTitle('Page Title');
$browser->assertTitleContains('Laravel');
$browser->assertUrlIs('http://localhost/home');
$browser->assertPathIs('/home');
$browser->assertRouteIs('home');
$browser->assertQueryStringHas('search', 'laravel');
```

### Element Assertions

```php
$browser->assertSee('Welcome');
$browser->assertDontSee('Error');
$browser->assertSeeIn('.alert', 'Success');
$browser->assertPresent('.selector');
$browser->assertMissing('.selector');
$browser->assertVisible('.selector');
$browser->assertHidden('.selector');
```

### Form Assertions

```php
$browser->assertInputValue('email', 'taylor@laravel.com');
$browser->assertInputValueIsNot('email', 'wrong@email.com');
$browser->assertChecked('terms');
$browser->assertNotChecked('terms');
$browser->assertRadioSelected('version', 'php8');
$browser->assertRadioNotSelected('version', 'php7');
$browser->assertSelected('size', 'Large');
$browser->assertNotSelected('size', 'Small');
```

## Waiting

### Basic Waiting

```php
$browser->pause(1000); // Wait 1 second

$browser->waitFor('.selector');
$browser->waitForText('Loading Complete');
$browser->waitForLink('Click Here');
$browser->waitForLocation('/dashboard');
$browser->waitForRoute('home');
```

### Custom Conditions

```php
$browser->waitUntil('App.initialized === true');
$browser->waitUntilMissing('.loading');
```

### Waiting with Callbacks

```php
$browser->waitUsing(10, 100, function () use ($browser) {
    return $browser->assertSee('Loaded');
});
```

## Pages

### Creating Page Objects

```bash
php artisan dusk:page Login
```

```php
<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Login extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@email' => 'input[name=email]',
            '@password' => 'input[name=password]',
            '@submit' => 'button[type=submit]',
        ];
    }
}
```

### Using Page Objects

```php
use Tests\Browser\Pages\Login;

$browser->visit(new Login)
        ->type('@email', 'taylor@laravel.com')
        ->type('@password', 'password')
        ->press('@submit');
```

## Components

### Creating Components

```bash
php artisan dusk:component DatePicker
```

```php
<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePicker extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '.date-picker';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@date-field' => 'input.datepicker-input',
            '@year-list' => 'div > div.datepicker-years',
            '@month-list' => 'div > div.datepicker-months',
            '@day-list' => 'div > div.datepicker-days',
        ];
    }

    /**
     * Select the given date.
     */
    public function selectDate(Browser $browser, int $year, int $month, int $day): void
    {
        $browser->click('@date-field')
                ->within('@year-list', function (Browser $browser) use ($year) {
                    $browser->click($year);
                })
                ->within('@month-list', function (Browser $browser) use ($month) {
                    $browser->click($month);
                })
                ->within('@day-list', function (Browser $browser) use ($day) {
                    $browser->click($day);
                });
    }
}
```

### Using Components

```php
use Tests\Browser\Components\DatePicker;

$browser->within(new DatePicker, function (Browser $browser) {
    $browser->selectDate(2023, 10, 15);
});
```

## Continuous Integration

### GitHub Actions

```yaml
name: Dusk Tests

on: [push, pull_request]

jobs:
  dusk:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Prepare Laravel Application
        run: |
          cp .env.ci .env
          php artisan key:generate

      - name: Run Dusk Tests
        run: php artisan dusk
```

## Best Practices

1. **Use Page Objects**: Organize selectors and actions into reusable page objects
2. **Wait Appropriately**: Use `waitFor` methods instead of fixed `pause()` calls
3. **Clean Database**: Use `DatabaseMigrations` or `DatabaseTruncation` traits
4. **Isolate Tests**: Each test should be independent and not rely on others
5. **Use Descriptive Selectors**: Prefer data attributes over CSS classes for stability

## Troubleshooting

### Common Issues

**ChromeDriver Version Mismatch**:

```bash
php artisan dusk:chrome-driver --detect
```

**Permission Errors**:

```bash
chmod -R 0755 vendor/laravel/dusk/bin/
```

**Port Conflicts**:

```php
// Change port in tests/DuskTestCase.php
protected function driver(): RemoteWebDriver
{
    return RemoteWebDriver::create(
        'http://localhost:9516', // Different port
        DesiredCapabilities::chrome()
    );
}
```

## References

- Official Documentation: <https://laravel.com/docs/12.x/dusk>
- GitHub Repository: <https://github.com/laravel/dusk>
