<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Tables;

use App\Enums\LoanStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoanApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application_number')
                    ->label(__('loan.filament.application_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('applicant_name')
                    ->label(__('loan.filament.applicant_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('division.name_ms')
                    ->label(__('loan.filament.division'))
                    ->formatStateUsing(fn ($record) => $record->division?->name)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('loan.filament.status'))
                    ->badge()
                    ->color(fn (LoanStatus $state): string => $state->color())
                    ->sortable(),
                TextColumn::make('loan_start_date')
                    ->label(__('loan.filament.start_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('loan_end_date')
                    ->label(__('loan.filament.end_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('approver.name')
                    ->label(__('loan.filament.approver'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('loan.filament.submitted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('loan.filament.status'))
                    ->options(LoanStatus::class)
                    ->multiple(),
                SelectFilter::make('division_id')
                    ->label(__('loan.filament.division'))
                    ->relationship('division', app()->getLocale() === 'ms' ? 'name_ms' : 'name_en')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('submitted_from')
                            ->label(__('loan.filament.submitted_from')),
                        DatePicker::make('submitted_until')
                            ->label(__('loan.filament.submitted_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['submitted_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['submitted_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('assign_assets')
                    ->label(__('loan.filament.assign_assets'))
                    ->icon('heroicon-o-cube')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status === LoanStatus::APPROVED || $record->status === LoanStatus::READY_ISSUANCE)
                    ->url(fn ($record): string => route('filament.admin.resources.loan-applications.assign-assets', ['record' => $record])),
                Action::make('record_return')
                    ->label(__('loan.filament.record_return'))
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('warning')
                    ->visible(fn ($record): bool => $record->status === LoanStatus::ISSUED)
                    ->url(fn ($record): string => route('filament.admin.resources.loan-applications.record-return', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
