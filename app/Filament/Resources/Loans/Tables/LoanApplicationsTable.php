<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Tables;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\LoanApplication;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class LoanApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_number')
                    ->label('No Permohonan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('applicant_name')
                    ->label('Pemohon')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('division.name_ms')
                    ->label('Bahagian')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof LoanStatus ? $state->color() : 'primary')
                    ->formatStateUsing(fn ($state) => method_exists($state, 'label')
                        ? $state->label()
                        : ucfirst(str_replace('_', ' ', (string) $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn ($state) => $state instanceof LoanPriority ? $state->color() : 'secondary')
                    ->formatStateUsing(fn ($state) => method_exists($state, 'label')
                        ? $state->label()
                        : ucfirst(str_replace('_', ' ', (string) $state))),
                Tables\Columns\TextColumn::make('loan_start_date')
                    ->label('Mula')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loan_end_date')
                    ->label('Tamat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_value')
                    ->label('Nilai (RM)')
                    ->money('MYR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('maintenance_required')
                    ->label('Penyelenggaraan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::enumOptions(LoanStatus::cases())),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Keutamaan')
                    ->options(self::enumOptions(LoanPriority::cases())),
                Tables\Filters\SelectFilter::make('division_id')
                    ->relationship('division', 'name_ms')
                    ->label('Bahagian'),
                Tables\Filters\Filter::make('overdue')
                    ->label('Lewat')
                    ->query(fn ($query) => $query->where('status', LoanStatus::OVERDUE->value)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sendApproval')
                    ->label('Hantar untuk Kelulusan')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (LoanApplication $record) => in_array(
                        $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                        [
                            LoanStatus::SUBMITTED->value,
                            LoanStatus::UNDER_REVIEW->value,
                        ]
                    ) && empty($record->approval_token))
                    ->requiresConfirmation()
                    ->action(fn (LoanApplication $record) => LoanApplicationResource::sendForApproval($record)),
                Tables\Actions\Action::make('approve')
                    ->label('Luluskan')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (LoanApplication $record) => in_array(
                        $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                        [
                            LoanStatus::UNDER_REVIEW->value,
                            LoanStatus::PENDING_INFO->value,
                        ]
                    ))
                    ->form([
                        Textarea::make('remarks')
                            ->label('Catatan Kelulusan')
                            ->maxLength(500),
                    ])
                    ->action(function (LoanApplication $record, array $data) {
                        $record->update([
                            'status' => LoanStatus::APPROVED,
                            'approved_at' => now(),
                            'rejected_reason' => null,
                            'special_instructions' => $data['remarks'] ?? $record->special_instructions,
                        ]);
                    }),
                Tables\Actions\Action::make('decline')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (LoanApplication $record) => $record->status instanceof LoanStatus
                        ? ! $record->status->isTerminal()
                        : ! in_array((string) $record->status, [
                            LoanStatus::REJECTED->value,
                            LoanStatus::COMPLETED->value,
                        ]))
                    ->form([
                        Textarea::make('reason')
                            ->label('Sebab Penolakan')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(fn (LoanApplication $record, array $data) => $record->update([
                        'status' => LoanStatus::REJECTED,
                        'rejected_reason' => $data['reason'],
                        'approval_token' => null,
                        'approval_token_expires_at' => null,
                    ])),
                Tables\Actions\Action::make('extend')
                    ->label('Lanjutkan')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (LoanApplication $record) => in_array(
                        $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                        [
                            LoanStatus::IN_USE->value,
                            LoanStatus::RETURN_DUE->value,
                        ]
                    ))
                    ->form([
                        DatePicker::make('loan_end_date')
                            ->label('Tarikh Baru')
                            ->required()
                            ->minDate(fn (callable $get) => now()),
                        Textarea::make('special_instructions')
                            ->label('Arahan')
                            ->maxLength(500),
                    ])
                    ->action(fn (LoanApplication $record, array $data) => $record->update([
                        'loan_end_date' => $data['loan_end_date'],
                        'special_instructions' => $data['special_instructions'],
                        'status' => LoanStatus::RETURN_DUE,
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkApprove')
                        ->label('Luluskan Pilihan')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each(
                            fn (LoanApplication $application) => $application->update([
                                'status' => LoanStatus::APPROVED,
                                'approved_at' => now(),
                                'rejected_reason' => null,
                            ])
                        )),
                    Tables\Actions\BulkAction::make('bulkDecline')
                        ->label('Tolak Pilihan')
                        ->color('danger')
                        ->form([
                            Textarea::make('reason')
                                ->label('Sebab')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each(
                            fn (LoanApplication $application) => $application->update([
                                'status' => LoanStatus::REJECTED,
                                'rejected_reason' => $data['reason'],
                                'approval_token' => null,
                                'approval_token_expires_at' => null,
                            ])
                        )),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
