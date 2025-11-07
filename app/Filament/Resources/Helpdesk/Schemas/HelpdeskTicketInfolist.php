<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Helpdesk Ticket Infolist
 *
 * Read-only presentation of ticket metadata with SLA indicators.
 */
class HelpdeskTicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan Tiket')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('ticket_number')->label('Nombor Tiket'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state)))
                            ->label('Status'),
                        TextEntry::make('priority')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ucfirst($state))
                            ->label('Keutamaan'),
                        TextEntry::make('category.name_ms')
                            ->label('Kategori'),
                    ]),
                    TextEntry::make('subject')->label('Subjek'),
                    TextEntry::make('description')
                        ->label('Perincian')
                        ->markdown(),
                ]),
            Section::make('Maklumat Pengadu')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('user.name')
                            ->label('Pengguna Berdaftar')
                            ->placeholder('-'),
                        TextEntry::make('guest_name')
                            ->label('Nama Tetamu')
                            ->placeholder('-'),
                        TextEntry::make('guest_email')
                            ->label('Emel Tetamu')
                            ->placeholder('-'),
                        TextEntry::make('guest_phone')
                            ->label('Telefon Tetamu')
                            ->placeholder('-'),
                        TextEntry::make('guest_staff_id')
                            ->label('ID Staf')
                            ->placeholder('-'),
                        TextEntry::make('guest_grade')
                            ->label('Gred')
                            ->placeholder('-'),
                        TextEntry::make('guest_division')
                            ->label('Bahagian (Tetamu)')
                            ->placeholder('-'),
                    ]),
                ]),
            Section::make('Tugasan & SLA')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('assignedDivision.name_ms')
                            ->label('Ditugaskan kepada Bahagian'),
                        TextEntry::make('assignedUser.name')
                            ->label('Pegawai Bertugas'),
                        TextEntry::make('assigned_to_agency')
                            ->label('Agensi Luar')
                            ->placeholder('-'),
                        TextEntry::make('asset.name')
                            ->label('Aset Berkaitan')
                            ->placeholder('-'),
                        TextEntry::make('sla_response_due_at')
                            ->label('SLA Respons')
                            ->dateTime('d M Y, h:i A'),
                        TextEntry::make('sla_resolution_due_at')
                            ->label('SLA Resolusi')
                            ->dateTime('d M Y, h:i A'),
                        TextEntry::make('responded_at')
                            ->label('Tarikh Respons')
                            ->dateTime('d M Y, h:i A'),
                        TextEntry::make('resolved_at')
                            ->label('Tarikh Selesai')
                            ->dateTime('d M Y, h:i A'),
                        TextEntry::make('closed_at')
                            ->label('Tarikh Tutup')
                            ->dateTime('d M Y, h:i A'),
                    ]),
                ]),
            Section::make('Nota')
                ->schema([
                    TextEntry::make('admin_notes')
                        ->label('Nota Pentadbir')
                        ->markdown()
                        ->placeholder('-'),
                    TextEntry::make('internal_notes')
                        ->label('Nota Dalaman')
                        ->placeholder('-')
                        ->markdown(),
                    TextEntry::make('resolution_notes')
                        ->label('Nota Penyelesaian')
                        ->placeholder('-')
                        ->markdown(),
                ]),

            // Related Asset Card (Cross-Module Integration)
            Section::make('Aset Berkaitan')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('asset.asset_code')
                            ->label('Kod Aset')
                            ->placeholder('-'),
                        TextEntry::make('asset.name')
                            ->label('Nama Aset')
                            ->placeholder('-')
                            ->url(fn ($record) => $record->asset_id ? route('filament.admin.resources.assets.assets.view', $record->asset_id) : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('asset.category.name_en')
                            ->label('Kategori')
                            ->placeholder('-'),
                        TextEntry::make('asset.status')
                            ->label('Status Aset')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                            ->color(fn (?string $state): string => match ($state) {
                                'available' => 'success',
                                'on_loan' => 'warning',
                                'maintenance' => 'danger',
                                'retired' => 'secondary',
                                default => 'gray',
                            })
                            ->placeholder('-'),
                        TextEntry::make('asset.location')
                            ->label('Lokasi')
                            ->placeholder('-'),
                        TextEntry::make('asset.condition')
                            ->label('Keadaan')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '-')
                            ->color(fn (?string $state): string => match ($state) {
                                'excellent' => 'success',
                                'good' => 'info',
                                'fair' => 'warning',
                                'poor' => 'danger',
                                'damaged' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('-'),
                        TextEntry::make('asset.currentLoan.applicant_name')
                            ->label('Peminjam Semasa')
                            ->placeholder('Tiada pinjaman aktif')
                            ->visible(fn ($record) => $record->asset?->status === 'on_loan'),
                        TextEntry::make('asset.currentLoan.expected_return_date')
                            ->label('Tarikh Jangka Pulang')
                            ->date('d M Y')
                            ->placeholder('-')
                            ->visible(fn ($record) => $record->asset?->status === 'on_loan'),
                    ]),
                    TextEntry::make('damage_type')
                        ->label('Jenis Kerosakan')
                        ->placeholder('-')
                        ->visible(fn ($record) => $record->damage_type !== null),
                    TextEntry::make('asset.loanHistory')
                        ->label('Sejarah Pinjaman (5 Terkini)')
                        ->listWithLineBreaks()
                        ->formatStateUsing(function ($record) {
                            if (! $record->asset_id) {
                                return '-';
                            }

                            $loans = \App\Models\LoanApplication::where('asset_id', $record->asset_id)
                                ->with('user')
                                ->latest()
                                ->limit(5)
                                ->get();

                            return $loans->map(function ($loan) {
                                $status = ucfirst(str_replace('_', ' ', $loan->status));
                                $date = $loan->loan_date?->format('d M Y') ?? 'N/A';
                                $applicant = $loan->user?->name ?? $loan->applicant_name ?? 'Unknown';

                                return "{$date} - {$applicant} ({$status})";
                            })->join("\n") ?: 'Tiada sejarah pinjaman';
                        })
                        ->placeholder('Tiada sejarah pinjaman'),
                ])
                ->visible(fn ($record) => $record->asset_id !== null)
                ->collapsible(),
        ]);
    }
}
