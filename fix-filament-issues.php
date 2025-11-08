<?php

declare(strict_types=1);

/**
 * Automated Filament 4 Issue Fixer
 *
 * Resolves 126 PHPStan errors in app/Filament directory
 */
$fixes = [
    // Fix 1: Replace Filament\Forms\Form with Filament\Schemas\Schema
    [
        'files' => [
            'app/Filament/Pages/BilingualManagement.php',
            'app/Filament/Pages/EmailTemplateManagement.php',
            'app/Filament/Pages/NotificationPreferences.php',
            'app/Filament/Pages/ReportBuilder.php',
            'app/Filament/Pages/SLAThresholdManagement.php',
            'app/Filament/Pages/UnifiedSearch.php',
            'app/Filament/Pages/WorkflowAutomationConfiguration.php',
        ],
        'search' => 'use Filament\\Forms\\Form;',
        'replace' => 'use Filament\\Schemas\\Schema;',
    ],
    [
        'files' => [
            'app/Filament/Pages/BilingualManagement.php',
            'app/Filament/Pages/EmailTemplateManagement.php',
            'app/Filament/Pages/NotificationPreferences.php',
            'app/Filament/Pages/ReportBuilder.php',
            'app/Filament/Pages/SLAThresholdManagement.php',
            'app/Filament/Pages/UnifiedSearch.php',
            'app/Filament/Pages/WorkflowAutomationConfiguration.php',
        ],
        'search' => 'public function form(Form $form): Form',
        'replace' => 'public function form(Schema $schema): Schema',
    ],
    [
        'files' => [
            'app/Filament/Pages/BilingualManagement.php',
            'app/Filament/Pages/EmailTemplateManagement.php',
            'app/Filament/Pages/NotificationPreferences.php',
            'app/Filament/Pages/ReportBuilder.php',
            'app/Filament/Pages/SLAThresholdManagement.php',
            'app/Filament/Pages/UnifiedSearch.php',
            'app/Filament/Pages/WorkflowAutomationConfiguration.php',
        ],
        'search' => 'return $form',
        'replace' => 'return $schema',
    ],

    // Fix 2: Add HasForms trait to pages with form property
    [
        'files' => [
            'app/Filament/Pages/BilingualManagement.php',
            'app/Filament/Pages/EmailTemplateManagement.php',
            'app/Filament/Pages/NotificationPreferences.php',
            'app/Filament/Pages/WorkflowAutomationConfiguration.php',
        ],
        'search' => 'use Filament\\Pages\\Page;',
        'replace' => "use Filament\\Forms\\Concerns\\InteractsWithForms;\nuse Filament\\Forms\\Contracts\\HasForms;\nuse Filament\\Pages\\Page;",
    ],
    [
        'files' => [
            'app/Filament/Pages/BilingualManagement.php',
            'app/Filament/Pages/EmailTemplateManagement.php',
            'app/Filament/Pages/NotificationPreferences.php',
            'app/Filament/Pages/WorkflowAutomationConfiguration.php',
        ],
        'search' => 'class BilingualManagement extends Page',
        'replace' => "class BilingualManagement extends Page implements HasForms\n{\n    use InteractsWithForms;",
        'pattern' => '/class (\w+) extends Page\n{/',
        'replacement' => "class $1 extends Page implements HasForms\n{\n    use InteractsWithForms;",
    ],

    // Fix 3: Fix navigation badge return types
    [
        'files' => [
            'app/Filament/Resources/HelpdeskTicketResource.php',
            'app/Filament/Resources/System/AuditResource.php',
        ],
        'search' => 'return static::getModel()::where',
        'replace' => 'return (string) static::getModel()::where',
    ],

    // Fix 4: Fix Heroicon constants
    [
        'pattern' => '/Heroicon::Outline(\w+)/',
        'replacement' => 'Heroicon::$1',
        'files' => 'all',
    ],
];

echo "Filament 4 Issue Fixer\n";
echo "======================\n\n";

$totalFixed = 0;

foreach ($fixes as $fix) {
    if (isset($fix['pattern'])) {
        // Regex-based fix
        $files = $fix['files'] === 'all'
            ? glob('app/Filament/**/*.php')
            : $fix['files'];

        foreach ($files as $file) {
            if (! file_exists($file)) {
                continue;
            }

            $content = file_get_contents($file);
            $newContent = preg_replace($fix['pattern'], $fix['replacement'], $content);

            if ($content !== $newContent) {
                file_put_contents($file, $newContent);
                echo "✓ Fixed: $file\n";
                $totalFixed++;
            }
        }
    } else {
        // Simple string replacement
        foreach ($fix['files'] as $file) {
            if (! file_exists($file)) {
                continue;
            }

            $content = file_get_contents($file);
            if (strpos($content, $fix['search']) !== false) {
                $newContent = str_replace($fix['search'], $fix['replace'], $content);
                file_put_contents($file, $newContent);
                echo "✓ Fixed: $file\n";
                $totalFixed++;
            }
        }
    }
}

echo "\n======================\n";
echo "Total files fixed: $totalFixed\n";
echo "\nRun: php vendor/bin/phpstan analyse app/Filament --level=5\n";
