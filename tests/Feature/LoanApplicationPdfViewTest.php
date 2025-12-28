<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LoanApplication;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanApplicationPdfViewTest extends TestCase
{
    /**
     * @see \App\Services\LoanApplicationPdfExporter
     */
    #[Test]
    public function loan_application_pdf_view_renders_with_qr_code(): void
    {
        $application = LoanApplication::factory()->approved()->create();
        $application->load(['user', 'division', 'loanItems.asset', 'transactions']);

        $html = view('pdf.loan-application-single', [
            'application' => $application,
            'includeQR' => true,
        ])->render();

        $this->assertStringContainsString($application->application_number, $html);
        $this->assertStringContainsString('data:image/svg+xml;base64,', $html);
    }
}
