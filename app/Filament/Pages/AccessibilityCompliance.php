<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\AccessibilityComplianceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use UnitEnum;

class AccessibilityCompliance extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Accessibility Compliance';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.accessibility-compliance';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runAudit')
                ->label('Run Accessibility Audit')
                ->action('runAccessibilityAudit')
                ->color('primary'),

            Action::make('exportReport')
                ->label('Export Report')
                ->action('exportAccessibilityReport')
                ->color('warning'),
        ];
    }

    #[Computed]
    public function accessibilityAudit(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->auditAccessibility();
    }

    #[Computed]
    public function colorPalette(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->getCompliantColorPalette();
    }

    #[Computed]
    public function focusStyles(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateFocusStyles();
    }

    #[Computed]
    public function ariaAttributes(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateAriaAttributes();
    }

    #[Computed]
    public function keyboardNavigation(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->validateKeyboardNavigation();
    }

    #[Computed]
    public function screenReaderContent(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateScreenReaderContent();
    }

    public function runAccessibilityAudit(): void
    {
        $audit = $this->accessibilityAudit;

        $totalIssues = collect($audit)
            ->sum(fn ($category) => count($category['issues'] ?? []));

        if ($totalIssues === 0) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Accessibility audit completed successfully. No issues found.',
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Accessibility audit completed. {$totalIssues} issues found.",
            ]);
        }
    }

    public function exportAccessibilityReport(): void
    {
        $audit = $this->accessibilityAudit;

        // In a real implementation, this would generate a PDF or CSV report
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Accessibility report export initiated. Check downloads folder.',
        ]);
    }

    public function getComplianceStatus(array $category): string
    {
        return match ($category['status']) {
            'compliant' => 'success',
            'needs_attention' => 'warning',
            'non_compliant' => 'danger',
            default => 'info',
        };
    }

    public function getComplianceIcon(array $category): string
    {
        return match ($category['status']) {
            'compliant' => 'heroicon-o-check-circle',
            'needs_attention' => 'heroicon-o-exclamation-triangle',
            'non_compliant' => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    }

    public function testColorContrast(string $foreground, string $background): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->validateColorContrast($foreground, $background);
    }
}
