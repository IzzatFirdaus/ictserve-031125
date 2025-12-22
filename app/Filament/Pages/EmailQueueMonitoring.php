<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\EmailQueueMonitoringService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

/**
 * Email Queue Monitoring Page
 *
 * Superuser-only page for monitoring email queue status, managing failed jobs,
 * and viewing queue performance metrics.
 *
 * Requirements: 18.2, D03-FR-014.2
 *
 * @see D04 §12.1 Email queue monitoring
 */
class EmailQueueMonitoring extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.email-queue-monitoring';

    /**
     * @return array<string, mixed>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\EmailQueueStatsWidget::class,
            \App\Filament\Widgets\EmailQueueTrendsWidget::class,
        ];
    }

    /** @var array<string, mixed> */
    public array $queueStats = [];

    /** @var array<int, mixed> */
    public array $failedJobs = [];

    /** @var array<int, array<string, mixed>> */
    public array $processingTrends = [];

    /** @var array<string, string> */
    public array $workerStatus = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.email_queue.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system_management');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    app(EmailQueueMonitoringService::class)->clearCache();
                    $this->loadData();

                    Notification::make()
                        ->title('Queue data refreshed')
                        ->success()
                        ->send();
                }),

            Action::make('retry_all_failed')
                ->label('Retry All Failed Jobs')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Retry All Failed Jobs')
                ->modalDescription('This will retry all failed jobs that are eligible for retry. Are you sure?')
                ->action(function (): void {
                    $service = app(EmailQueueMonitoringService::class);
                    $failedJobIds = collect($this->failedJobs)->pluck('id')->toArray();

                    $results = $service->bulkRetryFailedJobs($failedJobIds);

                    Notification::make()
                        ->title("Retry completed: {$results['success']} successful, {$results['failed']} failed")
                        ->success()
                        ->send();

                    $this->loadData();
                }),
        ];
    }

    public function retryJob(string $jobId): void
    {
        $service = app(EmailQueueMonitoringService::class);

        if ($service->retryFailedJob($jobId)) {
            Notification::make()
                ->title('Job queued for retry')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to retry job')
                ->danger()
                ->send();
        }

        $this->loadData();
    }

    public function deleteJob(string $jobId): void
    {
        $service = app(EmailQueueMonitoringService::class);

        if ($service->deleteFailedJob($jobId)) {
            Notification::make()
                ->title('Job deleted successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to delete job')
                ->danger()
                ->send();
        }

        $this->loadData();
    }

    private function loadData(): void
    {
        $service = app(EmailQueueMonitoringService::class);

        $this->queueStats = $service->getQueueStats();
        $this->failedJobs = $service->getFailedJobs(20)->toArray();
        $this->processingTrends = $service->getProcessingTrends(7);
        $this->workerStatus = $service->getWorkerStatus();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }
}
