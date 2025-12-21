<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Models\User;
use App\Services\AccessibilityComplianceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use UnitEnum;

class AccessibilityCompliance extends Page
{
    use HandlesTranslations;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.accessibility-compliance';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasRole('superuser');
    }

    public static function getNavigationLabel(): string
    {
        return static::trans('admin_pages.accessibility_compliance.label', 'Accessibility Compliance');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runAudit')
                ->label(static::trans('accessibility.run_audit', 'Run Audit'))
                ->action('runAccessibilityAudit')
                ->color('primary'),

            Action::make('exportReport')
                ->label(static::trans('accessibility.export_report', 'Export Report'))
                ->action('exportAccessibilityReport')
                ->color('warning'),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    #[Computed]
    public function accessibilityAudit(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->auditAccessibility();
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function colorPalette(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->getCompliantColorPalette();
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function focusStyles(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateFocusStyles();
    }

    /**
     * @return array<string, array<string, string>>
     */
    #[Computed]
    public function ariaAttributes(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateAriaAttributes();
    }

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function keyboardNavigation(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->validateKeyboardNavigation();
    }

    /**
     * @return array<string, array<string, string>>
     */
    #[Computed]
    public function screenReaderContent(): array
    {
        $service = app(AccessibilityComplianceService::class);

        return $service->generateScreenReaderContent();
    }

    public function runAccessibilityAudit(): void
    {
        $audit = $this->accessibilityAudit();

        $totalIssues = collect($audit)
            ->sum(fn (array $category): int => count((array) ($category['issues'] ?? [])));

        if ($totalIssues === 0) {
            Notification::make()
                ->title(static::trans('accessibility.audit_completed_no_issues', 'Audit completed with no issues'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(static::trans('accessibility.audit_completed_issues', 'Audit completed'))
                ->body(static::trans('accessibility.audit_completed_issues', 'Audit completed')." ({$totalIssues})")
                ->warning()
                ->send();
        }
    }

    public function exportAccessibilityReport(): void
    {
        $audit = $this->accessibilityAudit();

        // In a real implementation, this would generate a PDF or CSV report
        Notification::make()
            ->title(static::trans('accessibility.report_export_initiated', 'Export started'))
            ->info()
            ->send();
    }

    /**
     * @param  array<string, mixed>  $category
     */
    public function getComplianceStatus(array $category): string
    {
        return match ($category['status']) {
            'compliant' => 'success',
            'needs_attention' => 'warning',
            'non_compliant' => 'danger',
            default => 'info',
        };
    }

    /**
     * @param  array<string, mixed>  $category
     */
    public function getComplianceIcon(array $category): string
    {
        return match ($category['status']) {
            'compliant' => 'heroicon-o-check-circle',
            'needs_attention' => 'heroicon-o-exclamation-triangle',
            'non_compliant' => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    }

    /**
     * @return array{compliant: bool, ratio: float, required: float}
     */
    public function testColorContrast(string $foreground, string $background): array
    {
        $service = app(AccessibilityComplianceService::class);

        $result = $service->validateColorContrast($foreground, $background);

        return [
            'compliant' => (bool) ($result['compliant'] ?? false),
            'ratio' => (float) ($result['ratio'] ?? 0.0),
            'required' => (float) ($result['required'] ?? 0.0),
        ];
    }
}
