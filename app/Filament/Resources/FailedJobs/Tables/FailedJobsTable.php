<?php

declare(strict_types=1);

namespace App\Filament\Resources\FailedJobs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FailedJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('connection')
                    ->sortable(),
                TextColumn::make('queue')
                    ->sortable(),
                TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => $record->uuid]);
                        \Filament\Notifications\Notification::make()
                            ->title('Job retried')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
