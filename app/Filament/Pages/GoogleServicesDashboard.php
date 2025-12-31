<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Contracts\SsoHealthCheckInterface;
use App\Models\GoogleServicesAuditLog;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Google Services Dashboard Page
 *
 * Unified admin interface for managing all Google services:
 * - SSO service status and configuration
 * - Gmail API status and configuration
 * - OAuth verification status monitoring
 * - Test user management
 * - Service health monitoring
 * - Usage statistics and quota monitoring
 *
 * @version 3.6.1
 *
 * @since 2025-12-31
 *
 * @author Pasukan BPM MOTAC
 * @copyright 2025 MOTAC BPM
 *
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace Requirements 8.1, 8.2 (Admin Google Services Management)
 *
 * WCAG 2.2 AA: Full keyboard navigation, ARIA labels, 4.5:1 contrast
 */
class GoogleServicesDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud';

    protected string $view = 'filament.pages.google-services-dashboard';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    protected static string $pollingInterval = '60s';

    protected ?SsoHealthCheckInterface $healthCheck = null;

    protected ?GoogleOAuthVerificationServiceInterface $verificationService = null;

    /** @var array<string, mixed> */
    public array $overallStatus = [];

    /** @var array<string, mixed> */
    public array $ssoStatus = [];

    /** @var array<string, mixed> */
    public array $gmailStatus = [];

    /** @var array<string, mixed> */
    public array $verificationStatus = [];

    /** @var array<string, mixed> */
    public array $quotaStatus = [];

    /** @var array<string> */
    public array $testUsers = [];

    /** @var array<string, mixed> */
    public array $recentActivity = [];

    /** @var array<string, int> */
    public array $usageStats = [];

    public string $newTestUserEmail = '';

    public function boot(): void
    {
        $this->healthCheck = app(SsoHealthCheckInterface::class);
        $this->verificationService = app(GoogleOAuthVerificationServiceInterface::class);
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.google_services');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.keselamatan');
    }

    public function getTitle(): string
    {
        return __('admin.google_services_dashboard');
    }

    public function getHeading(): string
    {
        return __('admin.google_services_dashboard');
    }

    public function getSubheading(): ?string
    {
        return __('admin.google_services_dashboard_description');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && ($user->hasRole('admin') || $user->hasRole('superuser'));
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && ($user->hasRole('admin') || $user->hasRole('superuser'));
    }

    public function loadData(): void
    {
        if ($this->healthCheck !== null) {
            $this->overallStatus = $this->healthCheck->getOverallServiceStatus();
            $this->ssoStatus = $this->healthCheck->getServiceStatus();
            $this->gmailStatus = $this->healthCheck->getGmailServiceStatus();
            $this->quotaStatus = $this->healthCheck->checkQuotaLimits();
        }

        if ($this->verificationService !== null) {
            $this->verificationStatus = $this->verificationService->getVerificationDetails();
            $this->testUsers = $this->verificationService->getTestUsers();
        }

        $this->loadRecentActivity();
        $this->loadUsageStats();
    }

    private function loadRecentActivity(): void
    {
        $this->recentActivity = GoogleServicesAuditLog::query()
            ->with('user')
            ->latest('attempted_at')
            ->limit(10)
            ->get()
            ->map(fn (GoogleServicesAuditLog $log): array => [
                'id' => $log->id,
                'service_type' => $log->service_type,
                'email' => $log->email,
                'user_name' => $log->user?->name,
                'operation_type' => $log->operation_type,
                'success' => $log->success,
                'attempted_at' => $log->attempted_at?->diffForHumans(),
            ])
            ->toArray();
    }

    private function loadUsageStats(): void
    {
        $today = now()->startOfDay();

        $this->usageStats = [
            'sso_today' => GoogleServicesAuditLog::query()
                ->where('service_type', GoogleServicesAuditLog::SERVICE_SSO)
                ->where('attempted_at', '>=', $today)
                ->count(),
            'sso_success_today' => GoogleServicesAuditLog::query()
                ->where('service_type', GoogleServicesAuditLog::SERVICE_SSO)
                ->where('attempted_at', '>=', $today)
                ->where('success', true)
                ->count(),
            'gmail_today' => GoogleServicesAuditLog::query()
                ->where('service_type', GoogleServicesAuditLog::SERVICE_GMAIL)
                ->where('attempted_at', '>=', $today)
                ->count(),
            'gmail_success_today' => GoogleServicesAuditLog::query()
                ->where('service_type', GoogleServicesAuditLog::SERVICE_GMAIL)
                ->where('attempted_at', '>=', $today)
                ->where('success', true)
                ->count(),
            'total_sso_users' => User::whereNotNull('google_id')->count(),
        ];
    }

    public function refresh(): void
    {
        if ($this->healthCheck !== null) {
            $this->healthCheck->clearCache();
        }

        if ($this->verificationService !== null) {
            $this->verificationService->clearCache();
        }

        $this->loadData();
        $this->dispatch('$refresh');

        Notification::make()
            ->title(__('admin.data_refreshed'))
            ->success()
            ->send();
    }

    public function addTestUser(): void
    {
        $email = trim($this->newTestUserEmail);

        if (empty($email)) {
            Notification::make()
                ->title(__('admin.email_required'))
                ->danger()
                ->send();

            return;
        }

        if ($this->verificationService === null) {
            Notification::make()
                ->title(__('admin.service_unavailable'))
                ->danger()
                ->send();

            return;
        }

        if ($this->verificationService->addTestUser($email)) {
            $this->newTestUserEmail = '';
            $this->testUsers = $this->verificationService->getTestUsers();

            Notification::make()
                ->title(__('admin.test_user_added'))
                ->body(__('admin.test_user_added_description', ['email' => $email]))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('admin.test_user_add_failed'))
                ->body(__('admin.test_user_add_failed_description'))
                ->danger()
                ->send();
        }
    }

    public function removeTestUser(string $email): void
    {
        if ($this->verificationService === null) {
            Notification::make()
                ->title(__('admin.service_unavailable'))
                ->danger()
                ->send();

            return;
        }

        if ($this->verificationService->removeTestUser($email)) {
            $this->testUsers = $this->verificationService->getTestUsers();

            Notification::make()
                ->title(__('admin.test_user_removed'))
                ->body(__('admin.test_user_removed_description', ['email' => $email]))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('admin.test_user_remove_failed'))
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('admin.refresh'))
                ->icon(Heroicon::OutlinedArrowPath->value)
                ->color(Color::Gray)
                ->action('refresh'),

            Action::make('addTestUser')
                ->label(__('admin.add_test_user'))
                ->icon(Heroicon::OutlinedUserPlus->value)
                ->color(Color::Blue)
                ->form([
                    TextInput::make('email')
                        ->label(__('admin.email'))
                        ->email()
                        ->required()
                        ->placeholder('user@motac.gov.my')
                        ->helperText(__('admin.test_user_email_helper')),
                ])
                ->action(function (array $data): void {
                    $this->newTestUserEmail = $data['email'] ?? '';
                    $this->addTestUser();
                })
                ->visible(fn (): bool => $this->verificationService !== null
                    && ! $this->verificationService->isTestUserLimitReached()),

            Action::make('viewAuditLogs')
                ->label(__('admin.view_audit_logs'))
                ->icon(Heroicon::OutlinedClipboardDocumentList->value)
                ->color(Color::Gray)
                ->url(fn (): string => route('filament.admin.resources.google-services-audit-logs.index')),
        ];
    }

    public function getPollingInterval(): ?string
    {
        return self::$pollingInterval;
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'healthy', 'verified' => 'success',
            'degraded', 'pending', 'testing' => 'warning',
            'unhealthy', 'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusIcon(string $status): string
    {
        return match ($status) {
            'healthy', 'verified' => Heroicon::OutlinedCheckCircle->value,
            'degraded', 'pending', 'testing' => Heroicon::OutlinedExclamationTriangle->value,
            'unhealthy', 'rejected' => Heroicon::OutlinedXCircle->value,
            default => Heroicon::OutlinedQuestionMarkCircle->value,
        };
    }
}
