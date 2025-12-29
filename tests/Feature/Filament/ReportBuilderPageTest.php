<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\ReportBuilder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportBuilderPageTest extends TestCase
{
    #[Test]
    public function report_builder_navigation_metadata_is_configured(): void
    {
        self::assertSame('Pembina Laporan', ReportBuilder::getNavigationLabel());
        self::assertSame('Laporan & Analitik', ReportBuilder::getNavigationGroup());
        self::assertSame('heroicon-o-document-chart-bar', ReportBuilder::getNavigationIcon());
    }
}
