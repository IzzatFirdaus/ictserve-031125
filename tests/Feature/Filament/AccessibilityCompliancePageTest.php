<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\AccessibilityCompliance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessibilityCompliancePageTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function accessibility_compliance_navigation_metadata_is_configured(): void
    {
        app()->setLocale('en');

        self::assertSame('Accessibility Compliance', AccessibilityCompliance::getNavigationLabel());
        self::assertSame('System Configuration', AccessibilityCompliance::getNavigationGroup());
        self::assertSame('heroicon-o-eye', AccessibilityCompliance::getNavigationIcon());
    }
}
