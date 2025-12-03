<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * LoanApplicationInfolist - v3.5.0 True Hybrid Architecture
 *
 * Displays loan application details including:
 * - Form reference code PK.(S).MOTAC.07.(L3) per Requirement 24.2
 * - Applicant and Responsible Officer (when different) per Requirement 25.5
 *
 * @see D03 SRS-LOAN-001 - Loan Application Requirements
 * @see Requirements 24.2, 25.5, 6.4
 */
class LoanApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Form Reference Code Header - Requirement 24.2
            Section::make('Rujukan Borang / Form Reference')
                ->description('PK.(S).MOTAC.07.(L3) - Borang Permohonan Pinjaman Peralatan ICT')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('form_reference_code')
                            ->label('Kod Rujukan Borang')
                            ->default('PK.(S).MOTAC.07.(L3)')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('submission_type')
                            ->label('Jenis Permohonan')
                            ->state(fn (LoanApplication $record): string => $record->user_id ? 'Authenticated Staff' : 'Guest Submission')
                            ->badge()
                            ->color(fn (LoanApplication $record): string => $record->user_id ? 'success' : 'warning'),
                    ]),
                ])
                ->collapsible(),

            Section::make('Ringkasan Permohonan')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('application_number')->label('No Permohonan'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (LoanStatus|string|null $state): string => $state instanceof LoanStatus ? $state->color() : 'primary')
                            ->formatStateUsing(fn (LoanStatus|string|null $state): string => $state instanceof LoanStatus
? $state->label()
                                : (is_string($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')),
                        TextEntry::make('priority')
                            ->badge()
                            ->color(fn (LoanPriority|string|null $state): string => $state instanceof LoanPriority ? $state->color() : 'secondary')
                            ->formatStateUsing(fn (LoanPriority|string|null $state): string => $state instanceof LoanPriority
                                ? $state->label()
                                : (is_string($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')),
                        TextEntry::make('total_value')
                            ->label('Nilai Keseluruhan')
                            ->money('MYR'),
                    ]),
                    TextEntry::make('purpose')->label('Tujuan'),
                ]),
            Section::make('Maklumat Aset')
                ->schema([
                    TextEntry::make('loanItemsSummary')
                        ->label('Senarai Aset')
                        ->html()
                        ->formatStateUsing(function ($state, $record): string {
                            return $record->loanItems
                                ->loadMissing('asset')
                                ->map(function (LoanItem $item): string {
                                    $asset = $item->asset;
                                    $tag = $asset ? e($asset->asset_tag ?? __('loan.messages.unknown')) : e(__('loan.messages.unknown'));
                                    $name = $asset ? e($asset->name ?? '-') : e('-');

                                    return "<div class=\"space-y-1\"><div class=\"font-semibold\">{$tag}</div><div class=\"text-sm text-gray-600 dark:text-gray-300\">{$name}</div></div>";
                                })
                                ->implode('<hr class=\"my-2 border-gray-200 dark:border-gray-700\" />');
                        })
                        ->visible(fn ($record) => $record->loanItems->isNotEmpty())
                        ->placeholder(__('loan.messages.no_assets_requested')),
                ]),
            // Bahagian 1: Applicant Information
            Section::make('Bahagian 1: Maklumat Pemohon / Applicant Information')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('applicant_name')->label('Nama Pemohon'),
                        TextEntry::make('applicant_email')->label('Emel'),
                        TextEntry::make('applicant_phone')->label('Telefon'),
                    ]),
                    Grid::make(3)->schema([
                        TextEntry::make('staff_id')->label('ID Staf'),
                        TextEntry::make('grade')->label('Gred'),
                        TextEntry::make('division.name_ms')->label('Bahagian'),
                    ]),
                ]),

            // Bahagian 2: Responsible Officer - Requirement 25.5
            Section::make('Bahagian 2: Pegawai Bertanggungjawab / Responsible Officer')
                ->icon('heroicon-o-user-circle')
                ->description(fn (LoanApplication $record): string => $record->is_applicant_responsible
                    ? 'Pemohon adalah Pegawai Bertanggungjawab / Applicant is the Responsible Officer'
                    : 'Pegawai Bertanggungjawab berbeza daripada Pemohon / Responsible Officer differs from Applicant')
                ->schema([
                    Grid::make(2)->schema([
                        IconEntry::make('is_applicant_responsible')
                            ->label('Pemohon = Pegawai Bertanggungjawab')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('warning'),
                    ]),
                    // Show Responsible Officer details when different from Applicant
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('responsible_officer_name')
                                ->label('Nama Pegawai Bertanggungjawab')
                                ->placeholder('-')
                                ->visible(fn (LoanApplication $record): bool => ! $record->is_applicant_responsible),
                            TextEntry::make('responsible_officer_grade')
                                ->label('Jawatan & Gred')
                                ->placeholder('-')
                                ->visible(fn (LoanApplication $record): bool => ! $record->is_applicant_responsible),
                            TextEntry::make('responsible_officer_phone')
                                ->label('No. Telefon')
                                ->placeholder('-')
                                ->visible(fn (LoanApplication $record): bool => ! $record->is_applicant_responsible),
                        ]),
                    TextEntry::make('responsible_officer_acknowledged_at')
                        ->label('Tarikh Pengakuan Pegawai Bertanggungjawab')
                        ->dateTime()
                        ->placeholder('Belum diakui')
                        ->visible(fn (LoanApplication $record): bool => ! $record->is_applicant_responsible),
                ])
                ->collapsible(),
            Section::make('Tempoh Pinjaman')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('loan_start_date')->label('Tarikh Mula')->date(),
                        TextEntry::make('loan_end_date')->label('Tarikh Akhir')->date(),
                    ]),
                    Grid::make(2)->schema([
                        TextEntry::make('location')->label('Lokasi Penggunaan'),
                        TextEntry::make('return_location')->label('Lokasi Pemulangan'),
                    ]),
                ]),
            Section::make('Kelulusan')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('approver_email')->label('Emel Pegawai'),
                        TextEntry::make('approved_by_name')->label('Diluluskan oleh'),
                    ]),
                    TextEntry::make('approved_at')->label('Tarikh Kelulusan')->dateTime()->placeholder('-'),
                    TextEntry::make('rejected_reason')->label('Sebab Ditolak')->placeholder('-'),
                    TextEntry::make('special_instructions')->label('Arahan Khas')->placeholder('-'),
                ]),
            Section::make('Integrasi')
                ->schema([
                    TextEntry::make('maintenance_required')
                        ->label('Perlu Penyelenggaraan')
                        ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak')
                        ->badge()
                        ->color(fn ($state) => $state ? 'warning' : 'success'),
                    KeyValueEntry::make('related_helpdesk_tickets')
                        ->label('Tiket Helpdesk')
                        ->placeholder('Tiada data'),
                ]),
            Section::make('Application History')
                ->schema([
                    Grid::make(1)->schema([
                        TextEntry::make('created_at')->label('Dicipta')->dateTime(),
                        TextEntry::make('updated_at')->label('Dikemaskini')->dateTime(),
                    ]),
                ]),
        ]);
    }
}
