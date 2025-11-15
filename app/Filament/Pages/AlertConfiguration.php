<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ConfigurableAlertService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
        return __('admin_pages.alert_configuration.label');
    }

    public function getTitle(): string
    {
        return __('admin_pages.alert_configuration.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.alert_configuration.group');
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

        $this->data = $config;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_pages.alert_configuration.sections.tickets'))
                    ->description(__('admin_pages.alert_configuration.sections.tickets_desc'))
                    ->schema([
                        Toggle::make('overdue_tickets_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.overdue_tickets_enabled'))
                            ->default(true),

                        TextInput::make('overdue_tickets_threshold')
                            ->label(__('admin_pages.alert_configuration.fields.overdue_tickets_threshold'))
                            ->helperText(__('admin_pages.alert_configuration.fields.overdue_tickets_threshold_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(5)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.alert_configuration.sections.loans'))
                    ->description(__('admin_pages.alert_configuration.sections.loans_desc'))
                    ->schema([
                        Toggle::make('overdue_loans_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.overdue_loans_enabled'))
                            ->default(true),

                        TextInput::make('overdue_loans_threshold')
                            ->label(__('admin_pages.alert_configuration.fields.overdue_loans_threshold'))
                            ->helperText(__('admin_pages.alert_configuration.fields.overdue_loans_threshold_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(3)
                            ->required(),

                        Toggle::make('approval_delays_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.approval_delays_enabled'))
                            ->default(true),

                        TextInput::make('approval_delay_hours')
                            ->label(__('admin_pages.alert_configuration.fields.approval_delay_hours'))
                            ->helperText(__('admin_pages.alert_configuration.fields.approval_delay_hours_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(168) // 1 week
                            ->default(48)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.alert_configuration.sections.assets'))
                    ->description(__('admin_pages.alert_configuration.sections.assets_desc'))
                    ->schema([
                        Toggle::make('asset_shortages_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.asset_shortages_enabled'))
                            ->default(true),

                        TextInput::make('critical_asset_shortage_percentage')
                            ->label(__('admin_pages.alert_configuration.fields.critical_asset_shortage_percentage'))
                            ->helperText(__('admin_pages.alert_configuration.fields.critical_asset_shortage_percentage_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(10)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.alert_configuration.sections.system'))
                    ->description(__('admin_pages.alert_configuration.sections.system_desc'))
                    ->schema([
                        Toggle::make('system_health_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.system_health_enabled'))
                            ->default(true),

                        TextInput::make('system_health_threshold')
                            ->label(__('admin_pages.alert_configuration.fields.system_health_threshold'))
                            ->helperText(__('admin_pages.alert_configuration.fields.system_health_threshold_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99)
                            ->default(70)
                            ->suffix('%')
                            ->required(),

                        TextInput::make('response_time_threshold')
                            ->label(__('admin_pages.alert_configuration.fields.response_time_threshold'))
                            ->helperText(__('admin_pages.alert_configuration.fields.response_time_threshold_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(3600)
                            ->default(300)
                            ->suffix('s')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.alert_configuration.sections.delivery'))
                    ->description(__('admin_pages.alert_configuration.sections.delivery_desc'))
                    ->schema([
                        Toggle::make('email_notifications_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.email_notifications_enabled'))
                            ->default(true),

                        Toggle::make('admin_panel_notifications_enabled')
                            ->label(__('admin_pages.alert_configuration.fields.admin_panel_notifications_enabled'))
                            ->default(true),

                        \Filament\Forms\Components\Select::make('alert_frequency')
                            ->label(__('admin_pages.alert_configuration.fields.alert_frequency'))
                            ->options([
                                'immediate' => __('admin_pages.alert_configuration.frequency.immediate'),
                                'hourly' => __('admin_pages.alert_configuration.frequency.hourly'),
                                'daily' => __('admin_pages.alert_configuration.frequency.daily'),
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
                ->label(__('admin_pages.alert_configuration.actions.save'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveConfiguration'),

            Action::make('test')
                ->label(__('admin_pages.alert_configuration.actions.test'))
                ->icon('heroicon-o-play')
                ->color('warning')
                ->action('testAlerts')
                ->requiresConfirmation()
                ->modalHeading(__('admin_pages.alert_configuration.modals.test_heading'))
                ->modalDescription(__('admin_pages.alert_configuration.modals.test_description'))
                ->modalSubmitActionLabel(__('admin_pages.alert_configuration.modals.test_submit')),

            Action::make('reset')
                ->label(__('admin_pages.alert_configuration.actions.reset'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('resetToDefaults')
                ->requiresConfirmation()
                ->modalHeading(__('admin_pages.alert_configuration.modals.reset_heading'))
                ->modalDescription(__('admin_pages.alert_configuration.modals.reset_description'))
                ->modalSubmitActionLabel(__('admin_pages.alert_configuration.modals.reset_submit')),
        ];
    }

    public function saveConfiguration(): void
    {
        try {
            $alertService = app(ConfigurableAlertService::class);
            $alertService->updateAlertConfiguration($this->data);

            Notification::make()
                ->title(__('admin_pages.alert_configuration.notifications.saved_title'))
                ->body(__('admin_pages.alert_configuration.notifications.saved_body'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin_pages.alert_configuration.notifications.save_failed_title'))
                ->body(__('admin_pages.alert_configuration.notifications.save_failed_body', ['error' => $e->getMessage()]))
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
                ->title(__('admin_pages.alert_configuration.notifications.test_sent_title'))
                ->body(__('admin_pages.alert_configuration.notifications.test_sent_body'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin_pages.alert_configuration.notifications.test_failed_title'))
                ->body(__('admin_pages.alert_configuration.notifications.test_failed_body', ['error' => $e->getMessage()]))
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
            $this->data = $defaultConfig;

            Notification::make()
                ->title(__('admin_pages.alert_configuration.notifications.reset_title'))
                ->body(__('admin_pages.alert_configuration.notifications.reset_body'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin_pages.alert_configuration.notifications.reset_failed_title'))
                ->body(__('admin_pages.alert_configuration.notifications.reset_failed_body', ['error' => $e->getMessage()]))
                ->danger()
                ->send();
        }
    }
}
