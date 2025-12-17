<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Schemas;

use App\Enums\LoanStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Loan Application Infolist Schema v3.6.0
 *
 * Provides comprehensive read-only view of loan application details
 * with approval workflow status and asset assignment information.
 *
 * @see D03 Requirements 8.3, 8.5
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
 */
class LoanApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('loan.infolist.application_details'))
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('application_number')
                                ->label(__('loan.filament.application_number'))
                                ->weight('bold')
                                ->copyable(),

                            TextEntry::make('status')
                                ->label(__('loan.filament.status'))
                                ->badge()
                                ->color(fn (LoanStatus $state): string => $state->color()),

                            TextEntry::make('created_at')
                                ->label(__('loan.filament.submitted_at'))
                                ->dateTime('d M Y H:i'),
                        ]),
                ]),

            Section::make(__('loan.infolist.applicant_details'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('applicant_name')
                                ->label(__('loan.filament.applicant_name')),

                            TextEntry::make('applicant_email')
                                ->label(__('loan.filament.applicant_email'))
                                ->copyable(),

                            TextEntry::make('applicant_phone')
                                ->label(__('loan.filament.applicant_phone')),

                            TextEntry::make('division.name')
                                ->label(__('loan.filament.division')),

                            TextEntry::make('grade.name')
                                ->label(__('loan.filament.grade')),

                            TextEntry::make('position')
                                ->label(__('loan.filament.position')),
                        ]),
                ]),

            Section::make(__('loan.infolist.loan_period'))
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('loan_start_date')
                                ->label(__('loan.filament.start_date'))
                                ->date('d M Y'),

                            TextEntry::make('loan_end_date')
                                ->label(__('loan.filament.end_date'))
                                ->date('d M Y'),

                            TextEntry::make('purpose')
                                ->label(__('loan.filament.purpose'))
                                ->columnSpanFull(),
                        ]),
                ]),

            Section::make(__('loan.infolist.approval_details'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('approver.name')
                                ->label(__('loan.filament.approver')),

                            TextEntry::make('approved_at')
                                ->label(__('loan.filament.approved_at'))
                                ->dateTime('d M Y H:i'),

                            TextEntry::make('approval_notes')
                                ->label(__('loan.filament.approval_notes'))
                                ->columnSpanFull(),

                            TextEntry::make('rejection_reason')
                                ->label(__('loan.filament.rejection_reason'))
                                ->columnSpanFull()
                                ->visible(fn ($record) => $record->status === LoanStatus::REJECTED),
                        ]),
                ])
                ->visible(fn ($record) => \in_array($record->status, [
                    LoanStatus::APPROVED,
                    LoanStatus::REJECTED,
                    LoanStatus::READY_ISSUANCE,
                    LoanStatus::ISSUED,
                    LoanStatus::RETURNED,
                ], true)),

            Section::make(__('loan.infolist.issuance_details'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('issued_at')
                                ->label(__('loan.filament.issued_at'))
                                ->dateTime('d M Y H:i'),

                            TextEntry::make('issuedBy.name')
                                ->label(__('loan.filament.issued_by')),

                            TextEntry::make('returned_at')
                                ->label(__('loan.filament.returned_at'))
                                ->dateTime('d M Y H:i')
                                ->visible(fn ($record) => $record->status === LoanStatus::RETURNED),

                            TextEntry::make('returnedBy.name')
                                ->label(__('loan.filament.returned_by'))
                                ->visible(fn ($record) => $record->status === LoanStatus::RETURNED),
                        ]),
                ])
                ->visible(fn ($record) => \in_array($record->status, [
                    LoanStatus::ISSUED,
                    LoanStatus::RETURNED,
                ], true)),
        ]);
    }
}
