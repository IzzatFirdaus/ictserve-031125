<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\LoanItem;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LoanApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
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
                                    $tag = $asset ? e($asset->asset_tag ?? __('Tidak diketahui')) : e(__('Tidak diketahui'));
                                    $name = $asset ? e($asset->name ?? '-') : e('-');

                                    return "<div class=\"space-y-1\"><div class=\"font-semibold\">{$tag}</div><div class=\"text-sm text-gray-600 dark:text-gray-300\">{$name}</div></div>";
                                })
                                ->implode('<hr class=\"my-2 border-gray-200 dark:border-gray-700\" />');
                        })
                        ->visible(fn ($record) => $record->loanItems->isNotEmpty())
                        ->placeholder(__('Tiada aset dipohon')),
                ]),
            Section::make('Pemohon')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('applicant_name')->label('Nama'),
                        TextEntry::make('applicant_email')->label('Emel'),
                        TextEntry::make('applicant_phone')->label('Telefon'),
                    ]),
                    Grid::make(3)->schema([
                        TextEntry::make('staff_id')->label('ID Staf'),
                        TextEntry::make('grade')->label('Gred'),
                        TextEntry::make('division.name_ms')->label('Bahagian'),
                    ]),
                ]),
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
