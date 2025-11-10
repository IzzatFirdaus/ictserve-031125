<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Pages;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\LoanApplication;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * View Loan Application Page
 *
 * Enhanced with comprehensive loan lifecycle management actions.
 *
 * @trace Requirements 3.1, 3.3, 5.1, 5.2
 */
class ViewLoanApplication extends ViewRecord
{
    protected static string $resource = LoanApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            // Approve Action
            Action::make('approve')
                ->label('Luluskan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (LoanApplication $record): bool => in_array(
                    $record->status,
                    [LoanStatus::SUBMITTED, LoanStatus::UNDER_REVIEW, LoanStatus::PENDING_INFO]
                ))
                ->authorize('approve', $this->getRecord())
                ->requiresConfirmation()
                ->modalHeading('Luluskan Permohonan')
                ->modalDescription('Adakah anda pasti untuk meluluskan permohonan ini?')
                ->form([
                    Textarea::make('approval_notes')
                        ->label('Catatan Kelulusan')
                        ->placeholder('Masukkan sebarang catatan tambahan (pilihan)')
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->action(function (LoanApplication $record, array $data): void {
                    $record->update([
                        'status' => LoanStatus::APPROVED,
                        'approved_at' => now(),
                        'approved_by_name' => Auth::user()?->name ?? 'Admin',
                        'approval_method' => 'portal',
                        'approval_remarks' => $data['approval_notes'] ?? null,
                        'rejected_reason' => null,
                    ]);
                })
                ->successNotificationTitle('Permohonan berjaya diluluskan')
                ->after(fn () => $this->refreshFormData(['status', 'approved_at', 'approved_by_name'])),

            // Reject Action
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (LoanApplication $record): bool => ! in_array(
                    $record->status,
                    [LoanStatus::REJECTED, LoanStatus::COMPLETED]
                ))
                ->authorize('approve', $this->getRecord())
                ->requiresConfirmation()
                ->modalHeading('Tolak Permohonan')
                ->modalDescription('Sila berikan sebab penolakan.')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Sebab Penolakan')
                        ->placeholder('Masukkan sebab penolakan')
                        ->required()
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->action(function (LoanApplication $record, array $data): void {
                    $record->update([
                        'status' => LoanStatus::REJECTED,
                        'rejected_reason' => $data['rejection_reason'],
                        'approval_token' => null,
                        'approval_token_expires_at' => null,
                    ]);
                })
                ->successNotificationTitle('Permohonan telah ditolak')
                ->after(fn () => $this->refreshFormData(['status', 'rejected_reason'])),

            // Mark as Collected Action
            Action::make('markAsCollected')
                ->label('Tandakan Telah Diambil')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->visible(fn (LoanApplication $record): bool => $record->status === LoanStatus::APPROVED)
                ->authorize('issue', $this->getRecord())
                ->requiresConfirmation()
                ->modalHeading('Sahkan Pengambilan Aset')
                ->modalDescription('Adakah anda pasti aset telah diambil oleh pemohon?')
                ->action(function (LoanApplication $record): void {
                    $record->update([
                        'status' => LoanStatus::IN_USE,
                        'collected_at' => now(),
                    ]);

                    // Update asset status to 'loaned'
                    foreach ($record->loanItems as $item) {
                        $item->asset->update(['status' => AssetStatus::LOANED]);
                    }
                })
                ->successNotificationTitle('Aset telah ditanda sebagai diambil')
                ->after(fn () => $this->refreshFormData(['status', 'collected_at'])),

            // Process Return Action
            Action::make('processReturn')
                ->label('Proses Pemulangan')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn (LoanApplication $record): bool => $record->status === LoanStatus::IN_USE)
                ->authorize('return', $this->getRecord())
                ->requiresConfirmation()
                ->modalHeading('Proses Pemulangan Aset')
                ->form([
                    Select::make('return_condition')
                        ->label('Keadaan Aset')
                        ->required()
                        ->options([
                            'good' => 'Baik',
                            'fair' => 'Sederhana',
                            'damaged' => 'Rosak',
                        ])
                        ->default('good')
                        ->native(false),
                    Textarea::make('return_notes')
                        ->label('Catatan Pemulangan')
                        ->placeholder('Masukkan sebarang catatan tambahan (pilihan)')
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->action(function (LoanApplication $record, array $data): void {
                    $record->update([
                        'status' => LoanStatus::RETURNED,
                        'returned_at' => now(),
                        'return_condition' => $data['return_condition'],
                        'return_notes' => $data['return_notes'] ?? null,
                    ]);

                    // Update asset status based on condition
                    foreach ($record->loanItems as $item) {
                        $status = $data['return_condition'] === 'damaged'
                            ? AssetStatus::MAINTENANCE
                            : AssetStatus::AVAILABLE;

                        $item->asset->update(['status' => $status]);
                    }
                })
                ->successNotificationTitle('Pemulangan telah diproses')
                ->after(fn () => $this->refreshFormData(['status', 'returned_at', 'return_condition'])),

            // Approve Extension Action
            Action::make('approveExtension')
                ->label('Luluskan Lanjutan')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->visible(fn (LoanApplication $record): bool => in_array(
                    $record->status,
                    [LoanStatus::IN_USE, LoanStatus::RETURN_DUE]
                ))
                ->authorize('approve', $this->getRecord())
                ->requiresConfirmation()
                ->modalHeading('Luluskan Lanjutan Tempoh')
                ->form([
                    DatePicker::make('new_end_date')
                        ->label('Tarikh Tamat Baru')
                        ->required()
                        ->minDate(now())
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    Textarea::make('extension_notes')
                        ->label('Catatan Lanjutan')
                        ->placeholder('Masukkan sebab lanjutan (pilihan)')
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->action(function (LoanApplication $record, array $data): void {
                    $record->update([
                        'loan_end_date' => $data['new_end_date'],
                        'special_instructions' => $data['extension_notes'] ?? $record->special_instructions,
                        'status' => LoanStatus::IN_USE,
                    ]);
                })
                ->successNotificationTitle('Lanjutan tempoh telah diluluskan')
                ->after(fn () => $this->refreshFormData(['loan_end_date', 'status', 'special_instructions'])),
        ];
    }
}
