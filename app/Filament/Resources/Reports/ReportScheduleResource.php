<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports;

use App\Filament\Resources\Reports\ReportScheduleResource\Pages;
use App\Models\ReportSchedule;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReportScheduleResource extends Resource
{
    protected static ?string $model = ReportSchedule::class;

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Maklumat Asas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Laporan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('module')
                            ->label('Modul')
                            ->options([
                                'helpdesk' => 'Helpdesk',
                                'loans' => 'Pinjaman Aset',
                                'assets' => 'Inventori Aset',
                                'users' => 'Pengurusan Pengguna',
                                'unified' => 'Laporan Bersepadu',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('format')
                            ->label('Format')
                            ->options([
                                'pdf' => 'PDF',
                                'csv' => 'CSV',
                                'excel' => 'Excel',
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tetapan Jadual')
                    ->schema([
                        Forms\Components\Select::make('frequency')
                            ->label('Kekerapan')
                            ->options([
                                'daily' => 'Harian',
                                'weekly' => 'Mingguan',
                                'monthly' => 'Bulanan',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TimePicker::make('schedule_time')
                            ->label('Masa')
                            ->default('09:00:00')
                            ->required(),

                        Forms\Components\Select::make('schedule_day_of_week')
                            ->label('Hari dalam Minggu')
                            ->options([
                                1 => 'Isnin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Khamis',
                                5 => 'Jumaat',
                                6 => 'Sabtu',
                                7 => 'Ahad',
                            ])
                            ->visible(fn (callable $get) => $get('frequency') === 'weekly'),

                        Forms\Components\Select::make('schedule_day_of_month')
                            ->label('Hari dalam Bulan')
                            ->options(array_combine(range(1, 31), array_map('strval', range(1, 31))))
                            ->visible(fn (callable $get) => $get('frequency') === 'monthly'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Penerima dan Penapis')
                    ->schema([
                        Forms\Components\TagsInput::make('recipients')
                            ->label('Penerima E-mel')
                            ->placeholder('Masukkan alamat e-mel')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('filters')
                            ->label('Penapis Laporan')
                            ->keyLabel('Nama Penapis')
                            ->valueLabel('Nilai')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Laporan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('module')
                    ->label('Modul')
                    ->colors([
                        'primary' => 'helpdesk',
                        'success' => 'loans',
                        'warning' => 'assets',
                        'info' => 'users',
                        'secondary' => 'unified',
                    ]),

                Tables\Columns\BadgeColumn::make('frequency')
                    ->label('Kekerapan')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('schedule_time')
                    ->label('Masa')
                    ->time('H:i'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('last_run_at')
                    ->label('Terakhir Dijana')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Belum pernah'),

                Tables\Columns\TextColumn::make('next_run_at')
                    ->label('Akan Datang')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Tidak dijadualkan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->label('Modul')
                    ->options([
                        'helpdesk' => 'Helpdesk',
                        'loans' => 'Pinjaman Aset',
                        'assets' => 'Inventori Aset',
                        'users' => 'Pengurusan Pengguna',
                        'unified' => 'Laporan Bersepadu',
                    ]),

                Tables\Filters\SelectFilter::make('frequency')
                    ->label('Kekerapan')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('run_now')
                    ->label('Jana Sekarang')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Jana Laporan Sekarang')
                    ->modalDescription('Adakah anda pasti mahu menjana laporan ini sekarang?')
                    ->action(function (ReportSchedule $record) {
                        try {
                            app(\App\Services\AutomatedReportService::class)
                                ->generateAndSendReport($record);

                            $record->markAsExecuted();

                            \Filament\Notifications\Notification::make()
                                ->title('Laporan berjaya dijana')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal menjana laporan')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);

                            \Filament\Notifications\Notification::make()
                                ->title('Jadual laporan diaktifkan')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nyahaktif')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);

                            \Filament\Notifications\Notification::make()
                                ->title('Jadual laporan dinyahaktif')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportSchedules::route('/'),
            'create' => Pages\CreateReportSchedule::route('/create'),
            'view' => Pages\ViewReportSchedule::route('/{record}'),
            'edit' => Pages\EditReportSchedule::route('/{record}/edit'),
        ];
    }
}
