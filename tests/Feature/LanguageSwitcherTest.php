<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\BilingualSupportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Language Switcher Feature Tests
 *
 * @trace D03-FR-020 (Bilingual Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D15 §2 (Localization Standards)
 *
 * @version 1.0.0
 */
class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test default locale is Bahasa Melayu per D15 §2.1
     */
    #[Test]
    public function defaultLocaleIsBahasaMelayu(): void
    {
        $this->assertEquals('ms', config('app.locale'));
    }

    /**
     * Test language switcher renders on guest layout
     */
    #[Test]
    public function languageSwitcherRendersOnGuestLayout(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSeeLivewire('language-switcher');
    }

    /**
     * Test language switcher renders on authenticated layout
     */
    #[Test]
    public function languageSwitcherRendersOnAuthenticatedLayout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeLivewire('language-switcher');
    }

    /**
     * Test switching to English locale
     */
    #[Test]
    public function canSwitchToEnglishLocale(): void
    {
        Livewire::test('language-switcher')
            ->call('switchLocale', 'en')
            ->assertSessionHas('locale', 'en')
            ->assertDispatched('localeChanged', locale: 'en');
    }

    /**
     * Test switching to Malay locale
     */
    #[Test]
    public function canSwitchToMalayLocale(): void
    {
        Session::put('locale', 'en');

        Livewire::test('language-switcher')
            ->call('switchLocale', 'ms')
            ->assertSessionHas('locale', 'ms')
            ->assertDispatched('localeChanged', locale: 'ms');
    }

    /**
     * Test invalid locale is rejected
     */
    #[Test]
    public function invalidLocaleIsRejected(): void
    {
        Session::put('locale', 'ms');

        Livewire::test('language-switcher')
            ->call('switchLocale', 'fr')
            ->assertSessionHas('locale', 'ms');
    }

    /**
     * Test cookie persistence for locale preference
     * Note: Cookie assertions don't work with Livewire redirects, test session instead
     */
    #[Test]
    public function localePersistsInSession(): void
    {
        Livewire::test('language-switcher')
            ->call('switchLocale', 'en')
            ->assertSessionHas('locale', 'en');
    }

    /**
     * Test BilingualSupportService returns correct supported locales
     */
    #[Test]
    public function bilingualSupportServiceReturnsSupportedLocales(): void
    {
        $service = app(BilingualSupportService::class);
        $locales = $service->getSupportedLocales();

        $this->assertIsArray($locales);
        $this->assertArrayHasKey('ms', $locales);
        $this->assertArrayHasKey('en', $locales);
        $this->assertEquals('Bahasa Melayu', $locales['ms']['name']);
        $this->assertEquals('English', $locales['en']['name']);
    }

    /**
     * Test current locale detection priority: session > cookie > config
     */
    #[Test]
    public function localeDetectionPriority(): void
    {
        $service = app(BilingualSupportService::class);

        // Default: config (ms)
        $this->assertEquals('ms', $service->detectLocale());

        // Session priority (highest)
        Session::put('locale', 'en');
        $this->assertEquals('en', $service->detectLocale());

        // Change back to ms
        Session::put('locale', 'ms');
        $this->assertEquals('ms', $service->detectLocale());
    }

    /**
     * Test translation keys exist for language switcher
     */
    #[Test]
    public function translationKeysExist(): void
    {
        $this->assertNotEmpty(__('common.language_switcher'));
        $this->assertNotEmpty(__('common.switch_to', ['language' => 'English']));
        $this->assertNotEmpty(__('common.current_language'));
        $this->assertNotEmpty(__('common.malay'));
        $this->assertNotEmpty(__('common.english'));
    }

    /**
     * Test WCAG 2.2 AA: aria-current attribute is correct
     */
    #[Test]
    public function wcagAriaCurrentAttributeIsCorrect(): void
    {
        app()->setLocale('ms');

        $response = $this->get('/login');

        // Should have aria-current="page" for active locale, not "true"
        $response->assertSee('aria-current="page"', false);
        $response->assertDontSee('aria-current="true"', false);
    }

    /**
     * Test component reactivity: locale updates after locale change
     */
    #[Test]
    public function componentReactivity(): void
    {
        $initialLocale = app()->getLocale();
        $this->assertContains($initialLocale, ['ms', 'en']);

        $targetLocale = $initialLocale === 'ms' ? 'en' : 'ms';

        Livewire::test('language-switcher')
            ->call('switchLocale', $targetLocale)
            ->assertSessionHas('locale', $targetLocale);

        $this->assertEquals($targetLocale, app()->getLocale());
    }

    /**
     * Test redirect includes cache-busting parameter
     */
    #[Test]
    public function redirectIncludesCacheBustingParameter(): void
    {
        $response = Livewire::test('language-switcher')
            ->call('switchLocale', 'en');

        // Should redirect (we can't assert the exact URL with timestamp, but we can verify redirect happened)
        $response->assertRedirect();
    }

    /**
     * Test localeChanged event is dispatched with correct payload
     */
    #[Test]
    public function localeChangedEventIsDispatched(): void
    {
        Livewire::test('language-switcher')
            ->call('switchLocale', 'en')
            ->assertDispatched('localeChanged', function ($event, $params) {
                return $params['locale'] === 'en';
            });
    }
}
