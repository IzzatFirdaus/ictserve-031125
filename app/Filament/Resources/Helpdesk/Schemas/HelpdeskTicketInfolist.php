<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

/**
 * Helpdesk Ticket Infolist
 *
 * Read-only presentation of ticket metadata with SLA indicators.
 */
class HelpdeskTicketInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist->schema([
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
        ]);
    }
}
