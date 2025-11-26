<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Loan Approval Queue Widget
 *
 * Displays pending loan applications with Portal redirect actions.
 *
 * @trace D03-FR-005.1 (Loan Approval Queue)
 * @trace D04 §5.0.1 (Widget Portal Redirects)
 * @trace D10 §7 (Component Documentation)
 *
 * @version 2.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 *
 * @updated 2025-11-26
 */
class LoanApprovalQueueWidget extends BaseWidget
{
    protected ?string $pollingInterval = '300s';

    protected int|string|array $columnSpan = 'full';

    /**
     * Get the table query with eager loaded relationships.
     *
     * @return Builder<LoanApplication>
     */
    protected function getTableQuery(): Builder
    {
        return LoanApplication::query()
            ->with(['user', 'user.department', 'division', 'loanItems.asset'])
            ->whereIn('status', [
                LoanStatus::UNDER_REVIEW,
                LoanStatus::PENDING_INFO,
                LoanStatus::READY_ISSUANCE,
            ])
            ->orderByDesc('created_at')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('application_number')
                ->label(__('widgets.application_number'))
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('applicant_name')
                ->label(__('widgets.applicant'))
                ->description(fn (LoanApplication $record): ?string => $record->user?->department?->name ?? $record->division?->name_ms)
                ->searchable(),
            Tables\Columns\TextColumn::make('asset_summary')
                ->label(__('widgets.asset_type'))
                ->getStateUsing(fn (LoanApplication $record): string => $this->getAssetTypeSummary($record))
                ->wrap(),
            Tables\Columns\TextColumn::make('total_value')
                ->label(__('widgets.value_rm'))
                ->money('MYR'),
            Tables\Columns\TextColumn::make('status')
                ->label(__('widgets.current_status'))
                ->badge(),
            Tables\Columns\TextColumn::make('time_elapsed')
                ->label(__('widgets.time_elapsed'))
                ->getStateUsing(fn (LoanApplication $record): string => $record->created_at?->diffForHumans() ?? '-')
                ->badge()
                ->color(fn (LoanApplication $record): string => $this->getTimeElapsedColor($record->created_at)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('review_in_portal')
                ->label(__('widgets.review_in_portal'))
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (LoanApplication $record): string => route('loan.authenticated.show', $record))
                ->openUrlInNewTab(false)
                ->color(Color::Amber),
        ];
    }

    protected function getHeading(): string
    {
        return __('widgets.loan_approval_queue');
    }

    /**
     * Get color based on time elapsed since application creation.
     */
    protected function getTimeElapsedColor(?Carbon $createdAt): string
    {
        if (! $createdAt) {
            return 'gray';
        }

        $hoursElapsed = $createdAt->diffInHours(now());

        if ($hoursElapsed < 24) {
            return 'success'; // Green - within SLA
        }

        if ($hoursElapsed < 48) {
            return 'warning'; // Yellow - approaching deadline
        }

        return 'danger'; // Red - overdue
    }

    /**
     * Get a summary of asset types in the loan application.
     */
    protected function getAssetTypeSummary(LoanApplication $record): string
    {
        $assetTypes = $record->loanItems
            ->map(fn ($item) => $item->asset?->asset_type ?? __('widgets.unknown_asset'))
            ->unique()
            ->take(3);

        if ($assetTypes->isEmpty()) {
            return __('widgets.no_assets');
        }

        $summary = $assetTypes->implode(', ');

        if ($record->loanItems->count() > 3) {
            $summary .= ' +'.($record->loanItems->count() - 3);
        }

        return $summary;
    }
}
