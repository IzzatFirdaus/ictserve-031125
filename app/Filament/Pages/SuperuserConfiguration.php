<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\LoanStatus;
use App\Models\Activity;
use App\Models\LoanApplication;
use App\Services\ApprovalMatrixService;
use App\Services\EmailTemplateService;
use App\Services\SLAThresholdService;
use App\Services\TokenService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Superuser Configuration Management Page
 *
 * Unified configuration page for superusers to manage:
 * - SLA thresholds editing
 * - Email template management
 * - Approval workflow settings
 * - Token regeneration for expired approvals
 *
 * @trace Requirements 7.1, 7.4, 7.5
 *
 * @see D03 SRS-ADM-001, SRS-ADM-004
 */
class SuperuserConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.superuser-configuration';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 0;

    /** @var array<string, mixed> */
    public array $slaThresholds = [];

    /** @var array<string, mixed> */
    public array $approvalMatrix = [];

    public string $selectedLoanReference = '';

    /** @var array<string, mixed> */
    public array $tokenRegenerationData = [];

    protected SLAThresholdService $slaService;

    protected ApprovalMatrixService $approvalService;

    protected EmailTemplateService $emailService;

    protected TokenService $tokenService;

    public function boot(): void
    {
        $this->slaService = app(SLAThresholdService::class);
        $this->approvalService = app(ApprovalMatrixService::class);
        $this->emailService = app(EmailTemplateService::class);
        $this->tokenService = app(TokenService::class);

        $this->loadConfiguration();
    }

    public function mount(): void
    {
        $this->loadConfiguration();
    }

    public function loadConfiguration(): void
    {
        $this->slaThresholds = $this->slaService->getSLAThresholds();
        $this->approvalMatrix = $this->approvalService->getApprovalMatrix();
    }

    protected function getForms(): array
    {
        return [
            'tokenRegenerationForm' => Schema::make($this)
                ->schema([
                    Section::make(__('superuser_config.token_regeneration.title'))
                        ->description(__('superuser_config.token_regeneration.description'))
                        ->icon('heroicon-o-key')
                        ->iconColor('warning')
                        ->schema([
                            Select::make('selectedLoanReference')
                                ->label(__('superuser_config.token_regeneration.loan_reference'))
                                ->options(fn (): array => $this->getExpiredApprovalLoans())
                                ->searchable()
                                ->preload()
                                ->helperText(__('superuser_config.token_regeneration.helper'))
                                ->reactive(),

                            Textarea::make('tokenRegenerationData.reason')
                                ->label(__('superuser_config.token_regeneration.reason'))
                                ->rows(3)
                                ->required()
                                ->maxLength(500)
                                ->helperText(__('superuser_config.token_regeneration.reason_helper')),
                        ])
                        ->columns(1),
                ])
                ->statePath('tokenRegenerationData'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageSLA')
                ->label(__('superuser_config.actions.manage_sla'))
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->url(SLAThresholdManagement::getUrl()),

            Action::make('manageEmailTemplates')
                ->label(__('superuser_config.actions.manage_email'))
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->url(EmailTemplateManagement::getUrl()),

            Action::make('manageApprovalMatrix')
                ->label(__('superuser_config.actions.manage_approval'))
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->url(ApprovalMatrixConfiguration::getUrl()),

            Action::make('viewAuditLog')
                ->label(__('superuser_config.actions.view_audit'))
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('gray')
                ->url(UnifiedAuditLog::getUrl()),
        ];
    }

    /**
     * Get loan applications with expired approval tokens
     *
     * @return array<string, string>
     */
    public function getExpiredApprovalLoans(): array
    {
        return LoanApplication::query()
            ->whereIn('status', [
                LoanStatus::PENDING_APPROVAL->value,
                LoanStatus::UNDER_REVIEW->value,
            ])
            ->where(function ($query) {
                $query->whereNull('approval_token_expires_at')
                    ->orWhere('approval_token_expires_at', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->mapWithKeys(function (LoanApplication $loan): array {
                $applicationNumber = $loan->application_number ?? 'N/A';
                $expiredText = $loan->approval_token_expires_at
                    ? ' ('.__('superuser_config.token_regeneration.expired_at', ['date' => $loan->approval_token_expires_at->format('d/m/Y H:i')]).')'
                    : ' ('.__('superuser_config.token_regeneration.no_token').')';

                return [
                    $applicationNumber => $applicationNumber.' - '.$loan->applicant_name.$expiredText,
                ];
            })
            ->toArray();
    }

    /**
     * Regenerate approval token for selected loan application
     */
    public function regenerateToken(): void
    {
        if (empty($this->selectedLoanReference)) {
            Notification::make()
                ->title(__('superuser_config.notifications.select_loan'))
                ->warning()
                ->send();

            return;
        }

        $reason = $this->tokenRegenerationData['reason'] ?? '';
        if (empty($reason)) {
            Notification::make()
                ->title(__('superuser_config.notifications.reason_required'))
                ->warning()
                ->send();

            return;
        }

        $loan = LoanApplication::where('reference', $this->selectedLoanReference)
            ->orWhere('application_number', $this->selectedLoanReference)
            ->first();

        if (! $loan) {
            Notification::make()
                ->title(__('superuser_config.notifications.loan_not_found'))
                ->danger()
                ->send();

            return;
        }

        try {
            // Regenerate the token
            $tokenData = $this->tokenService->regenerateApprovalToken($loan);

            // Log the action using Activity model
            Activity::create([
                'log_name' => 'token_regeneration',
                'description' => 'Approval token regenerated for loan application',
                'subject_type' => LoanApplication::class,
                'subject_id' => $loan->id,
                'causer_type' => Auth::user() ? get_class(Auth::user()) : null,
                'causer_id' => Auth::id(),
                'properties' => [
                    'reason' => $reason,
                    'loan_reference' => $loan->reference ?? $loan->application_number,
                    'old_expires_at' => $loan->getOriginal('approval_token_expires_at'),
                    'new_expires_at' => $tokenData['expires_at']->toIso8601String(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ]);

            // Reset form
            $this->selectedLoanReference = '';
            $this->tokenRegenerationData = [];

            Notification::make()
                ->title(__('superuser_config.notifications.token_regenerated'))
                ->body(__('superuser_config.notifications.token_regenerated_body', [
                    'reference' => $loan->reference,
                    'expires_at' => $tokenData['expires_at']->format('d/m/Y H:i'),
                ]))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('superuser_config.notifications.token_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Get configuration summary statistics
     *
     * @return array<string, mixed>
     */
    public function getConfigurationStats(): array
    {
        return [
            'sla_categories' => count($this->slaThresholds['categories'] ?? []),
            'approval_rules' => count($this->approvalMatrix['rules'] ?? []),
            'email_templates' => \App\Models\EmailTemplate::count(),
            'expired_tokens' => LoanApplication::query()
                ->where('status', LoanStatus::PENDING_APPROVAL->value)
                ->where(function ($query) {
                    $query->whereNull('approval_token_expires_at')
                        ->orWhere('approval_token_expires_at', '<', now());
                })
                ->count(),
        ];
    }

    /**
     * Get recent configuration changes from audit log
     *
     * @return Collection<int, Activity>
     */
    public function getRecentConfigChanges(): Collection
    {
        return Activity::query()
            ->whereIn('log_name', ['sla_configuration', 'approval_matrix', 'email_templates', 'token_regeneration'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get pending approval loans count
     */
    public function getPendingApprovalsCount(): int
    {
        return LoanApplication::where('status', LoanStatus::PENDING_APPROVAL->value)->count();
    }

    public static function getNavigationLabel(): string
    {
        return __('superuser_config.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('superuser_config.navigation.group');
    }

    public function getTitle(): string
    {
        return __('superuser_config.navigation.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }
}
