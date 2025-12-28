<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\SecurityMonitoring;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityMonitoringPageTest extends TestCase
{
    #[Test]
    public function security_monitoring_navigation_metadata_is_configured(): void
    {
        // Navigation label uses translation key - verify it returns a non-empty string
        $navigationLabel = SecurityMonitoring::getNavigationLabel();
        self::assertNotEmpty($navigationLabel);
        self::assertContains($navigationLabel, [
            'Security Monitoring',           // English
            'Pemantauan Keselamatan',        // Bahasa Melayu
            __('admin_pages.security_monitoring.label'), // Current locale
        ]);

        // Navigation group uses translation key - verify it returns a non-empty string
        $navigationGroup = SecurityMonitoring::getNavigationGroup();
        self::assertNotEmpty($navigationGroup);
        self::assertContains($navigationGroup, [
            'System Configuration',          // English
            'Konfigurasi Sistem',            // Bahasa Melayu
            'System',                        // Alternative English
            __('filament.navigation.system'), // Current locale
        ]);

        // Icon should always be the same regardless of locale
        self::assertSame('heroicon-o-shield-exclamation', SecurityMonitoring::getNavigationIcon());
    }
}
