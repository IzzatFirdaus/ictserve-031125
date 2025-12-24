<?php

declare(strict_types=1);

/**
 * Master Compliance Validation Script
 *
 * Runs all compliance validation checks and generates comprehensive report
 * per task 1 requirements for compliance validation framework
 */

require_once __DIR__.'/validate-pks-references.php';
require_once __DIR__.'/validate-krisa-templates.php';
require_once __DIR__.'/validate-cross-references.php';
require_once __DIR__.'/version-control-tracker.php';

class MasterComplianceValidator
{
    private PKSReferenceValidator $pksValidator;

    private KRISATemplateValidator $templateValidator;

    private CrossReferenceValidator $crossRefValidator;

    private VersionControlTracker $versionTracker;

    public function __construct()
    {
        $this->pksValidator = new PKSReferenceValidator;
        $this->templateValidator = new KRISATemplateValidator;
        $this->crossRefValidator = new CrossReferenceValidator;
        $this->versionTracker = new VersionControlTracker;
    }

    public function runAllValidations(): array
    {
        echo "Running comprehensive compliance validation...\n\n";

        // Run PKS reference validation
        echo "1. Validating PKS policy references...\n";
        $pksResults = $this->pksValidator->validateAllDocuments();

        // Run KRISA template validation
        echo "2. Validating KRISA template compliance...\n";
        $templateResults = $this->templateValidator->validateAllDocuments();
        $crossRefTemplateResults = $this->templateValidator->validateCrossReferences();

        // Run cross-reference validation
        echo "3. Validating document cross-references...\n";
        $crossRefResults = $this->crossRefValidator->validateAllReferences();

        // Run version control validation
        echo "4. Validating version control consistency...\n";
        $versionResults = $this->versionTracker->getCurrentVersions();
        $versionViolations = $this->versionTracker->validateVersionConsistency();

        echo "All validations completed.\n\n";

        return [
            'pks_results' => $pksResults,
            'template_results' => $templateResults,
            'template_cross_ref' => $crossRefTemplateResults,
            'cross_ref_results' => $crossRefResults,
            'version_results' => $versionResults,
            'version_violations' => $versionViolations,
        ];
    }

    public function generateComprehensiveReport(array $results): string
    {
        $report = "COMPREHENSIVE COMPLIANCE VALIDATION REPORT\n";
        $report .= "KRISA PPSM PKS Compliance Update\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $report .= str_repeat('=', 70)."\n\n";

        // Executive Summary
        $report .= $this->generateExecutiveSummary($results);
        $report .= "\n";

        // PKS Compliance Section
        $report .= "1. PKS POLICY COMPLIANCE VALIDATION\n";
        $report .= str_repeat('=', 40)."\n";
        $report .= $this->pksValidator->generateReport($results['pks_results']);
        $report .= "\n";

        // KRISA Template Compliance Section
        $report .= "2. KRISA TEMPLATE COMPLIANCE VALIDATION\n";
        $report .= str_repeat('=', 45)."\n";
        $report .= $this->templateValidator->generateReport(
            $results['template_results'],
            $results['template_cross_ref']
        );
        $report .= "\n";

        // Cross-Reference Validation Section
        $report .= "3. DOCUMENT CROSS-REFERENCE VALIDATION\n";
        $report .= str_repeat('=', 42)."\n";
        $report .= $this->crossRefValidator->generateReport($results['cross_ref_results']);
        $report .= "\n";

        // Version Control Section
        $report .= "4. VERSION CONTROL VALIDATION\n";
        $report .= str_repeat('=', 32)."\n";
        $report .= $this->versionTracker->generateReport();
        $report .= "\n";

        // Compliance Action Plan
        $report .= $this->generateActionPlan($results);

        return $report;
    }

    private function generateExecutiveSummary(array $results): string
    {
        $summary = "EXECUTIVE SUMMARY\n";
        $summary .= str_repeat('=', 20)."\n";

        // Count total violations across all validations
        $pksViolations = $this->countViolations($results['pks_results']);
        $templateViolations = $this->countViolations($results['template_results']);
        $crossRefViolations = $this->countViolations($results['cross_ref_results']['document_results']);
        $versionViolations = count($results['version_violations']);

        $totalViolations = $pksViolations + $templateViolations + $crossRefViolations + $versionViolations;

        $summary .= "Validation Scope: Complete KRISA documentation suite (D01-D17)\n";
        $summary .= "Compliance Standards: PKS, PSPM, KRISA Templates\n";
        $summary .= 'Total Documents Analyzed: '.count($results['pks_results'])."\n";
        $summary .= "Total Compliance Violations: {$totalViolations}\n\n";

        $summary .= "Violation Breakdown:\n";
        $summary .= "- PKS Policy Violations: {$pksViolations}\n";
        $summary .= "- KRISA Template Violations: {$templateViolations}\n";
        $summary .= "- Cross-Reference Violations: {$crossRefViolations}\n";
        $summary .= "- Version Control Violations: {$versionViolations}\n\n";

        $complianceStatus = $totalViolations === 0 ? 'FULLY COMPLIANT' : 'NON-COMPLIANT';
        $summary .= "Overall Compliance Status: {$complianceStatus}\n";

        if ($totalViolations > 0) {
            $summary .= "Action Required: Immediate remediation needed\n";
        }

        return $summary;
    }

