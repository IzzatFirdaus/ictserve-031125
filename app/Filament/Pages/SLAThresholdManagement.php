<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\SLAThresholdService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * SLA Threshold Management Page
 *
 * Superuser-only page for managing SLA thresholds, response times,
 * resolution times, escalation rules, and notification settings.
 *
 * @trace Requirements 12.2, 5.2, 5.5
 */
class SLAThresholdManagement extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.pages.sla-threshold-management';

    protected static ?string $title = 'Pengurusan Ambang SLA';

    protected static ?string $navigationLabel = 'Ambang SLA';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 2;

    public array $thresholds = [];

    protected SLAThresholdService $slaService;

    public function boot(): void
    {
        $this->slaService = app(SLAThresholdService::class);
        $this->loadThresholds();
    }

    public function loadThresholds(): void
    {
        $this->thresholds = $this->slaService->getSLAThresholds();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Repeater::make('thresholds.categories')
                    ->label('Kategori SLA')
                    ->schema([
                        Fieldset::make('Maklumat Kategori')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(100),

                                Textarea::make('description')
                                    ->label('Keterangan')
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->columns(2),

                        Fieldset::make('Masa Respons (Jam)')
                            ->schema([
                                TextInput::make('response_times.low')
                                    ->label('Rendah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('response_times.normal')
                                    ->label('Biasa')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('response_times.high')
                                    ->label('Tinggi')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('response_times.urgent')
                                    ->label('Segera')
                                    ->numeric()
                                    ->minValue(0.5)
                                    ->step(0.5)
                                    ->suffix('jam'),
                            ])
                            ->columns(4),

                        Fieldset::make('Masa Penyelesaian (Jam)')
                            ->schema([
                                TextInput::make('resolution_times.low')
                                    ->label('Rendah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('resolution_times.normal')
                                    ->label('Biasa')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('resolution_times.high')
                                    ->label('Tinggi')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),

                                TextInput::make('resolution_times.urgent')
                                    ->label('Segera')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffix('jam'),
                            ])
                            ->columns(4),
                    ])
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => $state['name'] ?? 'Kategori Baharu')
                    ->addActionLabel('Tambah Kategori')
                    ->reorderableWithButtons()
                    ->cloneable(),

                Fieldset::make('Konfigurasi Eskalasi')
                    ->schema([
                        Checkbox::make('thresholds.escalation.enabled')
                            ->label('Aktifkan Eskalasi Automatik')
                            ->default(true),

                        TextInput::make('thresholds.escalation.threshold_percent')
                            ->label('Ambang Eskalasi (%)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(25)
                            ->suffix('%')
                            ->helperText('Eskalasi akan berlaku apabila baki masa kurang daripada peratusan ini'),

                        Select::make('thresholds.escalation.escalation_roles')
                            ->label('Peranan untuk Eskalasi')
                            ->multiple()
                            ->options([
                                'admin' => 'Pentadbir',
                                'superuser' => 'Superuser',
                            ])
                            ->default(['admin', 'superuser']),

                        Checkbox::make('thresholds.escalation.auto_assign')
                            ->label('Tugaskan Automatik kepada Pelulus')
                            ->default(true),
                    ])
                    ->columns(2),

                Fieldset::make('Konfigurasi Notifikasi')
                    ->schema([
                        Checkbox::make('thresholds.notifications.enabled')
                            ->label('Aktifkan Notifikasi SLA')
                            ->default(true),

                        TextInput::make('thresholds.notifications.intervals.warning')
                            ->label('Amaran (Minit sebelum breach)')
                            ->numeric()
                            ->minValue(1)
                            ->default(60)
                            ->suffix('minit'),

                        TextInput::make('thresholds.notifications.intervals.critical')
                            ->label('Kritikal (Minit sebelum breach)')
                            ->numeric()
                            ->minValue(1)
                            ->default(15)
                            ->suffix('minit'),

                        TextInput::make('thresholds.notifications.intervals.overdue')
                            ->label('Tertunggak (Selang notifikasi)')
                            ->numeric()
                            ->minValue(30)
                            ->default(240)
                            ->suffix('minit'),

                        Checkbox::make('thresholds.notifications.recipients.assignee')
                            ->label('Notifikasi kepada Penerima Tugasan')
                            ->default(true),

                        Checkbox::make('thresholds.notifications.recipients.supervisor')
                            ->label('Notifikasi kepada Penyelia')
                            ->default(true),

                        Checkbox::make('thresholds.notifications.recipients.admin')
                            ->label('Notifikasi kepada Pentadbir')
                            ->default(true),
                    ])
                    ->columns(3),

                Fieldset::make('Waktu Perniagaan')
                    ->schema([
                        Checkbox::make('thresholds.business_hours.enabled')
                            ->label('Aktifkan Waktu Perniagaan')
                            ->default(true),

                        Select::make('thresholds.business_hours.timezone')
                            ->label('Zon Masa')
                            ->options([
                                'Asia/Kuala_Lumpur' => 'Asia/Kuala Lumpur (MYT)',
                                'Asia/Singapore' => 'Asia/Singapore (SGT)',
                                'UTC' => 'UTC',
                            ])
                            ->default('Asia/Kuala_Lumpur'),

                        TimePicker::make('thresholds.business_hours.start_time')
                            ->label('Masa Mula')
                            ->default('08:00'),

                        TimePicker::make('thresholds.business_hours.end_time')
                            ->label('Masa Tamat')
                            ->default('17:00'),

                        Select::make('thresholds.business_hours.working_days')
                            ->label('Hari Bekerja')
                            ->multiple()
                            ->options([
                                1 => 'Isnin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Khamis',
                                5 => 'Jumaat',
                                6 => 'Sabtu',
                                7 => 'Ahad',
                            ])
                            ->default([1, 2, 3, 4, 5]),

                        Checkbox::make('thresholds.business_hours.exclude_weekends')
                            ->label('Kecualikan Hujung Minggu')
                            ->default(true),

                        Checkbox::make('thresholds.business_hours.exclude_holidays')
                            ->label('Kecualikan Cuti Umum')
                            ->default(true),
                    ])
                    ->columns(3),
            ])
            ->statePath('thresholds');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Konfigurasi')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action(function (): void {
                    try {
                        $this->slaService->updateSLAThresholds($this->thresholds);

                        Notification::make()
                            ->title('Ambang SLA berjaya dikemaskini')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Ralat menyimpan konfigurasi')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('test')
                ->label('Uji SLA')
                ->icon('heroicon-o-beaker')
                ->color('info')
                ->action(function (): void {
                    $this->runSLATests();
                }),

            Action::make('reset')
                ->label('Reset ke Lalai')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset Ambang SLA')
                ->modalDescription('Adakah anda pasti mahu reset ambang SLA ke konfigurasi lalai? Semua perubahan akan hilang.')
                ->action(function (): void {
                    $this->slaService->clearCache();
                    $this->loadThresholds();

                    Notification::make()
                        ->title('Ambang SLA telah direset')
                        ->success()
                        ->send();
                }),

            Action::make('export')
                ->label('Eksport')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->action(function () {
                    $export = $this->slaService->exportThresholds();
                    $filename = 'sla-thresholds-'.now()->format('Y-m-d-H-i-s').'.json';

                    return response()->streamDownload(
                        fn () => print (json_encode($export, JSON_PRETTY_PRINT)),
                        $filename,
                        ['Content-Type' => 'application/json']
                    );
                }),

            Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('secondary')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Fail JSON')
                        ->acceptedFileTypes(['application/json'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $content = file_get_contents($data['file']->getRealPath());
                        $importData = json_decode($content, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Fail JSON tidak sah');
                        }

                        $this->slaService->importThresholds($importData);
                        $this->loadThresholds();

                        Notification::make()
                            ->title('Ambang SLA berjaya diimport')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Ralat mengimport ambang SLA')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function runSLATests(): void
    {
        // Test SLA calculations with sample data
        $testCases = [
            ['priority' => 'urgent', 'category' => 'security'],
            ['priority' => 'high', 'category' => 'hardware'],
            ['priority' => 'normal', 'category' => 'software'],
            ['priority' => 'low', 'category' => 'general'],
        ];

        $results = [];
        foreach ($testCases as $test) {
            $sla = $this->slaService->getSLAForTicket($test['priority'], $test['category']);
            $deadlines = $this->slaService->calculateSLADeadlines($test['priority'], $test['category']);

            $results[] = [
                'priority' => $test['priority'],
                'category' => $test['category'],
                'response_hours' => $sla['response_time_hours'],
                'resolution_hours' => $sla['resolution_time_hours'],
                'response_deadline' => $deadlines['response_deadline']->format('d/m/Y H:i'),
                'resolution_deadline' => $deadlines['resolution_deadline']->format('d/m/Y H:i'),
            ];
        }

        Notification::make()
            ->title('Ujian SLA selesai')
            ->body('Semua '.count($results).' kes ujian telah dijalankan')
            ->info()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }
}
