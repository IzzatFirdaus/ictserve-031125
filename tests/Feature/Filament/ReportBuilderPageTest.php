<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\ReportBuilder;
use PHPUnit\Framework\TestCase;

class ReportBuilderPageTest extends TestCase
{
    public function test_report_builder_navigation_metadata_is_configured(): void
    {
        self::assertSame('Pembina Laporan', ReportBuilder::getNavigationLabel());
        self::assertSame('Reports & Analytics', ReportBuilder::getNavigationGroup());
        self::assertSame('heroicon-o-document-chart-bar', ReportBuilder::getNavigationIcon());
    }
}
