<?php

namespace App\Filament\Exports;

use App\Models\LoanApplication;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class LoanApplicationExporter extends Exporter
{
    protected static ?string $model = LoanApplication::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('application_number'),
            ExportColumn::make('tracking_token_expires_at'),
            ExportColumn::make('user.name'),
            ExportColumn::make('applicant_name'),
            ExportColumn::make('applicant_position'),
            ExportColumn::make('applicant_grade'),
            ExportColumn::make('applicant_email'),
            ExportColumn::make('applicant_phone'),
            ExportColumn::make('staff_id'),
            ExportColumn::make('grade'),
            ExportColumn::make('division.id'),
            ExportColumn::make('purpose'),
            ExportColumn::make('location'),
            ExportColumn::make('return_location'),
            ExportColumn::make('loan_start_date'),
            ExportColumn::make('loan_end_date'),
            ExportColumn::make('expected_return_date'),
            ExportColumn::make('status'),
            ExportColumn::make('priority'),
            ExportColumn::make('total_value'),
            ExportColumn::make('approver_email'),
            ExportColumn::make('approved_by_name'),
            ExportColumn::make('approved_at'),
            ExportColumn::make('rejected_at'),
            ExportColumn::make('approval_token_expires_at'),
            ExportColumn::make('approval_method'),
            ExportColumn::make('approval_remarks'),
            ExportColumn::make('rejected_reason'),
            ExportColumn::make('special_instructions'),
            ExportColumn::make('pickup_otp_hash'),
            ExportColumn::make('pickup_otp_expires_at'),
            ExportColumn::make('pickup_otp_attempts'),
            ExportColumn::make('pickup_otp_generated_at'),
            ExportColumn::make('pickup_otp_validated_at'),
            ExportColumn::make('is_applicant_responsible'),
            ExportColumn::make('is_delegate'),
            ExportColumn::make('responsible_officer_details'),
            ExportColumn::make('responsible_officer_name'),
            ExportColumn::make('responsible_officer_position'),
            ExportColumn::make('responsible_officer_grade'),
            ExportColumn::make('responsible_officer_phone'),
            ExportColumn::make('responsible_officer_email'),
            ExportColumn::make('responsible_officer_acknowledged_at'),
            ExportColumn::make('sponsorship_token_expires_at'),
            ExportColumn::make('applicant_declaration_date'),
            ExportColumn::make('applicant_digital_signature'),
            ExportColumn::make('terms_acknowledged'),
            ExportColumn::make('declared_at'),
            ExportColumn::make('approver.name'),
            ExportColumn::make('approval_status'),
            ExportColumn::make('approval_date'),
            ExportColumn::make('approver_digital_signature'),
            ExportColumn::make('approval_notes'),
            ExportColumn::make('related_helpdesk_tickets'),
            ExportColumn::make('maintenance_required'),
            ExportColumn::make('accessories'),
            ExportColumn::make('anonymized_at'),
            ExportColumn::make('claimed_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('pickup_otp_validated_by'),
            ExportColumn::make('approved_by'),
            ExportColumn::make('rejected_by'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your loan application export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
