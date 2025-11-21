<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use OwenIt\Auditing\Models\Audit;

class SensitiveAccessLogWidget extends TableWidget
{
    protected static ?string $heading = 'Akses Data Sensitif Terkini';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Audit::whereIn('auditable_type', [
                    'App\\Models\\User',
                    'App\\Models\\LoanApplication',
                ])
                ->latest()
                ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Masa')
                    ->dateTime('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->default('Sistem'),
                Tables\Columns\TextColumn::make('event')
                    ->label('Tindakan')
                    ->badge(),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Jenis Data')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
            ]);
    }
}
