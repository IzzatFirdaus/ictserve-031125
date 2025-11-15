<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

/**
 * Notification Preferences
 *
 * Allows admin users to configure their notification preferences including
 * delivery methods, frequency settings, and notification types.
 *
 * Requirements: 10.4
 *
 * @see D03-FR-008.4 Notification preferences
 * @see D04 §8.1 Notification system
 */
class NotificationPreferences extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.notification-preferences';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'notification-preferences';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public function mount(): void
    {
        $user = auth()->user();
        $preferences = $user->notification_preferences ?? [];

        $this->form->fill([
            'email_notifications' => $preferences['email_notifications'] ?? true,
            'in_app_notifications' => $preferences['in_app_notifications'] ?? true,
            'sms_notifications' => $preferences['sms_notifications'] ?? false,
            'desktop_notifications' => $preferences['desktop_notifications'] ?? true,

            // Notification Types
            'helpdesk_notifications' => $preferences['helpdesk_notifications'] ?? [
                'ticket_assigned' => true,
                'ticket_status_changed' => true,
                'sla_breach' => true,
                'overdue_tickets' => true,
            ],
            'loan_notifications' => $preferences['loan_notifications'] ?? [
                'application_submitted' => true,
                'application_approved' => true,
                'application_rejected' => true,
                'asset_overdue' => true,
                'return_reminder' => true,
            ],
            'security_notifications' => $preferences['security_notifications'] ?? [
                'security_incidents' => true,
                'failed_logins' => true,
                'role_changes' => true,
                'config_changes' => true,
            ],
            'system_notifications' => $preferences['system_notifications'] ?? [
                'maintenance_alerts' => true,
                'performance_alerts' => false,
                'backup_status' => false,
                'update_notifications' => true,
            ],

            // Frequency Settings
            'digest_frequency' => $preferences['digest_frequency'] ?? 'daily',
            'quiet_hours_enabled' => $preferences['quiet_hours_enabled'] ?? false,
            'quiet_hours_start' => $preferences['quiet_hours_start'] ?? '22:00',
            'quiet_hours_end' => $preferences['quiet_hours_end'] ?? '08:00',
            'weekend_notifications' => $preferences['weekend_notifications'] ?? false,

            // Priority Settings
            'urgent_only_mode' => $preferences['urgent_only_mode'] ?? false,
            'priority_threshold' => $preferences['priority_threshold'] ?? 'medium',
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.notification_preferences.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.notification_preferences.group');
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_pages.notification_preferences.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('notification_preferences.delivery_methods'))
                    ->description(__('notification_preferences.choose_how_receive'))
                    ->schema([
                        Forms\Components\Toggle::make('email_notifications')
                            ->label(__('notification_preferences.email_notifications'))
                            ->helperText(__('notification_preferences.receive_via_email'))
                            ->default(true),

                        Forms\Components\Toggle::make('in_app_notifications')
                            ->label(__('notification_preferences.in_app_notifications'))
                            ->helperText(__('notification_preferences.show_in_admin_panel'))
                            ->default(true),

                        Forms\Components\Toggle::make('sms_notifications')
                            ->label(__('notification_preferences.sms_notifications'))
                            ->helperText(__('notification_preferences.receive_via_sms'))
                            ->default(false),

                        Forms\Components\Toggle::make('desktop_notifications')
                            ->label(__('notification_preferences.desktop_notifications'))
                            ->helperText(__('notification_preferences.show_desktop_notifications'))
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make(__('notification_preferences.helpdesk_section'))
                    ->description(__('notification_preferences.helpdesk_desc'))
                    ->schema([
                        Forms\Components\CheckboxList::make('helpdesk_notifications')
                            ->label(__('notification_preferences.notification_types'))
                            ->options([
                                'ticket_assigned' => __('notification_preferences.ticket_assigned'),
                                'ticket_status_changed' => __('notification_preferences.ticket_status_changed'),
                                'sla_breach' => __('notification_preferences.sla_breach'),
                                'overdue_tickets' => __('notification_preferences.overdue_tickets'),
                                'new_comments' => __('notification_preferences.new_comments'),
                                'escalation_alerts' => __('notification_preferences.escalation_alerts'),
                            ])
                            ->columns(2)
                            ->default(['ticket_assigned', 'ticket_status_changed', 'sla_breach', 'overdue_tickets']),
                    ]),

                Section::make(__('notification_preferences.loan_section'))
                    ->description(__('notification_preferences.loan_desc'))
                    ->schema([
                        Forms\Components\CheckboxList::make('loan_notifications')
                            ->label(__('notification_preferences.notification_types'))
                            ->options([
                                'application_submitted' => __('notification_preferences.new_loan_applications'),
                                'application_approved' => __('notification_preferences.application_approved'),
                                'application_rejected' => __('notification_preferences.application_rejected'),
                                'asset_overdue' => __('notification_preferences.asset_overdue'),
                                'return_reminder' => __('notification_preferences.return_reminder'),
                                'damage_reports' => __('notification_preferences.damage_reports'),
                            ])
                            ->columns(2)
                            ->default(['application_submitted', 'application_approved', 'asset_overdue']),
                    ]),

                Section::make(__('notification_preferences.security_section'))
                    ->description(__('notification_preferences.security_desc'))
                    ->schema([
                        Forms\Components\CheckboxList::make('security_notifications')
                            ->label(__('notification_preferences.notification_types'))
                            ->options([
                                'security_incidents' => __('notification_preferences.security_incidents'),
                                'failed_logins' => __('notification_preferences.failed_logins'),
                                'role_changes' => __('notification_preferences.role_changes'),
                                'config_changes' => __('notification_preferences.config_changes'),
                                'suspicious_activity' => __('notification_preferences.suspicious_activity'),
                                'audit_alerts' => __('notification_preferences.audit_alerts'),
                            ])
                            ->columns(2)
                            ->default(['security_incidents', 'failed_logins', 'role_changes']),
                    ]),

                Section::make(__('notification_preferences.system_section'))
                    ->description(__('notification_preferences.system_desc'))
                    ->schema([
                        Forms\Components\CheckboxList::make('system_notifications')
                            ->label(__('notification_preferences.notification_types'))
                            ->options([
                                'maintenance_alerts' => __('notification_preferences.maintenance_alerts'),
                                'performance_alerts' => __('notification_preferences.performance_alerts'),
                                'backup_status' => __('notification_preferences.backup_status'),
                                'update_notifications' => __('notification_preferences.update_notifications'),
                                'integration_alerts' => __('notification_preferences.integration_alerts'),
                                'queue_alerts' => __('notification_preferences.queue_alerts'),
                            ])
                            ->columns(2)
                            ->default(['maintenance_alerts', 'update_notifications']),
                    ]),

                Section::make(__('notification_preferences.frequency_section'))
                    ->description(__('notification_preferences.frequency_desc'))
                    ->schema([
                        Forms\Components\Select::make('digest_frequency')
                            ->label(__('notification_preferences.digest_frequency'))
                            ->helperText(__('notification_preferences.choose_how_receive'))
                            ->options([
                                'immediate' => __('notification_preferences.digest_immediate'),
                                'hourly' => __('notification_preferences.digest_hourly'),
                                'daily' => __('notification_preferences.digest_daily'),
                                'weekly' => __('notification_preferences.digest_weekly'),
                            ])
                            ->default('daily'),

                        Forms\Components\Toggle::make('quiet_hours_enabled')
                            ->label(__('notification_preferences.enable_quiet_hours'))
                            ->helperText(__('notification_preferences.enable_quiet_hours'))
                            ->reactive(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('quiet_hours_start')
                                    ->label(__('notification_preferences.quiet_hours_start'))
                                    ->default('22:00')
                                    ->visible(fn ($get) => $get('quiet_hours_enabled')),

                                Forms\Components\TimePicker::make('quiet_hours_end')
                                    ->label(__('notification_preferences.quiet_hours_end'))
                                    ->default('08:00')
                                    ->visible(fn ($get) => $get('quiet_hours_enabled')),
                            ]),

                        Forms\Components\Toggle::make('weekend_notifications')
                            ->label(__('notification_preferences.weekend_notifications'))
                            ->helperText(__('notification_preferences.weekend_notifications'))
                            ->default(false),
                    ])
                    ->columns(1),

                Section::make(__('notification_preferences.priority_section'))
                    ->description(__('notification_preferences.priority_desc'))
                    ->schema([
                        Forms\Components\Toggle::make('urgent_only_mode')
                            ->label(__('notification_preferences.urgent_only_mode'))
                            ->helperText(__('notification_preferences.urgent_only_mode'))
                            ->default(false),

                        Forms\Components\Select::make('priority_threshold')
                            ->label(__('notification_preferences.priority_threshold'))
                            ->helperText(__('notification_preferences.only_receive_notifications_at_or_above_this_priority_level'))
                            ->options([
                                'low' => __('notification_preferences.low_and_above'),
                                'medium' => __('notification_preferences.medium_and_above'),
                                'high' => __('notification_preferences.high_and_above'),
                                'urgent' => __('notification_preferences.urgent_only'),
                            ])
                            ->default('medium')
                            ->visible(fn ($get) => ! $get('urgent_only_mode')),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('notification_preferences.save_preferences'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),

            Action::make('reset_defaults')
                ->label(__('notification_preferences.reset_to_defaults'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('notification_preferences.reset_modal_heading'))
                ->modalDescription(__('notification_preferences.reset_modal_desc'))
                ->action('resetToDefaults'),

            Action::make('test_notifications')
                ->label(__('notification_preferences.test_notifications'))
                ->icon('heroicon-o-bell')
                ->color('info')
                ->action('sendTestNotifications'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'notification_preferences' => $data,
        ]);

        // Clear user's notification cache
        Cache::forget("user_notification_preferences_{$user->id}");

        \Filament\Notifications\Notification::make()
            ->title(__('notification_preferences.preferences_saved'))
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        $user = auth()->user();

        $defaultPreferences = [
            'email_notifications' => true,
            'in_app_notifications' => true,
            'sms_notifications' => false,
            'desktop_notifications' => true,
            'helpdesk_notifications' => [
                'ticket_assigned' => true,
                'ticket_status_changed' => true,
                'sla_breach' => true,
                'overdue_tickets' => true,
            ],
            'loan_notifications' => [
                'application_submitted' => true,
                'application_approved' => true,
                'application_rejected' => true,
                'asset_overdue' => true,
                'return_reminder' => true,
            ],
            'security_notifications' => [
                'security_incidents' => true,
                'failed_logins' => true,
                'role_changes' => true,
                'config_changes' => true,
            ],
            'system_notifications' => [
                'maintenance_alerts' => true,
                'performance_alerts' => false,
                'backup_status' => false,
                'update_notifications' => true,
            ],
            'digest_frequency' => 'daily',
            'quiet_hours_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
            'weekend_notifications' => false,
            'urgent_only_mode' => false,
            'priority_threshold' => 'medium',
        ];

        $user->update([
            'notification_preferences' => $defaultPreferences,
        ]);

        $this->form->fill($defaultPreferences);

        // Clear user's notification cache
        Cache::forget("user_notification_preferences_{$user->id}");

        \Filament\Notifications\Notification::make()
            ->title(__('notification_preferences.preferences_reset'))
            ->success()
            ->send();
    }

    public function sendTestNotifications(): void
    {
        $user = auth()->user();

        // Send test notifications based on enabled delivery methods
        $preferences = $user->notification_preferences ?? [];

        if ($preferences['email_notifications'] ?? true) {
            // Send test email notification
            $user->notify(new \App\Notifications\TestNotification('email'));
        }

        if ($preferences['in_app_notifications'] ?? true) {
            // Send test in-app notification
            $user->notify(new \App\Notifications\TestNotification('in_app'));
        }

        \Filament\Notifications\Notification::make()
            ->title(__('notification_preferences.test_notifications_sent'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'preferences' => $this->data,
        ];
    }
}
