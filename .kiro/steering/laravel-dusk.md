---
inclusion:
  fileMatchPattern:
    - 'tests/Browser/**/*.php'
    - 'tests/DuskTestCase.php'
    - 'phpunit.dusk.xml'
  applyWhen:
    - Browser testing with Laravel Dusk
    - E2E testing with ChromeDriver
    - Automated browser testing
---

# Laravel Dusk Browser Testing Guidelines

Laravel Dusk provides expressive browser automation and testing. Use for E2E tests requiring full browser interaction.

**Note**: For new projects, prefer Playwright over Dusk for better performance.

## Key Commands

```bash
php artisan dusk                    # Run all Dusk tests
php artisan dusk:make LoginTest     # Generate test
php artisan dusk:chrome-driver      # Install ChromeDriver
php artisan dusk:fails              # Re-run failed tests
```

## Test Structure

```php
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function test_basic_example(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel');
        });
    }
}
```

## Database Management

**CRITICAL**: Never use `RefreshDatabase` trait with Dusk. Use `DatabaseMigrations` or `DatabaseTruncation` instead.

```php
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
}
```

## Common Patterns

```php
// Authentication
$browser->loginAs(User::find(1));

// Waiting
$browser->waitFor('.selector');
$browser->waitForText('Loading Complete');

// Assertions
$browser->assertSee('Welcome');
$browser->assertInputValue('email', 'test@example.com');
$browser->assertChecked('terms');
```

## ICTServe Specific

For ICTServe, prefer Playwright tests over Dusk for:
- Visual regression testing (Percy integration)
- Accessibility testing (Axe-core)
- Better performance and reliability

Use Dusk only for legacy browser tests or specific ChromeDriver requirements.