    private function countViolations(array $results): int
    {
        $count = 0;
        foreach ($results as $result) {
            if (isset($result['violations'])) {
                $count += count($result['violations']);
            }
        }

        return $count;
    }

    private function generateActionPlan(array $results): string
    {
        $plan = "COMPLIANCE ACTION PLAN\n";
        $plan .= str_repeat('=', 25)."\n";

        $plan .= "Priority Actions Required:\n\n";

        // High Priority: PKS violations
        $pksViolations = $this->countViolations($results['pks_results']);
        if ($pksViolations > 0) {
            $plan .= "HIGH PRIORITY - PKS Policy Compliance:\n";
            $plan .= "- Eliminate guest access references per PKS 5.2.1\n";
            $plan .= "- Implement data sovereignty controls per PKS 9.2.1 & 4.2\n";
            $plan .= "- Add mandatory authentication specifications\n";
            $plan .= "- Update cloud AI integration documentation\n\n";
        }

        // Medium Priority: Template compliance
        $templateViolations = $this->countViolations($results['template_results']);
        if ($templateViolations > 0) {
            $plan .= "MEDIUM PRIORITY - KRISA Template Compliance:\n";
            $plan .= "- Update document structure to match official templates\n";
            $plan .= "- Add missing methodology references\n";
            $plan .= "- Include CRUD indicators in database documentation\n";
            $plan .= "- Update notation formats (BF-ICT-XX-YY)\n\n";
        }

        // Medium Priority: Cross-references
        $crossRefViolations = $this->countViolations($results['cross_ref_results']['document_results']);
        if ($crossRefViolations > 0) {
            $plan .= "MEDIUM PRIORITY - Cross-Reference Consistency:\n";
            $plan .= "- Fix version mismatches between documents\n";
            $plan .= "- Add missing document references\n";
            $plan .= "- Update traceability statements\n\n";
        }

        // Low Priority: Version control
        $versionViolations = count($results['version_violations']);
        if ($versionViolations > 0) {
            $plan .= "LOW PRIORITY - Version Control:\n";
            $plan .= "- Update document versions per KRISA guidelines\n";
            $plan .= "- Complete change log entries\n";
            $plan .= "- Ensure version consistency across references\n\n";
        }

        $plan .= "Implementation Sequence:\n";
        $plan .= "1. Address PKS policy violations (security critical)\n";
        $plan .= "2. Update KRISA template compliance (audit requirement)\n";
        $plan .= "3. Fix cross-reference inconsistencies (quality assurance)\n";
        $plan .= "4. Standardize version control (documentation management)\n\n";

        $plan .= "Success Criteria:\n";
        $plan .= "- Zero PKS policy violations\n";
        $plan .= "- 100% KRISA template compliance\n";
        $plan .= "- All cross-references validated and consistent\n";
        $plan .= "- Version control following KRISA guidelines\n";

        return $plan;
    }

    public function saveReports(array $results, string $report): void
    {
        $timestamp = date('Y-m-d-H-i-s');
        $baseDir = 'storage/compliance';

        // Ensure directory exists
        @mkdir($baseDir, 0755, true);

        // Save comprehensive report
        $comprehensiveReportPath = "{$baseDir}/comprehensive-compliance-report-{$timestamp}.txt";
        file_put_contents($comprehensiveReportPath, $report);

        // Save individual reports
        $pksReport = $this->pksValidator->generateReport($results['pks_results']);
        file_put_contents("{$baseDir}/pks-validation-{$timestamp}.txt", $pksReport);

        $templateReport = $this->templateValidator->generateReport(
            $results['template_results'],
            $results['template_cross_ref']
        );
        file_put_contents("{$baseDir}/krisa-template-validation-{$timestamp}.txt", $templateReport);

        $crossRefReport = $this->crossRefValidator->generateReport($results['cross_ref_results']);
        file_put_contents("{$baseDir}/cross-reference-validation-{$timestamp}.txt", $crossRefReport);

        $versionReport = $this->versionTracker->generateReport();
        file_put_contents("{$baseDir}/version-control-validation-{$timestamp}.txt", $versionReport);

        echo "All reports saved to: {$baseDir}/\n";
        echo "Comprehensive report: {$comprehensiveReportPath}\n";
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $validator = new MasterComplianceValidator;
    $results = $validator->runAllValidations();
    $report = $validator->generateComprehensiveReport($results);

    echo $report;

    // Save all reports
    $validator->saveReports($results, $report);

    // Exit with error code if violations found
    $hasViolations =
        $validator->countViolations($results['pks_results']) > 0 ||
        $validator->countViolations($results['template_results']) > 0 ||
        $validator->countViolations($results['cross_ref_results']['document_results']) > 0 ||
        count($results['version_violations']) > 0;

    exit($hasViolations ? 1 : 0);
}
