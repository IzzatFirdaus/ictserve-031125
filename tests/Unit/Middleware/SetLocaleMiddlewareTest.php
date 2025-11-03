<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
class SetLocaleMiddlewareTest extends TestCase
{
    protected SetLocaleMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetLocaleMiddleware;
    }

    /**
     * Test that session locale has highest priority.
     */
    #[Test]
    public function test_session_locale_has_highest_priority(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');
        $request->cookies->set('locale', 'en'); // Cookie should be ignored
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9'); // Header should be ignored

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that cookie locale takes priority over Accept-Language header.
     */
    #[Test]
    public function test_cookie_locale_takes_priority_over_accept_language(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->cookies->set('locale', 'ms');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9'); // Header should be ignored

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }

    /**
     * Test that Accept-Language header is parsed correctly.
     */
    #[Test]
    public function test_accept_language_header_parsed_correctly(): void
    {
        // Arrange - Malay preference
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->headers->set('Accept-Language', 'ms-MY,ms;q=0.9,en;q=0.8');

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('ms', App::currentLocale());

        // Arrange - English preference
        $request2 = Request::create('/', 'GET');
        $request2->setLaravelSession($this->app['session']->driver());
        $request2->headers->set('Accept-Language', 'en-US,en;q=0.9');

        // Act
        $this->middleware->handle($request2, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('en', App::currentLocale());
    }

    /**
     * Test fallback to config default when no preference is set.
     */
    #[Test]
    public function test_fallback_to_config_default(): void
    {
        // Arrange
        Config::set('app.locale', 'en');
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        // No session, cookie, or Accept-Language header

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('en', App::currentLocale());
    }

    /**
     * Test that invalid locale is rejected.
     */
    #[Test]
    public function test_invalid_locale_rejected(): void
    {
        // Arrange
        Config::set('app.locale', 'en');
        Config::set('app.supported_locales', ['en', 'ms']);
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'fr'); // Invalid locale

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert - Should fallback to config default
        $this->assertEquals('en', App::currentLocale());
    }

    /**
     * Test that locale is applied to App facade.
     */
    #[Test]
    public function test_locale_applied_to_app_facade(): void
    {
        // Arrange
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('ms', App::currentLocale());
        $this->assertEquals('ms', app()->getLocale());
    }

    /**
     * Test that supported locales configuration is respected.
     */
    #[Test]
    public function test_supported_locales_configuration_respected(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);
        $request = Request::create('/', 'GET');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->put('locale', 'ms');

        // Act
        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert
        $this->assertEquals('ms', App::currentLocale());
    }
}
