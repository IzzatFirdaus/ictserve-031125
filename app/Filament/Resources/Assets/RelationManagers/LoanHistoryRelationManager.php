<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Enums\LoanStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Loan History Relation Manager
 *
 * Displays loan history for an asset with pagination and filtering.
 *
 * @trace Requirements 3.2, 7.2
 */
class LoanHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'loanItems';

    protected static ?string $title = 'Sejarah Pinjaman';

    protected static string|\BackedEnum|null $icon = Heroicon::OutlinedClipboardDocumentList;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('application_number')
            ->columns([
                Tables\Columns\TextColumn::make('loanApplication.application_number')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loanApplication.applicant_name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loanApplication.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state instanceof LoanStatus ? $state->color() : 'primary')
                    ->formatStateUsing(fn($state) => $state instanceof LoanStatus
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state))),
                Tables\Columns\TextColumn::make('loanApplication.loan_start_date')
                    ->label('Tarikh Mula')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('loanApplication.loan_end_date')
                    ->label('Tarikh Tamat')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition_on_issue')
                    ->label('Keadaan Semasa Dikeluarkan')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'excellent' => 'success',
                        'good' => 'info',
                        'fair' => 'warning',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('condition_on_return')
                    ->label('Keadaan Semasa Dipulangkan')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'excellent' => 'success',
                        'good' => 'info',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'damaged' => 'danger',
                        default => 'secondary',
                    })
                    ->default('-'),
                Tables\Columns\TextColumn::make('loanApplication.created_at')
                    ->label('Tarikh Permohonan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->relationship('loanApplication', 'status')
                    ->options([
                        'pending_approval' => 'Menunggu Kelulusan',
                        'approved' => 'Diluluskan',
                        'rejected' => 'Ditolak',
                        'issued' => 'Dikeluarkan',
                        'returned' => 'Dipulangkan',
                        'overdue' => 'Lewat',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('active_loans')
                    ->label('Pinjaman Aktif')
                    ->query(fn($query) => $query->whereHas('loanApplication', function ($q) {
                        $q->whereIn('status', ['approved', 'issued']);
                    }))
                    ->toggle(),
            ])
            ->headerActions([
                // No create action - loans are created through the loan application process
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.loans.loan-applications.view', [
                        'record' => $record->loan_application_id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // No bulk actions for loan history
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('30s');
    }
}
