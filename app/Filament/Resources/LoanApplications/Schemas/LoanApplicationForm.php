<?php

namespace App\Filament\Resources\LoanApplications\Schemas;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LoanApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('application_number')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                TextInput::make('applicant_name')
                    ->required(),
                TextInput::make('applicant_email')
                    ->email()
                    ->required(),
                TextInput::make('applicant_phone')
                    ->tel()
                    ->required(),
                TextInput::make('applicant_position')
                    ->default(null),
                TextInput::make('applicant_grade')
                    ->required(),
                TextInput::make('staff_id')
                    ->required(),
                TextInput::make('grade')
                    ->required(),
                Select::make('division_id')
                    ->relationship('division', 'name_ms')
                    ->required(),
                Textarea::make('purpose')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->required(),
                TextInput::make('return_location')
                    ->required(),
                DatePicker::make('loan_start_date')
                    ->required()
                    ->rules(['after:today']),
                DatePicker::make('loan_end_date')
                    ->required(),
                Toggle::make('is_responsible_officer')
                    ->required(),
                TextInput::make('responsible_officer_name')
                    ->default(null),
                TextInput::make('responsible_officer_position')
                    ->default(null),
                TextInput::make('responsible_officer_grade')
                    ->default(null),
                TextInput::make('responsible_officer_phone')
                    ->tel()
                    ->default(null),
                DateTimePicker::make('applicant_declaration_date'),
                TextInput::make('applicant_digital_signature')
                    ->default(null),
                Toggle::make('terms_acknowledged')
                    ->required(),
                Select::make('approver_id')
                    ->relationship('approver', 'name')
                    ->default(null),
                Select::make('approval_status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Diluluskan',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('approval_date'),
                TextInput::make('approver_digital_signature')
                    ->default(null),
                Textarea::make('approval_notes')
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('expected_return_date'),
                Select::make('status')
                    ->options(LoanStatus::class)
                    ->default('draft')
                    ->required(),
                Select::make('priority')
                    ->options(LoanPriority::class)
                    ->default('normal')
                    ->required(),
                TextInput::make('total_value')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('approver_email')
                    ->email()
                    ->default(null),
                TextInput::make('approved_by_name')
                    ->default(null),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('approval_token_expires_at'),
                TextInput::make('approval_method')
                    ->default(null),
                Textarea::make('approval_remarks')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('rejected_reason')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('special_instructions')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('related_helpdesk_tickets')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('maintenance_required')
                    ->required(),
                DateTimePicker::make('anonymized_at'),
                DateTimePicker::make('claimed_at'),
            ]);
    }
}
