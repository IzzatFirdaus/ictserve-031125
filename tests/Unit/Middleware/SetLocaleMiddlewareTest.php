<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\UnitTestCase;

/**
 * SetLocaleMiddleware Unit Tests
 *
 * Tests the locale detection priority chain and validation logic.
 *
 * Test Coverage:
 * - Session locale has highest priority
 * - Cookie locale takes priority over Accept-Language header
 * - Accept-Language header parsing
 * - Config fallback when no preference set
 * - Invalid locale rejection
 * - Locale application to App facade
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 20.1, 20.2, 20.4, 14.3, 15.1, 15.2
 * Standards: D03-FR-020, D04 §7.3, D10 §5.2, D11 §6.1
 */
class SetLocaleMiddlewareTest extends UnitTestCase
{
    protected SetLocaleMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetLocaleMiddleware;

        // Don't refresh database for middleware tests
        $this->refreshApplication();
    }

    /**
     * Test that session locale has highest priority.
     */
    #[Test]
    public function session_locale_has_highest_priority(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');
        $request->cookies->set('locale', 'en'); // Cookie should be ignored
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9'); // Header should be ignored

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that cookie locale takes priority over Accept-Language header.
     */
    #[Test]
    public function cookie_locale_takes_priority_over_accept_language(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->cookies->set('locale', 'ms');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9'); // Header should be ignored

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that Accept-Language header is parsed correctly.
     */
    #[Test]
    public function accept_language_header_parsed_correctly(): void
    {
        // Arrange - Malay preference
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->headers->set('Accept-Language', 'ms-MY,ms;q=0.9,en;q=0.8');

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert
        $this->assertEquals('ms', App::currentLocale());

        // Arrange - English preference (should fallback to BM in v3.6.0)
        $request2 = Request::create('/', 'GET');
        $request2->setLaravelSession($this->app['session']->driver());
        $request2->headers->set('Accept-Language', 'en-US,en;q=0.9');

        // Act
        $this->middleware->handle($request2, fn ($req) => response('OK'));

        // Assert - v3.6.0 is BM-only, should default to 'ms'
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test fallback to config default when no preference is set.
     *
     * @traceability Requirement 1.1 - BM-only locale in v3.6.0
     */
    #[Test]
    public function fallback_to_config_default(): void
    {
        // Arrange - v3.6.0 defaults to BM
        Config::set('app.locale', 'ms');
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        // No session, cookie, or Accept-Language header

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert - v3.6.0 is BM-only
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that invalid locale is rejected.
     *
     * @traceability Requirement 1.1 - BM-only locale in v3.6.0
     */
    #[Test]
    public function invalid_locale_rejected(): void
    {
        // Arrange - v3.6.0 is BM-only
        Config::set('app.locale', 'ms');
        Config::set('app.supported_locales', ['ms']);
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'fr'); // Invalid locale

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert - Should fallback to BM default
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that locale is applied to App facade.
     */
    #[Test]
    public function locale_applied_to_app_facade(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert
        $this->assertEquals('ms', App::currentLocale());
        $this->assertEquals('ms', app()->getLocale());
    }

    /**
     * Test that supported locales configuration is respected.
     *
     * @traceability Requirement 1.1 - BM-only locale in v3.6.0
     */
    #[Test]
    public function supported_locales_configuration_respected(): void
    {
        // Arrange - v3.6.0 supports only BM
        Config::set('app.supported_locales', ['ms']);
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test BM-only locale enforcement in v3.6.0
     *
     * @traceability Requirement 1.1 - BM-only locale in v3.6.0
     */
    #[Test]
    public function bm_only_locale_enforcement(): void
    {
        // Arrange - Try to set English locale
        Config::set('app.locale', 'ms');
        Config::set('app.supported_locales', ['ms']);
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'en'); // Should be rejected

        // Act
        $this->middleware->handle($request, fn ($req) => response('OK'));

        // Assert - Should fallback to BM
        $this->assertEquals('ms', App::currentLocale());
    }
}
