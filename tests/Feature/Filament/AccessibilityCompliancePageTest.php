<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\AccessibilityCompliance;
use PHPUnit\Framework\TestCase;

class AccessibilityCompliancePageTest extends TestCase
{
    public function test_accessibility_compliance_navigation_metadata_is_configured(): void
    {
        self::assertSame('Accessibility Compliance', AccessibilityCompliance::getNavigationLabel());
        self::assertSame('System Configuration', AccessibilityCompliance::getNavigationGroup());
        self::assertSame('heroicon-o-eye', AccessibilityCompliance::getNavigationIcon());
    }
}
