<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\AccessibilityCompliance;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessibilityCompliancePageTest extends TestCase
{
    #[Test]
    public function accessibility_compliance_navigation_metadata_is_configured(): void
    {
        // Clear any cached translations and set locale
        app()->setLocale('en');

        // Get the actual navigation group value
        $navigationGroup = AccessibilityCompliance::getNavigationGroup();

        self::assertSame('Accessibility Compliance', AccessibilityCompliance::getNavigationLabel());
        // The navigation group uses translation key 'filament.navigation.system'
        // which translates to 'System' in English
        self::assertSame(__('filament.navigation.system'), $navigationGroup);
        self::assertSame('heroicon-o-eye', AccessibilityCompliance::getNavigationIcon());
    }
}
