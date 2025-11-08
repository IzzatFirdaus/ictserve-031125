<?php

declare(strict_types=1);

namespace Tests\Feature\Translations;

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HelpdeskTranslationTest extends TestCase
{
    #[Test]
    public function email_keys_exist_in_all_supported_locales(): void
    {
        $keys = [
            'helpdesk.email.ticket_created_title',
            'helpdesk.email.portal_features_title',
            'helpdesk.email.sla_breach_alert_title',
        ];

        foreach (['en', 'ms'] as $locale) {
            App::setLocale($locale);

            foreach ($keys as $key) {
                $this->assertNotSame(
                    $key,
                    __($key),
                    sprintf('Missing translation for %s in %s locale', $key, $locale)
                );
            }
        }
    }
}
