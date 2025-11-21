<?php

namespace App\Filament\Resources\EmailLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipient_email')
                    ->searchable(),
                TextColumn::make('recipient_name')
                    ->searchable(),
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('mailable_class')
                    ->label('Jenis E-mel')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'App\\Mail\\LoanApproved' => 'Notifikasi Kelulusan',
                            'App\\Mail\\TicketCreated' => 'Tiket Baru',
                            'App\\Mail\\LoanApplicationCreated' => 'Permohonan Pinjaman',
                            'App\\Mail\\TicketAssigned' => 'Tiket Ditugaskan',
                            'App\\Mail\\TicketResolved' => 'Tiket Selesai',
                            default => class_basename($state),
                        };
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'App\\Mail\\LoanApproved' => 'success',
                            'App\\Mail\\TicketCreated' => 'primary',
                            'App\\Mail\\LoanApplicationCreated' => 'info',
                            'App\\Mail\\TicketAssigned' => 'warning',
                            'App\\Mail\\TicketResolved' => 'success',
                            default => 'gray',
                        };
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('message_id')
                    ->searchable(),
                TextColumn::make('queued_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
