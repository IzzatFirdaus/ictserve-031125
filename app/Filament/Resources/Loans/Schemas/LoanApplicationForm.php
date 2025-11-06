<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LoanApplicationForm
{
    /**
     * @param  array<int, LoanStatus>  $statuses
     * @param  array<int, LoanPriority>  $priorities
     */
    public static function configure(Schema $schema, array $statuses, array $priorities): Schema
    {
        return $schema->components([
            Section::make('Maklumat Permohonan')
                ->schema([
                    TextInput::make('application_number')
                        ->label('No Permohonan')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('staff_id')
                        ->label('ID Staf')
                        ->required()
                        ->maxLength(20),
                    Select::make('division_id')
                        ->relationship('division', 'name_ms')
                        ->label('Bahagian')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('grade')
                        ->label('Gred')
                        ->required()
                        ->maxLength(10),
                ])
                ->columns(2),
            Section::make('Pemohon')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('applicant_name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('applicant_email')
                            ->label('Emel')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('applicant_phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ]),
                    Textarea::make('purpose')
                        ->label('Tujuan Permohonan')
                        ->rows(3)
                        ->required(),
                ]),
            Section::make('Butiran Pinjaman')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('location')
                            ->label('Lokasi Penggunaan')
                            ->required(),
                        TextInput::make('return_location')
                            ->label('Lokasi Pemulangan')
                            ->required(),
                    ]),
                    Grid::make(2)->schema([
                        DatePicker::make('loan_start_date')
                            ->label('Tarikh Mula')
                            ->required(),
                        DatePicker::make('loan_end_date')
                            ->label('Tarikh Akhir')
                            ->required()
                            ->minDate(fn (callable $get) => $get('loan_start_date')),
                    ]),
                    Grid::make(3)->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(self::enumOptions($statuses))
                            ->required(),
                        Select::make('priority')
                            ->label('Keutamaan')
                            ->options(self::enumOptions($priorities))
                            ->required(),
                        TextInput::make('total_value')
                            ->label('Nilai Keseluruhan (RM)')
                            ->numeric()
                            ->required(),
                    ]),
                ]),
            Section::make('Kelulusan & Notifikasi')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('approver_email')
                            ->label('Emel Pegawai (G41+)')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('approved_by_name')
                            ->label('Diluluskan oleh')
                            ->maxLength(255),
                    ]),
                    Grid::make(2)->schema([
                        DatePicker::make('approved_at')
                            ->label('Tarikh Kelulusan')
                            ->native(false),
                        Textarea::make('rejected_reason')
                            ->label('Sebab Ditolak')
                            ->rows(2),
                    ]),
                    Select::make('approval_method')
                        ->label('Kaedah Kelulusan')
                        ->options([
                            'email' => 'Email',
                            'portal' => 'Portal',
                        ])
                        ->native(false),
                    Textarea::make('approval_remarks')
                        ->label('Catatan Kelulusan')
                        ->rows(2),
                    Textarea::make('special_instructions')
                        ->label('Arahan Khas')
                        ->rows(2),
                    Toggle::make('maintenance_required')
                        ->label('Perlu Penyelenggaraan')
                        ->inline(false),
                    KeyValue::make('related_helpdesk_tickets')
                        ->label('Tiket Helpdesk Berkaitan')
                        ->keyLabel('Ticket #')
                        ->valueLabel('Catatan')
                        ->reorderable(),
                ]),
        ]);
    }

    /**
     * @param  array<int, LoanStatus|LoanPriority>  $cases
     */
    private static function enumOptions(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn ($case) => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }
}
