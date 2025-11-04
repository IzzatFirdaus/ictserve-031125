<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Helpdesk Ticket Form Schema
 *
 * Provides admin level management over ticket lifecycle, assignment, and SLA metadata.
 */
class HelpdeskTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Tiket')
                ->schema([
                    TextInput::make('ticket_number')
                        ->label('Nombor Tiket')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('subject')
                        ->label('Subjek')
                        ->required()
                        ->maxLength(255),
                    Select::make('category_id')
                        ->relationship('category', 'name_ms')
                        ->label('Kategori')
                        ->searchable()
                        ->preload()
                        ->required(),
                    ToggleButtons::make('priority')
                        ->label('Keutamaan')
                        ->options(self::priorityOptions())
                        ->inline()
                        ->required(),
                    TextInput::make('damage_type')
                        ->label('Jenis Kerosakan')
                        ->maxLength(255),
                    Select::make('asset_id')
                        ->relationship('asset', 'name')
                        ->label('Aset Berkaitan')
                        ->searchable()
                        ->preload(),
                    ToggleButtons::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->inline()
                        ->required(),
                ])
                ->columns(2),
            Section::make('Maklumat Pengadu')
                ->description('Tiket boleh daripada pengguna berdaftar atau tetamu')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Pengguna Berdaftar')
                        ->searchable()
                        ->preload()
                        ->helperText('Pilih pengguna berdaftar ATAU isi maklumat tetamu di bawah')
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $state ? self::clearGuestFields($set) : null),

                    // Guest fields - shown when user_id is null
                    TextInput::make('guest_name')
                        ->label('Nama Tetamu')
                        ->maxLength(255)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_email')
                        ->label('Emel Tetamu')
                        ->email()
                        ->maxLength(255)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_phone')
                        ->label('Telefon Tetamu')
                        ->tel()
                        ->maxLength(30)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_staff_id')
                        ->label('ID Staf Tetamu')
                        ->maxLength(50)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_grade')
                        ->label('Gred Tetamu')
                        ->maxLength(10)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_division')
                        ->label('Bahagian Tetamu')
                        ->maxLength(100)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),

                    Textarea::make('description')
                        ->label('Perincian Aduan')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Tugasan & SLA')
                ->schema([
                    Select::make('division_id')
                        ->relationship('division', 'name_ms')
                        ->label('Bahagian Pemohon')
                        ->searchable()
                        ->preload(),
                    Select::make('assigned_to_division')
                        ->relationship('assignedDivision', 'name_ms')
                        ->label('Ditugaskan kepada Bahagian')
                        ->searchable()
                        ->preload(),
                    TextInput::make('assigned_to_agency')
                        ->label('Agensi Luar')
                        ->maxLength(255),
                    Select::make('assigned_to_user')
                        ->relationship('assignedUser', 'name')
                        ->label('Pegawai Bertugas')
                        ->searchable()
                        ->preload(),
                    DateTimePicker::make('sla_response_due_at')
                        ->label('SLA Respons')
                        ->seconds(false),
                    DateTimePicker::make('sla_resolution_due_at')
                        ->label('SLA Resolusi')
                        ->seconds(false),
                    DateTimePicker::make('responded_at')
                        ->label('Tarikh Respons')
                        ->seconds(false),
                    DateTimePicker::make('resolved_at')
                        ->label('Tarikh Selesai')
                        ->seconds(false),
                    DateTimePicker::make('closed_at')
                        ->label('Tarikh Tutup')
                        ->seconds(false),
                ])
                ->columns(3),
            Section::make('Nota')
                ->schema([
                    Textarea::make('admin_notes')
                        ->label('Nota Pentadbir')
                        ->rows(3),
                    Textarea::make('internal_notes')
                        ->label('Nota Dalaman')
                        ->rows(3),
                    Textarea::make('resolution_notes')
                        ->label('Nota Penyelesaian')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }

    private static function statusOptions(): array
    {
        return [
            'open' => 'Open / Dibuka',
            'assigned' => 'Assigned / Ditugaskan',
            'in_progress' => 'In Progress / Dalam Tindakan',
            'pending_user' => 'Pending User / Menunggu Pengadu',
            'resolved' => 'Resolved / Selesai',
            'closed' => 'Closed / Ditutup',
        ];
    }

    private static function priorityOptions(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * Clear guest fields when authenticated user is selected
     */
    private static function clearGuestFields(callable $set): void
    {
        $set('guest_name', null);
        $set('guest_email', null);
        $set('guest_phone', null);
        $set('guest_staff_id', null);
        $set('guest_grade', null);
        $set('guest_division', null);
    }
}
