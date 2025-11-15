<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class LoanApprovalQueueWidget extends BaseWidget
{
    protected ?string $pollingInterval = '300s';

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return LoanApplication::query()
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
                ->description(fn ($record) => $record->division?->name_ms)
                ->searchable(),
            Tables\Columns\TextColumn::make('total_value')
                ->label(__('widgets.value_rm'))
                ->money('MYR'),
            Tables\Columns\TextColumn::make('status')
                ->label(__('widgets.current_status'))
                ->badge(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('widgets.created'))
                ->formatStateUsing(fn ($state) => $state?->diffForHumans())
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getHeading(): string
    {
        return 'Senarai Kelulusan Terkini';
    }
}
