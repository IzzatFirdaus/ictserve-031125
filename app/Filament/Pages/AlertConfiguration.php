<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ConfigurableAlertService;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Alert Configuration Page
 *
 * Interface for configuring system alert thresholds and notification settings.
 * Allows administrators to customize alert behavior and recipients.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 */
class AlertConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bell-alert';
    }

    public static function getNavigationLabel(): string
    {
        return 'Konfigurasi Amaran';
    }

    public function getTitle(): string
    {
        return 'Konfigurasi Sistem Amaran';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public function getView(): string
    {
        return 'filament.pages.alert-configuration';
    }

    public function mount(): void
    {
        $alertService = app(ConfigurableAlertService::class);
        $config = $alertService->getAlertConfiguration();

        $this->form->fill($config);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Amaran Tiket')
                    ->description('Tetapkan had dan konfigurasi untuk amaran tiket helpdesk')
                    ->schema([
                        Toggle::make('overdue_tickets_enabled')
                            ->label('Aktifkan Amaran Tiket Tertunggak')
                            ->default(true),

                        TextInput::make('overdue_tickets_threshold')
                            ->label('Had Tiket Tertunggak')
                            ->helperText('Bilangan tiket tertunggak sebelum amaran dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(5)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Konfigurasi Amaran Pinjaman')
                    ->description('Tetapkan had dan konfigurasi untuk amaran pinjaman aset')
                    ->schema([
                        Toggle::make('overdue_loans_enabled')
                            ->label('Aktifkan Amaran Pinjaman Tertunggak')
                            ->default(true),

                        TextInput::make('overdue_loans_threshold')
                            ->label('Had Pinjaman Tertunggak')
                            ->helperText('Bilangan pinjaman tertunggak sebelum amaran dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(3)
                            ->required(),

                        Toggle::make('approval_delays_enabled')
                            ->label('Aktifkan Amaran Kelewatan Kelulusan')
                            ->default(true),

                        TextInput::make('approval_delay_hours')
                            ->label('Had Kelewatan Kelulusan (Jam)')
                            ->helperText('Bilangan jam sebelum amaran kelewatan kelulusan dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(168) // 1 week
                            ->default(48)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Konfigurasi Amaran Aset')
                    ->description('Tetapkan had dan konfigurasi untuk amaran aset dan inventori')
                    ->schema([
                        Toggle::make('asset_shortages_enabled')
                            ->label('Aktifkan Amaran Kekurangan Aset')
                            ->default(true),

                        TextInput::make('critical_asset_shortage_percentage')
                            ->label('Had Kekurangan Aset Kritikal (%)')
                            ->helperText('Peratusan ketersediaan minimum sebelum amaran dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(10)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Konfigurasi Amaran Sistem')
                    ->description('Tetapkan had untuk amaran kesihatan sistem keseluruhan')
                    ->schema([
                        Toggle::make('system_health_enabled')
                            ->label('Aktifkan Amaran Kesihatan Sistem')
                            ->default(true),

                        TextInput::make('system_health_threshold')
                            ->label('Had Skor Kesihatan Sistem (%)')
                            ->helperText('Skor kesihatan minimum sebelum amaran dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99)
                            ->default(70)
                            ->suffix('%')
                            ->required(),

                        TextInput::make('response_time_threshold')
                            ->label('Had Masa Respons (Saat)')
                            ->helperText('Masa respons maksimum sebelum amaran prestasi dihantar')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(3600)
                            ->default(300)
                            ->suffix('s')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Konfigurasi Penyampaian')
                    ->description('Tetapkan kaedah dan kekerapan penyampaian amaran')
                    ->schema([
                        Toggle::make('email_notifications_enabled')
                            ->label('Aktifkan Notifikasi Email')
                            ->default(true),

                        Toggle::make('admin_panel_notifications_enabled')
                            ->label('Aktifkan Notifikasi Panel Admin')
                            ->default(true),

                        \Filament\Forms\Components\Select::make('alert_frequency')
                            ->label('Kekerapan Semakan Amaran')
                            ->options([
                                'immediate' => 'Segera (Real-time)',
                                'hourly' => 'Setiap Jam',
                                'daily' => 'Harian',
                            ])
                            ->default('hourly')
                            ->required(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Konfigurasi')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveConfiguration'),

            Action::make('test')
                ->label('Uji Amaran')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->action('testAlerts')
                ->requiresConfirmation()
                ->modalHeading('Uji Sistem Amaran')
                ->modalDescription('Adakah anda pasti untuk menguji sistem amaran? Ini akan menghantar amaran ujian kepada penerima yang dikonfigurasi.')
                ->modalSubmitActionLabel('Ya, Uji Amaran'),

            Action::make('reset')
                ->label('Reset ke Default')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('resetToDefaults')
                ->requiresConfirmation()
                ->modalHeading('Reset Konfigurasi')
                ->modalDescription('Adakah anda pasti untuk reset semua konfigurasi ke nilai default?')
                ->modalSubmitActionLabel('Ya, Reset'),
        ];
    }

    public function saveConfiguration(): void
    {
        try {
            $alertService = app(ConfigurableAlertService::class);
            $alertService->updateAlertConfiguration($this->data);

            Notification::make()
                ->title('Konfigurasi Disimpan')
                ->body('Konfigurasi amaran telah berjaya disimpan dan akan berkuat kuasa serta-merta.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menyimpan')
                ->body("Ralat semasa menyimpan konfigurasi: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    public function testAlerts(): void
    {
        try {
            $alertService = app(ConfigurableAlertService::class);
            $alertService->sendTestAlert();

            Notification::make()
                ->title('Ujian Amaran Dihantar')
                ->body('Amaran ujian telah dihantar kepada semua penerima yang dikonfigurasi. Semak email dan panel admin untuk mengesahkan penerimaan.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Ujian Gagal')
                ->body("Ralat semasa menguji amaran: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    public function resetToDefaults(): void
    {
        try {
            $alertService = app(ConfigurableAlertService::class);

            // Get default configuration
            $defaultConfig = [
                'overdue_tickets_enabled' => true,
                'overdue_tickets_threshold' => 5,
                'overdue_loans_enabled' => true,
                'overdue_loans_threshold' => 3,
                'approval_delays_enabled' => true,
                'approval_delay_hours' => 48,
                'asset_shortages_enabled' => true,
                'critical_asset_shortage_percentage' => 10,
                'system_health_enabled' => true,
                'system_health_threshold' => 70,
                'response_time_threshold' => 300,
                'email_notifications_enabled' => true,
                'admin_panel_notifications_enabled' => true,
                'alert_frequency' => 'hourly',
            ];

            $alertService->updateAlertConfiguration($defaultConfig);
            $this->form->fill($defaultConfig);

            Notification::make()
                ->title('Konfigurasi Direset')
                ->body('Semua konfigurasi amaran telah direset ke nilai default.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Reset Gagal')
                ->body("Ralat semasa mereset konfigurasi: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }
}
