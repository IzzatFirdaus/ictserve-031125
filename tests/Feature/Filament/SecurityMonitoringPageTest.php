<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\SecurityMonitoring;
use PHPUnit\Framework\TestCase;

class SecurityMonitoringPageTest extends TestCase
{
    public function test_security_monitoring_navigation_metadata_is_configured(): void
    {
        self::assertSame('Security Monitoring', SecurityMonitoring::getNavigationLabel());
        self::assertSame('System Configuration', SecurityMonitoring::getNavigationGroup());
        self::assertSame('heroicon-o-shield-exclamation', SecurityMonitoring::getNavigationIcon());
    }
}
